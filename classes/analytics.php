<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_edqscore;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

/**
 * Assign / forum / quiz analytics queries, scoped to a set of student ids.
 *
 * Each method returns one "card" per activity: summary counts for the
 * collapsed view, plus a nested list of per-student (or per-discussion,
 * for forums) rows for the expanded detail.
 *
 * All figures are computed directly from the standard mod_assign,
 * mod_forum and mod_quiz tables — this plugin stores no activity data of
 * its own, only the grading-turnaround threshold configuration.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics {

    /**
     * One card per assignment: submitted/graded counts and an overdue
     * count for the summary, plus a ->students list (submission date,
     * graded-on date, grade, overdue) for the expanded detail.
     *
     * @param int $courseid
     * @param int[] $userids students in scope
     * @param int $thresholdhours
     * @param string $countfrom one of the LOCAL_EDQSCORE_COUNTFROM_* constants
     * @param int $latehours
     * @return \stdClass[]
     */
    public static function get_assignments(int $courseid, array $userids, int $thresholdhours,
            string $countfrom = LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, int $latehours = LOCAL_EDQSCORE_DEFAULT_LATE_HOURS): array {
        global $DB;

        $assignments = $DB->get_records('assign', ['course' => $courseid], 'duedate ASC, name ASC',
            'id, name, duedate, cutoffdate, grade');
        if (!$assignments) {
            return [];
        }

        $cms = $DB->get_records_sql("
                SELECT cm.instance, cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                 WHERE cm.course = :courseid AND m.name = 'assign'",
            ['courseid' => $courseid]);

        $total = count($userids);
        foreach ($assignments as $a) {
            $a->cmid = $cms[$a->id]->id ?? null;
            $a->isgraded = ((int) $a->grade) > 0;
            $a->totalstudents = $total;
            $a->submittedcount = 0;
            $a->gradedcount = 0;
            $a->overduecount = 0;
            $a->latecount = 0;
            $a->feedbackcount = 0;
            $a->students = [];
        }
        if (empty($userids)) {
            return array_values($assignments);
        }

        $students = $DB->get_records_list('user', 'id', $userids, 'lastname ASC, firstname ASC',
            'id, firstname, lastname');

        [$usql, $uparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'u');
        [$asql, $aparams] = $DB->get_in_or_equal(array_keys($assignments), SQL_PARAMS_NAMED, 'a');

        $submissionmap = [];
        $submissions = $DB->get_records_sql("
                SELECT s.id, s.assignment, s.userid, s.timemodified, s.status
                  FROM {assign_submission} s
                 WHERE s.assignment $asql AND s.latest = 1 AND s.userid $usql",
            array_merge($aparams, $uparams));
        foreach ($submissions as $s) {
            $submissionmap[$s->assignment . ':' . $s->userid] = $s;
        }

        // Order by attempt number so, if an assignment allows multiple
        // attempts, the highest-attempt grade is what survives in the map.
        $grademap = [];
        $grades = $DB->get_records_sql("
                SELECT g.id, g.assignment, g.userid, g.grade, g.timemodified, g.attemptnumber
                  FROM {assign_grades} g
                 WHERE g.assignment $asql AND g.userid $usql
              ORDER BY g.attemptnumber ASC",
            array_merge($aparams, $uparams));
        foreach ($grades as $g) {
            if ($g->grade === null || $g->grade < 0) {
                continue;
            }
            $grademap[$g->assignment . ':' . $g->userid] = $g;
        }

        $feedbackbygradeid = [];
        $gradeids = array_map(fn($g) => $g->id, $grademap);
        if ($gradeids) {
            [$gidsql, $gidparams] = $DB->get_in_or_equal($gradeids, SQL_PARAMS_NAMED, 'gid');
            $comments = $DB->get_records_sql("
                    SELECT id, grade, commenttext, commentformat
                      FROM {assignfeedback_comments}
                     WHERE grade $gidsql", $gidparams);
            foreach ($comments as $c) {
                if (!html_is_blank($c->commenttext)) {
                    $feedbackbygradeid[$c->grade] = $c;
                }
            }
        }

        $now = time();

        foreach ($assignments as $assignmentid => $a) {
            foreach ($students as $studentid => $student) {
                $key = $assignmentid . ':' . $studentid;
                $submission = $submissionmap[$key] ?? null;
                $grade = $grademap[$key] ?? null;

                $row = new \stdClass();
                $row->studentid = $studentid;
                $row->studentname = fullname($student);

                $submitted = $submission && $submission->status === 'submitted';
                $row->submissiondate = $submitted ? (int) $submission->timemodified : null;
                $row->gradedon = $grade ? (int) $grade->timemodified : null;
                $row->grade = $grade ? (float) $grade->grade : null;
                $comment = $grade ? ($feedbackbygradeid[$grade->id] ?? null) : null;
                $row->feedback = $comment->commenttext ?? null;
                $row->feedbackformat = $comment->commentformat ?? FORMAT_HTML;

                // Turnaround: how long grading actually took if it's done, or
                // how long it's been waiting if it isn't — either way,
                // banded into on time / late / overdue. Being graded caps
                // nothing — grade it after 6+ days and it's still "overdue",
                // just resolved. The clock's start point (the submission, or
                // the due date) depends on $countfrom.
                $row->latestatus = 'ontime';
                $row->overdue = false;
                if ($submitted) {
                    $anchor = local_edqscore_resolve_countfrom(
                        $countfrom,
                        (int) $a->duedate,
                        $row->submissiondate,
                        (int) $a->cutoffdate
                    );
                    if ($anchor !== null) {
                        $row->latestatus = local_edqscore_grading_status(
                            $row->gradedon,
                            $anchor,
                            $thresholdhours,
                            $latehours,
                            $now
                        );
                        $row->overdue = $row->latestatus !== 'ontime';
                    }
                }

                if ($submitted) {
                    $a->submittedcount++;
                    if ($row->feedback !== null) {
                        $a->feedbackcount++;
                    }
                }
                if ($grade) {
                    $a->gradedcount++;
                }
                if ($row->overdue) {
                    $a->overduecount++;
                }
                if ($row->latestatus === 'late') {
                    $a->latecount++;
                }

                $a->students[] = $row;
            }
        }

        return array_values($assignments);
    }

    /**
     * One card per forum: discussion/post/participation counts, instructor
     * reply count and last-reply time for the summary, plus a
     * ->discussionlist for the expanded detail.
     *
     * The model here: students start discussions, and the instructor is
     * the one who owes a reply — replies from other students don't count
     * as resolving anything, and only instructor posts that are genuinely
     * replying to a student-started discussion count toward
     * "instructor replies" (an instructor's own announcement thread isn't
     * a "reply" to anyone).
     *
     * Forums can also be graded (rated) in Moodle, independently of
     * whether anyone has replied — a graded forum needs an actual rating
     * on each student post (mdl_rating), not a reply. This method picks
     * the right definition of "overdue" per forum:
     * - Graded forum (forum.assessed != 0): a student post is overdue if
     *   it hasn't been rated within the threshold.
     * - Ungraded forum: a student-started discussion is overdue if the
     *   instructor hasn't replied to it within the threshold.
     *
     * @param int $courseid
     * @param int[] $userids students in scope
     * @param int[] $groupids groups in scope, empty means "all groups"
     * @param int $thresholdhours
     * @param string $countfrom one of the LOCAL_EDQSCORE_COUNTFROM_* constants
     * @param int $latehours
     * @return \stdClass[]
     */
    public static function get_forums(int $courseid, array $userids, array $groupids, int $thresholdhours,
            string $countfrom = LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, int $latehours = LOCAL_EDQSCORE_DEFAULT_LATE_HOURS): array {
        global $DB;

        $forums = $DB->get_records('forum', ['course' => $courseid], 'name ASC', 'id, name, assessed, duedate');
        if (!$forums) {
            return [];
        }

        $cms = $DB->get_records_sql("
                SELECT cm.instance, cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                 WHERE cm.course = :courseid AND m.name = 'forum'",
            ['courseid' => $courseid]);

        $total = count($userids);
        foreach ($forums as $f) {
            $f->cmid = $cms[$f->id]->id ?? null;
            $f->isgraded = ((int) $f->assessed) !== 0;
            $f->totalstudents = $total;
            $f->discussioncount = 0;
            $f->postcount = 0;
            $f->participatingcount = 0;
            $f->instructorreplies = 0;
            $f->lastinstructorreply = null;
            $f->overduecount = 0;
            $f->latecount = 0;
            $f->discussionlist = [];
        }

        $context = \context_course::instance($courseid);
        [$rsql, $rparams] = $DB->get_in_or_equal(['teacher', 'editingteacher'], SQL_PARAMS_NAMED, 'r');
        $instructorids = $DB->get_fieldset_sql("
                SELECT DISTINCT ra.userid
                  FROM {role_assignments} ra
                  JOIN {role} r ON r.id = ra.roleid
                 WHERE ra.contextid = :contextid AND r.shortname $rsql",
            array_merge(['contextid' => $context->id], $rparams));
        $instructorset = array_flip($instructorids);

        [$fsql, $fparams] = $DB->get_in_or_equal(array_keys($forums), SQL_PARAMS_NAMED, 'f');

        $groupfilter = '';
        $groupparams = [];
        if (!empty($groupids)) {
            [$gsql, $gparams] = $DB->get_in_or_equal($groupids, SQL_PARAMS_NAMED, 'g');
            $groupfilter = "AND (d.groupid = -1 OR d.groupid $gsql)";
            $groupparams = $gparams;
        }

        $discussions = $DB->get_records_sql("
                SELECT d.id, d.forum, d.name, d.groupid, d.userid AS starterid
                  FROM {forum_discussions} d
                 WHERE d.forum $fsql $groupfilter",
            array_merge($fparams, $groupparams));

        foreach ($discussions as $d) {
            if (isset($forums[$d->forum])) {
                $forums[$d->forum]->discussioncount++;
            }
        }

        if (!$discussions) {
            return array_values($forums);
        }

        // Ratings live against the forum's own module context, not the
        // post — collect one context id per graded forum up front. Keep the
        // earliest rating time per post so late-but-rated posts can be told
        // apart from posts still waiting to be rated.
        $ratingtimes = [];
        $gradedforumids = array_keys(array_filter($forums, fn($f) => $f->isgraded));
        if (!empty($gradedforumids)) {
            $modulecontextids = [];
            foreach ($gradedforumids as $fid) {
                if (!empty($cms[$fid])) {
                    $modulecontextids[] = \context_module::instance($cms[$fid]->id)->id;
                }
            }
            if (!empty($modulecontextids)) {
                [$mcsql, $mcparams] = $DB->get_in_or_equal($modulecontextids, SQL_PARAMS_NAMED, 'mc');
                $ratedrows = $DB->get_records_sql("
                        SELECT id, itemid, timecreated
                          FROM {rating}
                         WHERE component = 'mod_forum' AND ratingarea = 'post' AND contextid $mcsql",
                    $mcparams);
                foreach ($ratedrows as $r) {
                    if (!isset($ratingtimes[$r->itemid]) || $r->timecreated < $ratingtimes[$r->itemid]) {
                        $ratingtimes[$r->itemid] = (int) $r->timecreated;
                    }
                }
            }
        }

        [$dsql, $dparams] = $DB->get_in_or_equal(array_keys($discussions), SQL_PARAMS_NAMED, 'd');
        $allposts = $DB->get_records_sql("
                SELECT p.id, p.discussion, p.userid, p.created
                  FROM {forum_posts} p
                 WHERE p.discussion $dsql AND p.deleted = 0
              ORDER BY p.created ASC",
            $dparams);

        $studentset = array_flip($userids);
        $now = time();

        // Rank so the row-building loop can keep the *worst* status when a
        // discussion has multiple student posts (a graded discussion with
        // one rated-on-time post and one still-pending post is "pending"
        // for the discussion as a whole, not "on time").
        $statusrank = ['ontime' => 0, 'late' => 1, 'overdue' => 2];

        // Track the latest post per discussion, who has participated,
        // whether/when the instructor first replied to each student-started
        // discussion, and — for graded forums — the worst grading status
        // among its student posts.
        $lastpost = [];
        $participants = [];
        $discussionfirstpost = [];
        $discussionreplytime = [];
        $discussionanswered = [];
        $discussionstatus = [];
        foreach ($allposts as $p) {
            $discussion = $discussions[$p->discussion] ?? null;
            if (!$discussion || !isset($forums[$discussion->forum])) {
                continue;
            }
            $forumid = $discussion->forum;
            $startedbystudent = isset($studentset[$discussion->starterid]);

            $forums[$forumid]->postcount++;
            if (!isset($discussionfirstpost[$p->discussion])) {
                // The $allposts list is ordered ASC by created, so the first post
                // encountered per discussion is its opening post — the
                // natural anchor for "how long has this needed a reply".
                $discussionfirstpost[$p->discussion] = (int) $p->created;
            }

            if (isset($studentset[$p->userid])) {
                $participants[$forumid][$p->userid] = true;
                if ($forums[$forumid]->isgraded) {
                    $anchor = local_edqscore_resolve_countfrom($countfrom, (int) $forums[$forumid]->duedate, (int) $p->created);
                    if ($anchor !== null) {
                        $ratedon = $ratingtimes[$p->id] ?? null;
                        $status = local_edqscore_grading_status($ratedon, $anchor, $thresholdhours, $latehours, $now);
                        if (!isset($discussionstatus[$p->discussion])
                                || $statusrank[$status] > $statusrank[$discussionstatus[$p->discussion]]) {
                            $discussionstatus[$p->discussion] = $status;
                        }
                    }
                }
            } else if (isset($instructorset[$p->userid]) && $startedbystudent) {
                // Only counts as a "reply" when it's answering a
                // student-started discussion — an instructor's own
                // announcement thread isn't a reply to anyone.
                $forums[$forumid]->instructorreplies++;
                $discussionanswered[$p->discussion] = true;
                if (!isset($discussionreplytime[$p->discussion]) || $p->created < $discussionreplytime[$p->discussion]) {
                    $discussionreplytime[$p->discussion] = (int) $p->created;
                }
                if ($forums[$forumid]->lastinstructorreply === null || $p->created > $forums[$forumid]->lastinstructorreply) {
                    $forums[$forumid]->lastinstructorreply = (int) $p->created;
                }
            }
            $lastpost[$p->discussion] = $p;
        }

        $students = $DB->get_records_list('user', 'id', $userids, '', 'id, firstname, lastname');

        foreach ($discussions as $discussionid => $d) {
            $forumid = $d->forum;
            if (!isset($forums[$forumid])) {
                continue;
            }
            $f = $forums[$forumid];
            $p = $lastpost[$discussionid] ?? null;
            $startedbystudent = isset($studentset[$d->starterid]);

            $row = new \stdClass();
            $row->discussionid = $discussionid;
            $row->name = $d->name;
            $row->lastpostid = $p ? (int) $p->id : null;
            $row->lastposttime = $p ? (int) $p->created : null;
            $row->lastpostbystudent = $p && isset($studentset[$p->userid]);
            $row->lastpostname = null;
            if ($p && isset($studentset[$p->userid]) && isset($students[$p->userid])) {
                $row->lastpostname = fullname($students[$p->userid]);
            }

            $row->latestatus = 'ontime';

            if ($f->isgraded) {
                // Worst status among this discussion's student posts —
                // computed post-by-post up front, since each post has its
                // own anchor and (if rated) its own rating time.
                $row->latestatus = $discussionstatus[$discussionid] ?? 'ontime';
            } else if ($startedbystudent) {
                // Turnaround on the instructor's reply: how long it took if
                // they've replied, or how long it's been waiting if not —
                // anchored to the discussion's opening post. Other students
                // chiming in doesn't count as resolving it.
                $origin = $discussionfirstpost[$discussionid] ?? null;
                if ($origin !== null) {
                    $anchor = local_edqscore_resolve_countfrom($countfrom, (int) $f->duedate, $origin);
                    if ($anchor !== null) {
                        $replytime = $discussionreplytime[$discussionid] ?? null;
                        $row->latestatus = local_edqscore_grading_status($replytime, $anchor, $thresholdhours, $latehours, $now);
                    }
                }
            } else {
                // Instructor-started thread (e.g. an announcement) — there's
                // nothing "owed" here under this model.
                $row->latestatus = 'ontime';
            }

            $row->overdue = $row->latestatus !== 'ontime';
            if ($row->overdue) {
                $forums[$forumid]->overduecount++;
            }
            if ($row->latestatus === 'late') {
                $forums[$forumid]->latecount++;
            }

            $forums[$forumid]->discussionlist[] = $row;
        }

        foreach ($forums as $forumid => $f) {
            $f->participatingcount = isset($participants[$forumid]) ? count($participants[$forumid]) : 0;
        }

        return array_values($forums);
    }

    /**
     * One card per quiz: attempt/completion/average-score counts for the
     * summary, plus a ->students list (attempt count, best score, last
     * attempt time) for the expanded detail.
     *
     * @param int $courseid
     * @param int[] $userids students in scope
     * @param int $thresholdhours
     * @param string $countfrom one of the LOCAL_EDQSCORE_COUNTFROM_* constants
     * @param int $latehours
     * @return \stdClass[]
     */
    public static function get_quizzes(int $courseid, array $userids, int $thresholdhours = LOCAL_EDQSCORE_DEFAULT_QUIZ_HOURS,
            string $countfrom = LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, int $latehours = LOCAL_EDQSCORE_DEFAULT_LATE_HOURS): array {
        global $DB;

        $quizzes = $DB->get_records('quiz', ['course' => $courseid], 'name ASC', 'id, name, grade, timeclose');
        if (!$quizzes) {
            return [];
        }

        $cms = $DB->get_records_sql("
                SELECT cm.instance, cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                 WHERE cm.course = :courseid AND m.name = 'quiz'",
            ['courseid' => $courseid]);

        $total = count($userids);
        foreach ($quizzes as $q) {
            $q->cmid = $cms[$q->id]->id ?? null;
            $q->isgraded = ((int) $q->grade) > 0;
            $q->totalstudents = $total;
            $q->attemptcount = 0;
            $q->completedcount = 0;
            $q->gradedcount = 0;
            $q->overduecount = 0;
            $q->latecount = 0;
            $q->avgscorepct = null;
            $q->hasessay = false;
            $q->students = [];
        }

        // A quiz "has essay" (needs manual grading) if any of its slots currently
        // resolve to a question of type 'essay'. Follows the Moodle 4.0+ question
        // bank versioning scheme: slot -> question_references -> a specific (or
        // "always latest") version in question_bank_entries -> the actual question.
        [$qidsql, $qidparams] = $DB->get_in_or_equal(array_keys($quizzes), SQL_PARAMS_NAMED, 'qz');
        $essayquizzes = $DB->get_records_sql("
                SELECT DISTINCT qs.quizid
                  FROM {quiz_slots} qs
                  JOIN {question_references} qr ON qr.component = 'mod_quiz' AND qr.questionarea = 'slot'
                       AND qr.itemid = qs.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qr.questionbankentryid
                  JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                       AND (qr.version = qv.version OR (qr.version IS NULL AND qv.version = (
                           SELECT MAX(qv2.version) FROM {question_versions} qv2
                            WHERE qv2.questionbankentryid = qbe.id)))
                  JOIN {question} qn ON qn.id = qv.questionid AND qn.qtype = 'essay'
                 WHERE qs.quizid $qidsql", $qidparams);
        foreach ($essayquizzes as $row) {
            if (isset($quizzes[$row->quizid])) {
                $quizzes[$row->quizid]->hasessay = true;
            }
        }

        if (empty($userids)) {
            return array_values($quizzes);
        }

        $students = $DB->get_records_list('user', 'id', $userids, 'lastname ASC, firstname ASC',
            'id, firstname, lastname');

        [$usql, $uparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'u');
        [$qsql, $qparams] = $DB->get_in_or_equal(array_keys($quizzes), SQL_PARAMS_NAMED, 'q');

        // A student is "overridden" for a quiz if there's a user override for
        // them directly, or a group override for any group they're in.
        // Recordsets (not get_records_sql) because neither query's columns
        // are guaranteed unique per row, e.g. a group with overrides on two
        // quizzes joins to the same group-member row twice.
        $overridden = [];
        $useroverrides = $DB->get_recordset_sql("
                SELECT qo.quiz, qo.userid
                  FROM {quiz_overrides} qo
                 WHERE qo.quiz $qsql AND qo.userid IS NOT NULL AND qo.userid $usql",
            array_merge($qparams, $uparams));
        foreach ($useroverrides as $o) {
            $overridden[$o->quiz . ':' . $o->userid] = true;
        }
        $useroverrides->close();
        $groupoverrides = $DB->get_recordset_sql("
                SELECT qo.quiz, gm.userid
                  FROM {quiz_overrides} qo
                  JOIN {groups_members} gm ON gm.groupid = qo.groupid
                 WHERE qo.quiz $qsql AND qo.groupid IS NOT NULL AND gm.userid $usql",
            array_merge($qparams, $uparams));
        foreach ($groupoverrides as $o) {
            $overridden[$o->quiz . ':' . $o->userid] = true;
        }
        $groupoverrides->close();

        $attemptrows = $DB->get_records_sql("
                SELECT a.id, a.quiz, a.userid, a.timefinish, a.uniqueid
                  FROM {quiz_attempts} a
                 WHERE a.quiz $qsql AND a.state = :state AND a.userid $usql",
            array_merge($qparams, $uparams, ['state' => 'finished']));

        $attemptsbykey = [];
        foreach ($attemptrows as $row) {
            $key = $row->quiz . ':' . $row->userid;
            $attemptsbykey[$key]['count'] = ($attemptsbykey[$key]['count'] ?? 0) + 1;
            if (!isset($attemptsbykey[$key]['last']) || (int) $row->timefinish > $attemptsbykey[$key]['last']) {
                $attemptsbykey[$key]['last'] = (int) $row->timefinish;
                $attemptsbykey[$key]['lastattemptid'] = (int) $row->id;
                $attemptsbykey[$key]['lastuniqueid'] = (int) $row->uniqueid;
            }
            if (isset($quizzes[$row->quiz])) {
                $quizzes[$row->quiz]->attemptcount++;
            }
        }

        // For the last attempt of each key, work out whether its essay
        // question(s) — if any — still need manual grading, and when the
        // last one was actually marked (for turnaround measurement).
        $uniqueidtokey = [];
        foreach ($attemptsbykey as $key => $info) {
            if (!empty($info['lastuniqueid'])) {
                $uniqueidtokey[$info['lastuniqueid']] = $key;
            }
        }

        $essaygrading = [];
        if ($uniqueidtokey) {
            [$uuqsql, $uuqparams] = $DB->get_in_or_equal(array_keys($uniqueidtokey), SQL_PARAMS_NAMED, 'uu');
            $essayqas = $DB->get_records_sql("
                    SELECT qatt.id, qatt.questionusageid
                      FROM {question_attempts} qatt
                      JOIN {question} qn ON qn.id = qatt.questionid AND qn.qtype = 'essay'
                     WHERE qatt.questionusageid $uuqsql", $uuqparams);

            if ($essayqas) {
                [$qaidsql, $qaidparams] = $DB->get_in_or_equal(array_keys($essayqas), SQL_PARAMS_NAMED, 'qa');
                $steps = $DB->get_records_sql("
                        SELECT id, questionattemptid, sequencenumber, state, timecreated
                          FROM {question_attempt_steps}
                         WHERE questionattemptid $qaidsql
                      ORDER BY questionattemptid ASC, sequencenumber ASC", $qaidparams);

                $laststate = [];
                foreach ($steps as $s) {
                    // Rows arrive in ascending sequencenumber order per question
                    // attempt, so the last write wins and ends up holding the
                    // current (i.e. latest) state.
                    $laststate[$s->questionattemptid] = $s;
                }

                foreach ($essayqas as $qa) {
                    $key = $uniqueidtokey[$qa->questionusageid] ?? null;
                    if ($key === null) {
                        continue;
                    }
                    if (!isset($essaygrading[$key])) {
                        $essaygrading[$key] = (object) ['alldone' => true, 'gradedon' => null];
                    }
                    $st = $laststate[$qa->id] ?? null;
                    if ($st !== null && strpos($st->state, 'mangr') === 0) {
                        if ($essaygrading[$key]->gradedon === null || (int) $st->timecreated > $essaygrading[$key]->gradedon) {
                            $essaygrading[$key]->gradedon = (int) $st->timecreated;
                        }
                    } else {
                        // Needsgrading, or any other non-manually-graded state.
                        $essaygrading[$key]->alldone = false;
                    }
                }
            }
        }

        $gradesbykey = [];
        $grades = $DB->get_records_sql("
                SELECT g.id, g.quiz, g.userid, g.grade
                  FROM {quiz_grades} g
                 WHERE g.quiz $qsql AND g.userid $usql",
            array_merge($qparams, $uparams));
        foreach ($grades as $g) {
            $gradesbykey[$g->quiz . ':' . $g->userid] = $g;
        }

        $sumscorepct = [];
        $countscorepct = [];
        $now = time();

        foreach ($quizzes as $quizid => $q) {
            foreach ($students as $studentid => $student) {
                $key = $quizid . ':' . $studentid;
                $attemptinfo = $attemptsbykey[$key] ?? null;
                $grade = $gradesbykey[$key] ?? null;

                $row = new \stdClass();
                $row->studentid = $studentid;
                $row->studentname = fullname($student);
                $row->attemptcount = $attemptinfo['count'] ?? 0;
                $row->lastattempttime = $attemptinfo['last'] ?? null;
                $row->lastattemptid = $attemptinfo['lastattemptid'] ?? null;
                $row->scorepct = null;
                $row->needsmanualgrading = false;
                $row->gradedon = null;
                $row->latestatus = 'ontime';
                $row->overdue = false;
                $row->overridden = !empty($overridden[$quizid . ':' . $studentid]);

                if ($grade && $q->grade > 0) {
                    $row->scorepct = round(((float) $grade->grade / (float) $q->grade) * 100, 1);
                    $sumscorepct[$quizid] = ($sumscorepct[$quizid] ?? 0) + $row->scorepct;
                    $countscorepct[$quizid] = ($countscorepct[$quizid] ?? 0) + 1;
                }

                if ($row->attemptcount > 0) {
                    $q->completedcount++;

                    if ($q->hasessay) {
                        $einfo = $essaygrading[$key] ?? null;
                        $alldone = $einfo ? $einfo->alldone : false;
                        $row->needsmanualgrading = !$alldone;
                        $row->gradedon = $einfo->gradedon ?? null;
                        if ($alldone) {
                            $q->gradedcount++;
                        }
                        $anchor = local_edqscore_resolve_countfrom(
                            $countfrom,
                            (int) ($q->timeclose ?? 0),
                            (int) $row->lastattempttime
                        );
                        if ($anchor !== null) {
                            $row->latestatus = local_edqscore_grading_status(
                                $alldone ? $row->gradedon : null,
                                $anchor,
                                $thresholdhours,
                                $latehours,
                                $now
                            );
                            $row->overdue = $row->latestatus !== 'ontime';
                            if ($row->overdue) {
                                $q->overduecount++;
                            }
                            if ($row->latestatus === 'late') {
                                $q->latecount++;
                            }
                        }
                    } else {
                        // Fully auto-graded: the score is final the moment the
                        // attempt is submitted, no manual grading window applies.
                        $q->gradedcount++;
                    }
                }

                $q->students[] = $row;
            }
        }

        foreach ($quizzes as $quizid => $q) {
            if (!empty($countscorepct[$quizid])) {
                $q->avgscorepct = round($sumscorepct[$quizid] / $countscorepct[$quizid], 1);
            }
        }

        return array_values($quizzes);
    }

    /**
     * Assignment submissions from students in scope, submitted since a
     * given timestamp — a lightweight query for the daily submission
     * digest, deliberately not reusing get_assignments() since the digest
     * only needs who-submitted-what-when, not the full grading/turnaround
     * breakdown that method computes.
     *
     * @param int $courseid
     * @param int[] $userids students in scope
     * @param int $since only submissions at or after this timestamp
     * @return \stdClass[] each with studentid, firstname, lastname, assignid, assignname, cmid, timemodified
     */
    public static function get_recent_submissions(int $courseid, array $userids, int $since): array {
        global $DB;

        if (empty($userids)) {
            return [];
        }

        [$usql, $uparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'u');
        $sql = "SELECT s.id, s.userid AS studentid, s.timemodified,
                       u.firstname, u.lastname,
                       a.id AS assignid, a.name AS assignname, cm.id AS cmid
                  FROM {assign_submission} s
                  JOIN {assign} a ON a.id = s.assignment
                  JOIN {user} u ON u.id = s.userid
                  JOIN {course_modules} cm ON cm.instance = a.id
                  JOIN {modules} m ON m.id = cm.module AND m.name = 'assign'
                 WHERE a.course = :courseid AND s.status = 'submitted' AND s.latest = 1
                       AND s.timemodified >= :since AND s.userid $usql
              ORDER BY s.timemodified DESC";
        $params = array_merge($uparams, ['courseid' => $courseid, 'since' => $since]);

        return array_values($DB->get_records_sql($sql, $params));
    }
}

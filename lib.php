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

/**
 * Library functions for local_edqscore.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Default hours an assignment submission can go ungraded before it's flagged. */
define('LOCAL_EDQSCORE_DEFAULT_ASSIGN_HOURS', 48);
/** Default hours a forum post can go without an instructor reply/grade before it's flagged. */
define('LOCAL_EDQSCORE_DEFAULT_FORUM_HOURS', 72);
/** Default hours an essay-question quiz attempt can go unmarked before it's flagged. */
define('LOCAL_EDQSCORE_DEFAULT_QUIZ_HOURS', 48);

/** Grading-turnaround clock starts at the activity's due date. */
define('LOCAL_EDQSCORE_COUNTFROM_DUEDATE', 'duedate');
/** Grading-turnaround clock starts when the student actually submitted/posted. */
define('LOCAL_EDQSCORE_COUNTFROM_SUBMISSION', 'submission');
/** Due date if one is set, otherwise falls back to the submission/post date. */
define('LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK', 'duedate_fallback');
/** Grading-turnaround clock starts at the assignment's cut-off date (assignments only). */
define('LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE', 'cutoffdate');
/** Cut-off date if one is set, otherwise falls back to the due date (assignments only). */
define('LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK', 'cutoffdate_fallback');

/** EdQ credit given to an item that was eventually graded/replied to, but past the threshold — full credit is 1.0. */
define('LOCAL_EDQSCORE_EDQ_LATE_CREDIT', 0.5);

/** Default hours past which something eventually graded/replied to counts as merely "Late" rather than "Overdue". */
define('LOCAL_EDQSCORE_DEFAULT_LATE_HOURS', 120);

/**
 * Work out which students this user should see analytics for in a course,
 * and which groups they're allowed to filter down to via the group
 * dropdown on the board.
 *
 * Base visibility: every student, if the user can access all groups
 * (manager, Program Director, or a teacher explicitly granted
 * moodle/site:accessallgroups); otherwise only students who share a group
 * with this user. A teacher who teaches no groups in a groups-based
 * course sees nobody, matching the "instructor must be in a group to see
 * it" rule this mirrors.
 *
 * On top of that base visibility, $requestedgroupid (e.g. from the
 * dropdown) narrows the view to just that one group — but only if it's a
 * group the user is actually allowed to see; an invalid or unauthorised
 * value is silently ignored rather than widening access.
 *
 * @param int $courseid
 * @param int $userid
 * @param int $requestedgroupid 0 for no filter (see everything in scope)
 * @return array{
 *     scope: string,
 *     groupids: int[],
 *     userids: int[],
 *     selectablegroups: array<int, stdClass>,
 *     selectedgroupid: int
 * } scope is 'all', 'groups', or 'none'.
 */
function local_edqscore_get_teaching_scope(int $courseid, int $userid, int $requestedgroupid = 0): array {
    global $DB;

    $context = context_course::instance($courseid);

    $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
    $allstudents = $studentroleid
        ? array_keys(get_role_users($studentroleid, $context, false, 'u.id', 'u.id ASC'))
        : [];

    $accessall = has_capability('moodle/site:accessallgroups', $context, $userid);
    $allcoursegroups = groups_get_all_groups($courseid);

    // Which groups can this user choose from in the dropdown?
    if ($accessall) {
        $selectablegroups = $allcoursegroups;
    } else {
        $mygroups = groups_get_user_groups($courseid, $userid);
        $mygroupids = $mygroups[0] ?? [];
        $selectablegroups = array_intersect_key($allcoursegroups, array_flip($mygroupids));
    }

    // Base group restriction before any dropdown filter is applied.
    if ($accessall || empty($allcoursegroups)) {
        $basegroupids = [];
    } else if (!empty($selectablegroups)) {
        $basegroupids = array_keys($selectablegroups);
    } else {
        // Groups exist in this course, the user isn't in any of them, and
        // doesn't have access-all-groups: nothing is in scope.
        return [
            'scope' => 'none', 'groupids' => [], 'userids' => [],
            'selectablegroups' => [], 'selectedgroupid' => 0,
        ];
    }

    $effectivegroupids = $basegroupids;
    $selectedgroupid = 0;
    if ($requestedgroupid && isset($selectablegroups[$requestedgroupid])) {
        $effectivegroupids = [$requestedgroupid];
        $selectedgroupid = $requestedgroupid;
    }

    if (empty($effectivegroupids)) {
        $userids = $allstudents;
        $scope = 'all';
    } else {
        $groupmembers = [];
        foreach ($effectivegroupids as $groupid) {
            foreach (groups_get_members($groupid, 'u.id') as $member) {
                $groupmembers[$member->id] = true;
            }
        }
        $studentset = array_flip($allstudents);
        $userids = array_keys(array_intersect_key($groupmembers, $studentset));
        $scope = 'groups';
    }

    return [
        'scope' => $scope,
        'groupids' => $effectivegroupids,
        'userids' => $userids,
        'selectablegroups' => $selectablegroups,
        'selectedgroupid' => $selectedgroupid,
    ];
}

/**
 * A checkbox setting whose default is "on", read back correctly whether or
 * not it has ever been explicitly saved. get_config() returns false (not a
 * real config value) for a setting that's never been written, which would
 * otherwise be misread as "off" — this treats that specific case as "on".
 *
 * @param string $name
 * @return bool
 */
function local_edqscore_default_true(string $name): bool {
    $raw = get_config('local_edqscore', $name);
    return $raw === false ? true : (bool) $raw;
}

/**
 * Effective grading-turnaround thresholds and "count from" modes for a
 * course: the course-level override for each setting if one is set,
 * otherwise the site-wide default.
 *
 * "Only show submitted" only makes sense when assignments are tracked by
 * submission date — if the course counts from the due date instead, seeing
 * who *hasn't* submitted yet is the whole point, so this always forces
 * onlyshowsubmitted to false when assigncountfrom isn't 'submission',
 * regardless of what's stored (e.g. if someone switches count-from mode
 * after already turning this on).
 *
 * @param int $courseid
 * @return stdClass{forumgradinghours: int, assigngradinghours: int,
 *      assigncountfrom: string, forumcountfrom: string, onlyshowsubmitted: bool,
 *      edqscoredigestenabled: bool, submissiondigestenabled: bool}
 */
function local_edqscore_get_course_thresholds(int $courseid): stdClass {
    global $DB;

    $defaults = new stdClass();
    $defaults->forumgradinghours = (int) (get_config('local_edqscore', 'forumgradinghours') ?: LOCAL_EDQSCORE_DEFAULT_FORUM_HOURS);
    $defaults->assigngradinghours = (int) (get_config('local_edqscore', 'assigngradinghours')
        ?: LOCAL_EDQSCORE_DEFAULT_ASSIGN_HOURS);
    $defaults->assigncountfrom = get_config('local_edqscore', 'assigncountfrom') ?: LOCAL_EDQSCORE_COUNTFROM_SUBMISSION;
    $defaults->forumcountfrom = get_config('local_edqscore', 'forumcountfrom')
        ?: LOCAL_EDQSCORE_COUNTFROM_SUBMISSION;
    $defaults->onlyshowsubmitted = (bool) get_config('local_edqscore', 'onlyshowsubmitted');
    $defaults->quizgradinghours = (int) (get_config('local_edqscore', 'quizgradinghours') ?: LOCAL_EDQSCORE_DEFAULT_QUIZ_HOURS);
    $defaults->quizcountfrom = get_config('local_edqscore', 'quizcountfrom') ?: LOCAL_EDQSCORE_COUNTFROM_SUBMISSION;
    $defaults->quizonlymanual = local_edqscore_default_true('quizonlymanual');
    $defaults->edqincludefeedback = local_edqscore_default_true('edqincludefeedback');
    $defaults->edqincludequizmanual = local_edqscore_default_true('edqincludequizmanual');
    $defaults->assignlatehours = (int) (get_config('local_edqscore', 'assignlatehours') ?: LOCAL_EDQSCORE_DEFAULT_LATE_HOURS);
    $defaults->forumlatehours = (int) (get_config('local_edqscore', 'forumlatehours') ?: LOCAL_EDQSCORE_DEFAULT_LATE_HOURS);
    $defaults->quizlatehours = (int) (get_config('local_edqscore', 'quizlatehours') ?: LOCAL_EDQSCORE_DEFAULT_LATE_HOURS);
    $defaults->edqscoredigestenabled = local_edqscore_default_true('edqscoredigestenabled');
    $defaults->submissiondigestenabled = local_edqscore_default_true('submissiondigestenabled');

    $override = $DB->get_record('local_edqscore_course_settings', ['courseid' => $courseid]);
    if ($override) {
        if ($override->forumgradinghours !== null) {
            $defaults->forumgradinghours = (int) $override->forumgradinghours;
        }
        if ($override->assigngradinghours !== null) {
            $defaults->assigngradinghours = (int) $override->assigngradinghours;
        }
        if (!empty($override->assigncountfrom)) {
            $defaults->assigncountfrom = $override->assigncountfrom;
        }
        if (!empty($override->forumcountfrom)) {
            $defaults->forumcountfrom = $override->forumcountfrom;
        }
        if ($override->onlyshowsubmitted !== null) {
            $defaults->onlyshowsubmitted = (bool) $override->onlyshowsubmitted;
        }
        if ($override->quizgradinghours !== null) {
            $defaults->quizgradinghours = (int) $override->quizgradinghours;
        }
        if (!empty($override->quizcountfrom)) {
            $defaults->quizcountfrom = $override->quizcountfrom;
        }
        if ($override->quizonlymanual !== null) {
            $defaults->quizonlymanual = (bool) $override->quizonlymanual;
        }
        if ($override->edqincludefeedback !== null) {
            $defaults->edqincludefeedback = (bool) $override->edqincludefeedback;
        }
        if ($override->edqincludequizmanual !== null) {
            $defaults->edqincludequizmanual = (bool) $override->edqincludequizmanual;
        }
        if ($override->assignlatehours !== null) {
            $defaults->assignlatehours = (int) $override->assignlatehours;
        }
        if ($override->forumlatehours !== null) {
            $defaults->forumlatehours = (int) $override->forumlatehours;
        }
        if ($override->quizlatehours !== null) {
            $defaults->quizlatehours = (int) $override->quizlatehours;
        }
        if ($override->edqscoredigestenabled !== null) {
            $defaults->edqscoredigestenabled = (bool) $override->edqscoredigestenabled;
        }
        if ($override->submissiondigestenabled !== null) {
            $defaults->submissiondigestenabled = (bool) $override->submissiondigestenabled;
        }
    }

    if ($defaults->assigncountfrom !== LOCAL_EDQSCORE_COUNTFROM_SUBMISSION) {
        $defaults->onlyshowsubmitted = false;
    }

    return $defaults;
}

/**
 * Resolve the timestamp the grading-turnaround clock should count from,
 * given the configured mode.
 *
 * @param string $mode one of the LOCAL_EDQSCORE_COUNTFROM_* constants
 * @param int $duedate 0 if none is set
 * @param int $submissiondate when the student actually submitted/posted
 * @param int $cutoffdate 0 if none is set (assignments only)
 * @return int|null null means "no valid anchor" (e.g. mode is
 *      due-date-only and there's no due date) — never overdue in that case
 */
function local_edqscore_resolve_countfrom(string $mode, int $duedate, int $submissiondate, int $cutoffdate = 0): ?int {
    switch ($mode) {
        case LOCAL_EDQSCORE_COUNTFROM_DUEDATE:
            return $duedate > 0 ? $duedate : null;
        case LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK:
            return $duedate > 0 ? $duedate : $submissiondate;
        case LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE:
            return $cutoffdate > 0 ? $cutoffdate : null;
        case LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK:
            if ($cutoffdate > 0) {
                return $cutoffdate;
            }
            return $duedate > 0 ? $duedate : null;
        case LOCAL_EDQSCORE_COUNTFROM_SUBMISSION:
        default:
            return $submissiondate;
    }
}

/**
 * The three-tier grading-turnaround status: how long an item has taken to
 * get graded/replied to (or has been waiting, if it still hasn't), measured
 * from the anchor (due date or submission date, per the course's
 * count-from setting):
 *
 * - 'ontime': within the configurable on-time threshold (e.g. assignment/
 *    forum/quiz grading hours).
 * - 'late': past that threshold, but under the configurable "considered
 *    late" threshold — applies whether or not it's actually been done yet.
 * - 'overdue': past the "considered late" threshold. This applies even to
 *    something that HAS since been graded/replied to, if it took that long
 *    — being done doesn't cap it at "late" once it's that far gone.
 *
 * @param int|null $donetime when it was graded/replied to, or null if not yet
 * @param int $anchor the due/submission timestamp turnaround is measured from
 * @param int $ontimehours the on-time threshold, in hours (the existing grading-hours setting)
 * @param int $latehours the late-vs-overdue threshold, in hours (the "considered late" setting)
 * @param int|null $now defaults to time(), overridable for testing
 * @return string 'ontime'|'late'|'overdue'
 */
function local_edqscore_grading_status(?int $donetime, int $anchor, int $ontimehours, int $latehours, ?int $now = null): string {
    $now = $now ?? time();
    $elapsed = ($donetime ?? $now) - $anchor;
    if ($elapsed <= $ontimehours * HOURSECS) {
        return 'ontime';
    }
    if ($elapsed < $latehours * HOURSECS) {
        return 'late';
    }
    return 'overdue';
}

/**
 * Whether to show items with no grading configured at all (e.g. a general
 * discussion forum with forum.assessed = 0, or an assignment/quiz with no
 * grade) — as opposed to *ungraded submissions within a graded item*,
 * which is a different concept (see analytics::get_*() "overdue").
 *
 * The course-level override wins if one is set; otherwise falls back to
 * the site-wide default, which itself defaults to off (hidden) so a
 * fresh install starts uncluttered.
 *
 * @param int $courseid
 * @return bool
 */
function local_edqscore_show_ungraded(int $courseid): bool {
    global $DB;

    $override = $DB->get_record('local_edqscore_course_settings', ['courseid' => $courseid]);
    if ($override && $override->showungraded !== null) {
        return (bool) $override->showungraded;
    }

    return (bool) get_config('local_edqscore', 'showungraded');
}

/**
 * Human-readable label for the current scope, e.g. for the toolbar.
 *
 * @param array $scope as returned by local_edqscore_get_teaching_scope()
 * @return string
 */
function local_edqscore_scope_label(array $scope): string {
    if ($scope['scope'] === 'all') {
        return get_string('scopeall', 'local_edqscore');
    }
    if ($scope['scope'] === 'groups') {
        $groupnames = array_map('groups_get_group_name', $scope['groupids']);
        return get_string('scopegroups', 'local_edqscore', implode(', ', $groupnames));
    }
    return get_string('scopenone', 'local_edqscore');
}

/**
 * EdQ ("Education Quality"): the share of gradeable submissions, forum
 * replies, assignment feedback and manually graded quiz attempts that got
 * handled inside the grading-turnaround threshold. Weighted by raw counts
 * (not an average of percentages) so a course with lots of forum traffic
 * isn't skewed by a single assignment. Full credit for "on time", partial
 * credit for "late" (it got done, just slowly), zero credit for "overdue"
 * (still not done, or done so late it no longer counts as merely late).
 *
 * Shared by course.php (the ring) and edqmisses.php (the full breakdown),
 * so the two never drift apart.
 *
 * @param \stdClass[] $assignments as returned by analytics::get_assignments()
 * @param \stdClass[] $forums as returned by analytics::get_forums()
 * @param \stdClass[] $quizzes as returned by analytics::get_quizzes()
 * @param stdClass $thresholds as returned by local_edqscore_get_course_thresholds()
 * @return array{score: int, color: string, trackable: float, ontime: float, misses: stdClass[]}
 */
function local_edqscore_compute_edq(array $assignments, array $forums, array $quizzes, stdClass $thresholds): array {
    $edqincludefeedback = $thresholds->edqincludefeedback;
    $edqincludequizmanual = $thresholds->edqincludequizmanual;

    $edqtrackable = 0;
    $edqontime = 0;
    $edqmisses = [];

    foreach ($assignments as $a) {
        $edqtrackable += $a->submittedcount;
        $edqontime += ($a->submittedcount - $a->overduecount) + ($a->latecount * LOCAL_EDQSCORE_EDQ_LATE_CREDIT);

        // Feedback given is tracked separately from grading turnaround: only
        // submitted work is trackable — a student who never submitted isn't
        // held against the instructor for missing feedback.
        if ($edqincludefeedback) {
            $edqtrackable += $a->submittedcount;
            $edqontime += $a->feedbackcount;
        }

        $assignurl = $a->cmid ? new moodle_url('/mod/assign/view.php', ['id' => $a->cmid, 'action' => 'grading']) : null;
        foreach ($a->students as $s) {
            if ($s->submissiondate === null) {
                continue;
            }
            if ($s->latestatus !== 'ontime') {
                $edqmisses[] = (object) [
                    'module' => 'assign', 'accent' => '#1E7A34', 'status' => $s->latestatus,
                    'item' => format_string($a->name), 'student' => $s->studentname,
                    'reason' => get_string($s->grade === null ? 'edqmiss_notgraded' : 'edqmiss_gradedlate', 'local_edqscore'),
                    'url' => $assignurl,
                ];
            }
            if ($edqincludefeedback && $s->feedback === null) {
                $edqmisses[] = (object) [
                    'module' => 'assign', 'accent' => '#1E7A34', 'status' => null,
                    'item' => format_string($a->name), 'student' => $s->studentname,
                    'reason' => get_string('edqmiss_nofeedback', 'local_edqscore'),
                    'url' => $assignurl,
                ];
            }
        }
    }

    foreach ($forums as $f) {
        $edqtrackable += $f->discussioncount;
        $edqontime += ($f->discussioncount - $f->overduecount) + ($f->latecount * LOCAL_EDQSCORE_EDQ_LATE_CREDIT);

        foreach ($f->discussionlist as $d) {
            if ($d->latestatus !== 'ontime') {
                $edqmisses[] = (object) [
                    'module' => 'forum', 'accent' => '#158FD1', 'status' => $d->latestatus,
                    'item' => format_string($f->name) . ': ' . format_string($d->name), 'student' => $d->lastpostname,
                    'reason' => get_string('edqmiss_noreply', 'local_edqscore'),
                    'url' => new moodle_url('/mod/forum/discuss.php', ['d' => $d->discussionid]),
                ];
            }
        }
    }

    foreach ($quizzes as $q) {
        // Only quizzes with an essay component involve any manual grading turnaround;
        // fully auto-graded quizzes are scored the instant they're submitted.
        if ($q->hasessay && $edqincludequizmanual) {
            $edqtrackable += $q->completedcount;
            $edqontime += ($q->completedcount - $q->overduecount) + ($q->latecount * LOCAL_EDQSCORE_EDQ_LATE_CREDIT);

            foreach ($q->students as $s) {
                if ($s->attemptcount > 0 && $s->latestatus !== 'ontime') {
                    $reasonstring = $s->needsmanualgrading ? 'edqmiss_notgraded' : 'edqmiss_gradedlate';
                    $edqmisses[] = (object) [
                        'module' => 'quiz', 'accent' => '#FFC72C', 'status' => $s->latestatus,
                        'item' => format_string($q->name), 'student' => $s->studentname,
                        'reason' => get_string($reasonstring, 'local_edqscore'),
                        'url' => $s->lastattemptid
                            ? new moodle_url('/mod/quiz/review.php', ['attempt' => $s->lastattemptid])
                            : null,
                    ];
                }
            }
        }
    }

    $edqscore = $edqtrackable > 0 ? round(($edqontime / $edqtrackable) * 100) : 100;
    if ($edqscore >= 90) {
        $edqcolor = '#1E7A34';
    } else if ($edqscore >= 70) {
        $edqcolor = '#FFC72C';
    } else {
        $edqcolor = '#D50032';
    }

    return [
        'score' => $edqscore,
        'color' => $edqcolor,
        'trackable' => $edqtrackable,
        'ontime' => $edqontime,
        'misses' => $edqmisses,
    ];
}

/**
 * Compute one specific user's teaching scope and EdQ score for a course,
 * applying the same showungraded/quizonlymanual filtering as the
 * dashboard. This is the full pipeline course.php runs to render the
 * dashboard for the logged-in user; the EdQ Score digest task reuses it
 * unchanged (once per course per instructor) so the daily email/popup
 * summary can never drift from what the dashboard itself would show that
 * instructor.
 *
 * @param int $courseid
 * @param int $userid
 * @param int $requestedgroupid 0 for no filter (see local_edqscore_get_teaching_scope())
 * @return array{scope: array, thresholds: stdClass, assignments: array,
 *      forums: array, quizzes: array, edq: array}
 */
function local_edqscore_compute_edq_for_user(int $courseid, int $userid, int $requestedgroupid = 0): array {
    $scope = local_edqscore_get_teaching_scope($courseid, $userid, $requestedgroupid);
    $thresholds = local_edqscore_get_course_thresholds($courseid);

    $assignments = \local_edqscore\analytics::get_assignments(
        $courseid,
        $scope['userids'],
        $thresholds->assigngradinghours,
        $thresholds->assigncountfrom,
        $thresholds->assignlatehours
    );
    $forums = \local_edqscore\analytics::get_forums(
        $courseid,
        $scope['userids'],
        $scope['groupids'],
        $thresholds->forumgradinghours,
        $thresholds->forumcountfrom,
        $thresholds->forumlatehours
    );
    $quizzes = \local_edqscore\analytics::get_quizzes(
        $courseid,
        $scope['userids'],
        $thresholds->quizgradinghours,
        $thresholds->quizcountfrom,
        $thresholds->quizlatehours
    );

    if (!local_edqscore_show_ungraded($courseid)) {
        $assignments = array_values(array_filter($assignments, fn($a) => $a->isgraded));
        $forums = array_values(array_filter($forums, fn($f) => $f->isgraded));
        $quizzes = array_values(array_filter($quizzes, fn($q) => $q->isgraded));
    }
    if ($thresholds->quizonlymanual) {
        $quizzes = array_values(array_filter($quizzes, fn($q) => $q->hasessay));
    }

    return [
        'scope' => $scope,
        'thresholds' => $thresholds,
        'assignments' => $assignments,
        'forums' => $forums,
        'quizzes' => $quizzes,
        'edq' => local_edqscore_compute_edq($assignments, $forums, $quizzes, $thresholds),
    ];
}

/**
 * Add a "EdQ Score" link to the course navigation for users who can view
 * this course's analytics.
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function local_edqscore_extend_navigation_course($navigation, $course, $context) {
    global $USER;

    if ($course->id == SITEID) {
        return;
    }
    if (!has_capability('local/edqscore:view', $context, $USER->id)) {
        return;
    }

    $url = new moodle_url('/local/edqscore/course.php', ['id' => $course->id]);
    $node = navigation_node::create(
        get_string('pluginname', 'local_edqscore'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'local_edqscore',
        new pix_icon('i/report', '')
    );
    $navigation->add_node($node);
}

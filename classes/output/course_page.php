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

namespace local_edqscore\output;

/**
 * Renderable for the per-course EdQ Score dashboard: teacher EdQ score, a
 * completion-rate chart, and three clickable cards leading to the
 * Assignments, Forums and Quizzes detail pages.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_page implements \renderable, \templatable {

    /** @var \stdClass the course */
    protected $course;

    /** @var \stdClass the current user */
    protected $user;

    /** @var array teaching scope, as returned by local_edqscore_get_teaching_scope() */
    protected $scope;

    /** @var bool whether the current user can edit course settings */
    protected $cansettings;

    /** @var \moodle_url the current page, used as the toolbar's group-filter form target */
    protected $pageurl;

    /** @var \stdClass[] as returned by analytics::get_assignments() */
    protected $assignments;

    /** @var \stdClass[] as returned by analytics::get_forums() */
    protected $forums;

    /** @var \stdClass[] as returned by analytics::get_quizzes() */
    protected $quizzes;

    /** @var array EdQ result, as returned by local_edqscore_compute_edq() */
    protected $edq;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param \stdClass $user
     * @param array $scope
     * @param bool $cansettings
     * @param \moodle_url $pageurl
     * @param array $assignments
     * @param array $forums
     * @param array $quizzes
     * @param array $edq
     */
    public function __construct(
        \stdClass $course,
        \stdClass $user,
        array $scope,
        bool $cansettings,
        \moodle_url $pageurl,
        array $assignments,
        array $forums,
        array $quizzes,
        array $edq
    ) {
        $this->course = $course;
        $this->user = $user;
        $this->scope = $scope;
        $this->cansettings = $cansettings;
        $this->pageurl = $pageurl;
        $this->assignments = $assignments;
        $this->forums = $forums;
        $this->quizzes = $quizzes;
        $this->edq = $edq;
    }

    /**
     * Export context for use in the local_edqscore/course_page template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $groupparam = $this->scope['selectedgroupid'] ? ['groupid' => $this->scope['selectedgroupid']] : [];
        $courseid = $this->course->id;

        $assignoverdue = 0;
        $assigngradedpctsum = 0;
        foreach ($this->assignments as $a) {
            $assignoverdue += $a->overduecount;
            $assigngradedpctsum += $a->totalstudents > 0 ? ($a->gradedcount / $a->totalstudents * 100) : 0;
        }
        $assigngradedavg = count($this->assignments) > 0 ? round($assigngradedpctsum / count($this->assignments)) : 0;

        $forumoverdue = 0;
        $forumparticipationpctsum = 0;
        foreach ($this->forums as $f) {
            $forumoverdue += $f->overduecount;
            $forumparticipationpctsum += $f->totalstudents > 0 ? ($f->participatingcount / $f->totalstudents * 100) : 0;
        }
        $forumcount = count($this->forums);
        $forumparticipationavg = $forumcount > 0 ? round($forumparticipationpctsum / $forumcount) : 0;

        // Quiz completion is a student behaviour (they take the quiz), not an
        // instructor grading action — auto-graded quizzes score themselves the
        // instant a student submits, so there's nothing for the instructor to
        // do. The quiz bar therefore only reflects grading progress on the
        // manually graded (essay) quizzes, mirroring the assignment bar's
        // semantics, and never mixes in auto-graded completion.
        $manualquizzes = array_filter($this->quizzes, fn($q) => $q->hasessay);
        $quizoverdue = 0;
        $quizgradedpctsum = 0;
        foreach ($manualquizzes as $q) {
            $quizoverdue += $q->overduecount;
            $quizgradedpctsum += $q->totalstudents > 0 ? ($q->gradedcount / $q->totalstudents * 100) : 0;
        }
        $manualquizcount = count($manualquizzes);
        $quizgradedavg = $manualquizcount > 0 ? round($quizgradedpctsum / $manualquizcount) : 0;

        $totaloverdue = $assignoverdue + $forumoverdue + $quizoverdue;
        $totalactivities = count($this->assignments) + $forumcount + count($this->quizzes);

        $chart = null;
        if ($totalactivities > 0) {
            $chartobj = new \core\chart_bar();
            $chartobj->set_horizontal(true);
            $series = new \core\chart_series(
                get_string('chartcompletiontitle', 'local_edqscore'),
                [$assigngradedavg, $forumparticipationavg, $quizgradedavg]
            );
            $series->set_colors(['#1E7A34', '#158FD1', '#FFC72C']);
            $chartobj->add_series($series);
            $chartobj->set_labels([
                get_string('sectionassignments', 'local_edqscore'),
                get_string('sectionforums', 'local_edqscore'),
                get_string('sectionquizzes', 'local_edqscore'),
            ]);
            $yaxis = $chartobj->get_yaxis(0, true);
            $yaxis->set_max(100);
            $yaxis->set_min(0);
            $chart = $output->render($chartobj);
        }

        return [
            'courseurl' => (new \moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
            'coursefullname' => format_string($this->course->fullname),
            'userpicture' => $output->user_picture($this->user, ['size' => 56, 'class' => 'userpicture']),
            'username' => fullname($this->user),
            'toolbar' => helper::export_toolbar($this->pageurl, $courseid, $this->scope, $this->cansettings),
            'edqdesc' => get_string('edqdesc', 'local_edqscore'),
            'edqmisseslabel' => get_string('edqmisses', 'local_edqscore'),
            'edqmissesurl' => (new \moodle_url('/local/edqscore/edqmisses.php', ['id' => $courseid] + $groupparam))->out(false),
            'edqmissescount' => count($this->edq['misses']),
            'hasedqmisses' => !empty($this->edq['misses']),
            'edqlabel' => get_string('edq', 'local_edqscore'),
            'edqscore' => $this->edq['score'],
            'edqcolor' => $this->edq['color'],
            'scopenone' => $this->scope['scope'] === 'none',
            'scopenonemessage' => get_string('scopenone', 'local_edqscore'),
            'studentcount' => count($this->scope['userids']),
            'studentslabel' => get_string('students', 'local_edqscore'),
            'totalactivities' => $totalactivities,
            'activitieslabel' => get_string('kpiactivities', 'local_edqscore'),
            'totaloverdue' => $totaloverdue,
            'hasoverdue' => $totaloverdue > 0,
            'overduelabel' => get_string('kpioverdue', 'local_edqscore'),
            'haschart' => $chart !== null,
            'charttitle' => get_string('chartcompletiontitle', 'local_edqscore'),
            'chart' => $chart,
            'assignurl' => (new \moodle_url('/local/edqscore/assignments.php', ['id' => $courseid] + $groupparam))->out(false),
            'assigntitle' => get_string('sectionassignments', 'local_edqscore'),
            'assigncount' => count($this->assignments),
            'assigngradedavg' => $assigngradedavg,
            'assignoverdue' => $assignoverdue,
            'hasassignoverdue' => $assignoverdue > 0,
            'forumurl' => (new \moodle_url('/local/edqscore/forums.php', ['id' => $courseid] + $groupparam))->out(false),
            'forumtitle' => get_string('sectionforums', 'local_edqscore'),
            'forumcount' => $forumcount,
            'forumparticipationavg' => $forumparticipationavg,
            'forumoverdue' => $forumoverdue,
            'hasforumoverdue' => $forumoverdue > 0,
            'quizurl' => (new \moodle_url('/local/edqscore/quizzes.php', ['id' => $courseid] + $groupparam))->out(false),
            'quiztitle' => get_string('sectionquizzes', 'local_edqscore'),
            'quizcount' => count($this->quizzes),
            'quizgradedavg' => $quizgradedavg,
            'quizoverdue' => $quizoverdue,
            'hasquizoverdue' => $quizoverdue > 0,
            'gradedlabel' => strtolower(get_string('countgraded', 'local_edqscore')),
            'participationlabel' => strtolower(get_string('colparticipation', 'local_edqscore')),
            'overduechiplabel' => get_string('coloverdue', 'local_edqscore'),
            'viewdetailslabel' => get_string('viewdetails', 'local_edqscore'),
        ];
    }
}

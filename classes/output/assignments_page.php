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
 * Renderable for the Assignments detail page.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignments_page implements \renderable, \templatable {

    /** @var \stdClass the course */
    protected $course;

    /** @var array teaching scope, as returned by local_edqscore_get_teaching_scope() */
    protected $scope;

    /** @var bool whether the current user can edit course settings */
    protected $cansettings;

    /** @var \moodle_url the current page, used as the toolbar's group-filter form target */
    protected $pageurl;

    /** @var \stdClass[] as returned by analytics::get_assignments() */
    protected $assignments;

    /** @var \stdClass course-turnaround thresholds, as returned by local_edqscore_get_course_thresholds() */
    protected $thresholds;

    /** @var \context_course */
    protected $context;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param array $scope
     * @param bool $cansettings
     * @param \moodle_url $pageurl
     * @param array $assignments
     * @param \stdClass $thresholds
     * @param \context_course $context
     */
    public function __construct(
        \stdClass $course,
        array $scope,
        bool $cansettings,
        \moodle_url $pageurl,
        array $assignments,
        \stdClass $thresholds,
        \context_course $context
    ) {
        $this->course = $course;
        $this->scope = $scope;
        $this->cansettings = $cansettings;
        $this->pageurl = $pageurl;
        $this->assignments = $assignments;
        $this->thresholds = $thresholds;
        $this->context = $context;
    }

    /**
     * Export context for use in the local_edqscore/assignments_page template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $courseid = $this->course->id;
        $dateformat = get_string('strftimedatetimeshort', 'langconfig');

        $assignments = [];
        foreach ($this->assignments as $a) {
            $assignurl = $a->cmid ? (new \moodle_url('/mod/assign/view.php', ['id' => $a->cmid]))->out(false) : null;
            $gradedpct = $a->totalstudents > 0 ? round(($a->gradedcount / $a->totalstudents) * 100) : 0;

            $displaystudents = $this->thresholds->onlyshowsubmitted
                ? array_filter($a->students, fn($s) => $s->submissiondate !== null)
                : $a->students;

            $students = [];
            foreach ($displaystudents as $s) {
                $gradeurl = $a->cmid ? (new \moodle_url('/mod/assign/view.php', [
                    'id' => $a->cmid, 'action' => 'grader', 'userid' => $s->studentid,
                ]))->out(false) : null;

                $row = [
                    'studentname' => $s->studentname,
                    'gradeurl' => $gradeurl,
                    'submissiondate' => $s->submissiondate ? userdate($s->submissiondate, $dateformat) : null,
                    'gradedon' => $s->gradedon ? userdate($s->gradedon, $dateformat) : null,
                    'grade' => $s->grade !== null ? ($s->grade . ' / ' . (int) $a->grade) : null,
                    'status' => helper::export_status_pill($s->latestatus),
                    'hasfeedback' => $s->feedback !== null,
                    'feedbackid' => null,
                    'feedbacktext' => null,
                    'feedbackshowlabel' => get_string('feedbackshow', 'local_edqscore'),
                    'feedbacknolabel' => get_string('feedbackno', 'local_edqscore'),
                ];
                if ($s->feedback !== null) {
                    $row['feedbackid'] = 'mygama-feedback-' . $a->id . '-' . $s->studentid;
                    $row['feedbacktext'] = format_text($s->feedback, $s->feedbackformat, ['context' => $this->context]);
                }
                $students[] = $row;
            }

            $assignments[] = [
                'collapseid' => 'mygama-assign-' . $a->id,
                'name' => format_string($a->name),
                'url' => $assignurl,
                'duedate' => $a->duedate ? userdate($a->duedate, $dateformat) : null,
                'gradedpct' => $gradedpct,
                'submittedcount' => $a->submittedcount,
                'totalstudents' => $a->totalstudents,
                'hasoverdue' => $a->overduecount > 0,
                'overduecount' => $a->overduecount,
                'noneinscope' => empty($a->students),
                'nosubmittedwork' => !empty($a->students) && empty($displaystudents),
                'students' => $students,
            ];
        }

        return [
            'backurl' => (new \moodle_url('/local/edqscore/course.php', ['id' => $courseid]))->out(false),
            'pluginname' => get_string('pluginname', 'local_edqscore'),
            'toolbar' => helper::export_toolbar($this->pageurl, $courseid, $this->scope, $this->cansettings),
            'scopenone' => $this->scope['scope'] === 'none',
            'scopenonemessage' => get_string('scopenone', 'local_edqscore'),
            'sectiontitle' => get_string('sectionassignments', 'local_edqscore'),
            'sectioncount' => count($this->assignments),
            'hasassignments' => !empty($this->assignments),
            'noactivitiesmessage' => get_string(
                'noactivities',
                'local_edqscore',
                get_string('sectionassignments', 'local_edqscore')
            ),
            'assignments' => $assignments,
            'duelabel' => get_string('coldue', 'local_edqscore'),
            'gradedlabel' => get_string('countgraded', 'local_edqscore'),
            'submittedlabel' => get_string('countsubmitted', 'local_edqscore'),
            'overduechiplabel' => get_string('coloverdue', 'local_edqscore'),
            'allcaughtuplabel' => get_string('allcaughtup', 'local_edqscore'),
            'colstudent' => get_string('colstudent', 'local_edqscore'),
            'colsubmissiondate' => get_string('colsubmissiondate', 'local_edqscore'),
            'colgradedon' => get_string('colgradedon', 'local_edqscore'),
            'colgrade' => get_string('colgrade', 'local_edqscore'),
            'coloverdue' => get_string('coloverdue', 'local_edqscore'),
            'colfeedback' => get_string('colfeedback', 'local_edqscore'),
            'noneinscopemessage' => get_string('noneinscope', 'local_edqscore'),
            'nosubmittedworkmessage' => get_string('nosubmittedwork', 'local_edqscore'),
        ];
    }
}

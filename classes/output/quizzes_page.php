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
 * Renderable for the Quizzes detail page.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizzes_page implements \renderable, \templatable {

    /** @var \stdClass the course */
    protected $course;

    /** @var array teaching scope, as returned by local_edqscore_get_teaching_scope() */
    protected $scope;

    /** @var bool whether the current user can edit course settings */
    protected $cansettings;

    /** @var \moodle_url the current page, used as the toolbar's group-filter form target */
    protected $pageurl;

    /** @var \stdClass[] as returned by analytics::get_quizzes() */
    protected $quizzes;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param array $scope
     * @param bool $cansettings
     * @param \moodle_url $pageurl
     * @param array $quizzes
     */
    public function __construct(\stdClass $course, array $scope, bool $cansettings, \moodle_url $pageurl, array $quizzes) {
        $this->course = $course;
        $this->scope = $scope;
        $this->cansettings = $cansettings;
        $this->pageurl = $pageurl;
        $this->quizzes = $quizzes;
    }

    /**
     * Export context for use in the local_edqscore/quizzes_page template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $courseid = $this->course->id;
        $dateformat = get_string('strftimedatetimeshort', 'langconfig');

        $quizzes = [];
        foreach ($this->quizzes as $q) {
            $quizurl = $q->cmid ? (new \moodle_url('/mod/quiz/view.php', ['id' => $q->cmid]))->out(false) : null;
            $completedpct = $q->totalstudents > 0 ? round(($q->completedcount / $q->totalstudents) * 100) : 0;
            $gradedpct = $q->totalstudents > 0 ? round(($q->gradedcount / $q->totalstudents) * 100) : 0;

            $students = [];
            foreach ($q->students as $s) {
                $reviewurl = $s->lastattemptid
                    ? (new \moodle_url('/mod/quiz/review.php', ['attempt' => $s->lastattemptid]))->out(false)
                    : null;

                $students[] = [
                    'studentname' => $s->studentname,
                    'reviewurl' => $reviewurl,
                    'attemptcount' => $s->attemptcount,
                    'lastattempttime' => $s->lastattempttime ? userdate($s->lastattempttime, $dateformat) : null,
                    'scorepct' => $s->scorepct !== null ? $s->scorepct . '%' : null,
                    'overridden' => $s->overridden,
                    'gradedon' => $s->gradedon ? userdate($s->gradedon, $dateformat) : null,
                    'hasattempt' => $s->attemptcount > 0,
                    'status' => helper::export_status_pill($s->latestatus),
                ];
            }

            $quizzes[] = [
                'collapseid' => 'mygama-quiz-' . $q->id,
                'name' => format_string($q->name),
                'url' => $quizurl,
                'hasessay' => $q->hasessay,
                'attemptcount' => $q->attemptcount,
                'completedpct' => $completedpct,
                'gradedpct' => $gradedpct,
                'avgscorepct' => $q->avgscorepct !== null ? $q->avgscorepct . '%' : null,
                'hasoverdue' => $q->overduecount > 0,
                'overduecount' => $q->overduecount,
                'colspan' => $q->hasessay ? 7 : 5,
                'noneinscope' => empty($q->students),
                'students' => $students,
            ];
        }

        return [
            'backurl' => (new \moodle_url('/local/edqscore/course.php', ['id' => $courseid]))->out(false),
            'pluginname' => get_string('pluginname', 'local_edqscore'),
            'toolbar' => helper::export_toolbar($this->pageurl, $courseid, $this->scope, $this->cansettings),
            'scopenone' => $this->scope['scope'] === 'none',
            'scopenonemessage' => get_string('scopenone', 'local_edqscore'),
            'sectiontitle' => get_string('sectionquizzes', 'local_edqscore'),
            'sectioncount' => count($this->quizzes),
            'hasquizzes' => !empty($this->quizzes),
            'noactivitiesmessage' => get_string('noactivities', 'local_edqscore', get_string('sectionquizzes', 'local_edqscore')),
            'quizzes' => $quizzes,
            'containsessaylabel' => get_string('containsessay', 'local_edqscore'),
            'autogradedlabel' => get_string('autograded', 'local_edqscore'),
            'attemptslabel' => get_string('colattempts', 'local_edqscore'),
            'gradedlabel' => get_string('countgraded', 'local_edqscore'),
            'completedlabel' => get_string('colcompleted', 'local_edqscore'),
            'avgscorelabel' => get_string('colavgscore', 'local_edqscore'),
            'overduechiplabel' => get_string('coloverdue', 'local_edqscore'),
            'allcaughtuplabel' => get_string('allcaughtup', 'local_edqscore'),
            'colstudent' => get_string('colstudent', 'local_edqscore'),
            'colattempts' => get_string('colattempts', 'local_edqscore'),
            'collastattempt' => get_string('collastattempt', 'local_edqscore'),
            'colscore' => get_string('colscore', 'local_edqscore'),
            'coloverridden' => get_string('coloverridden', 'local_edqscore'),
            'colgradedon' => get_string('colgradedon', 'local_edqscore'),
            'coloverdue' => get_string('coloverdue', 'local_edqscore'),
            'noneinscopemessage' => get_string('noneinscope', 'local_edqscore'),
            'overriddenyeslabel' => get_string('overriddenyes', 'local_edqscore'),
            'overriddennolabel' => get_string('overriddenno', 'local_edqscore'),
        ];
    }
}

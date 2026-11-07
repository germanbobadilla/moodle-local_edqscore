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
 * Per-course override of the EdQ Score grading-turnaround thresholds.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/edqscore/lib.php');

use local_edqscore\output\coursesettings_page;

$courseid = required_param('id', PARAM_INT);

require_login($courseid, false);
$course = get_course($courseid);
$context = context_course::instance($courseid);
require_capability('local/edqscore:configurecourse', $context);

$pageurl = new moodle_url('/local/edqscore/coursesettings.php', ['id' => $courseid]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('coursesettings', 'local_edqscore') . ': ' . format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cacheable(false);
$title = get_string('pluginname', 'local_edqscore');
$PAGE->navbar->add($title, new moodle_url('/local/edqscore/course.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('coursesettings', 'local_edqscore'));

$existing = $DB->get_record('local_edqscore_course_settings', ['courseid' => $courseid]);

if (data_submitted()) {
    require_sesskey();

    $assigncountfromraw = optional_param('assigncountfrom', '', PARAM_ALPHANUMEXT);
    $forumcountfromraw = optional_param('forumcountfrom', '', PARAM_ALPHANUMEXT);
    $quizcountfromraw = optional_param('quizcountfrom', '', PARAM_ALPHANUMEXT);

    $record = new stdClass();
    $record->courseid = $courseid;
    $record->forumgradinghours = local_edqscore_optional_hours('forumgradinghours');
    $record->assigngradinghours = local_edqscore_optional_hours('assigngradinghours');
    $record->showungraded = local_edqscore_optional_flag('showungraded');
    $record->assigncountfrom = ($assigncountfromraw === '') ? null : $assigncountfromraw;
    $record->forumcountfrom = ($forumcountfromraw === '') ? null : $forumcountfromraw;
    $record->onlyshowsubmitted = local_edqscore_optional_flag('onlyshowsubmitted');
    $record->quizgradinghours = local_edqscore_optional_hours('quizgradinghours');
    $record->quizcountfrom = ($quizcountfromraw === '') ? null : $quizcountfromraw;
    $record->quizonlymanual = local_edqscore_optional_flag('quizonlymanual');
    $record->edqincludefeedback = local_edqscore_optional_flag('edqincludefeedback');
    $record->edqincludequizmanual = local_edqscore_optional_flag('edqincludequizmanual');
    $record->assignlatehours = local_edqscore_optional_hours('assignlatehours');
    $record->forumlatehours = local_edqscore_optional_hours('forumlatehours');
    $record->quizlatehours = local_edqscore_optional_hours('quizlatehours');
    $record->edqscoredigestenabled = local_edqscore_optional_flag('edqscoredigestenabled');
    $record->submissiondigestenabled = local_edqscore_optional_flag('submissiondigestenabled');
    $record->timemodified = time();
    $record->usermodified = $USER->id;

    if ($existing) {
        $record->id = $existing->id;
        $DB->update_record('local_edqscore_course_settings', $record);
    } else {
        $DB->insert_record('local_edqscore_course_settings', $record);
    }

    redirect(
        new moodle_url('/local/edqscore/coursesettings.php', ['id' => $courseid]),
        get_string('settingssaved', 'local_edqscore'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$defaults = [
    'showungraded' => (bool) get_config('local_edqscore', 'showungraded'),
    'edqincludefeedback' => local_edqscore_default_true('edqincludefeedback'),
    'edqincludequizmanual' => local_edqscore_default_true('edqincludequizmanual'),
    'assigngradinghours' => (int) (get_config('local_edqscore', 'assigngradinghours') ?: LOCAL_EDQSCORE_DEFAULT_ASSIGN_HOURS),
    'assignlatehours' => (int) (get_config('local_edqscore', 'assignlatehours') ?: LOCAL_EDQSCORE_DEFAULT_LATE_HOURS),
    'assigncountfrom' => get_config('local_edqscore', 'assigncountfrom') ?: LOCAL_EDQSCORE_COUNTFROM_SUBMISSION,
    'onlyshowsubmitted' => (bool) get_config('local_edqscore', 'onlyshowsubmitted'),
    'forumgradinghours' => (int) (get_config('local_edqscore', 'forumgradinghours') ?: LOCAL_EDQSCORE_DEFAULT_FORUM_HOURS),
    'forumlatehours' => (int) (get_config('local_edqscore', 'forumlatehours') ?: LOCAL_EDQSCORE_DEFAULT_LATE_HOURS),
    'forumcountfrom' => get_config('local_edqscore', 'forumcountfrom') ?: LOCAL_EDQSCORE_COUNTFROM_SUBMISSION,
    'quizgradinghours' => (int) (get_config('local_edqscore', 'quizgradinghours') ?: LOCAL_EDQSCORE_DEFAULT_QUIZ_HOURS),
    'quizlatehours' => (int) (get_config('local_edqscore', 'quizlatehours') ?: LOCAL_EDQSCORE_DEFAULT_LATE_HOURS),
    'quizcountfrom' => get_config('local_edqscore', 'quizcountfrom') ?: LOCAL_EDQSCORE_COUNTFROM_SUBMISSION,
    'quizonlymanual' => local_edqscore_default_true('quizonlymanual'),
    'edqscoredigestenabled' => local_edqscore_default_true('edqscoredigestenabled'),
    'submissiondigestenabled' => local_edqscore_default_true('submissiondigestenabled'),
];

$renderable = new coursesettings_page($course, $pageurl, $existing, $defaults);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_edqscore/coursesettings_page', $renderable->export_for_template($OUTPUT));
echo $OUTPUT->footer();

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
 * EdQ Score dashboard for one course: teacher EdQ score, a completion-rate
 * chart, and three clickable cards leading to the Assignments, Forums and
 * Quizzes detail pages.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/edqscore/lib.php');

use local_edqscore\output\course_page;

$courseid = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT);

require_login($courseid, false);
$course = get_course($courseid);
$context = context_course::instance($courseid);
require_capability('local/edqscore:view', $context);

$pageurl = new moodle_url('/local/edqscore/course.php', ['id' => $courseid]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'local_edqscore') . ': ' . format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cacheable(false);
$PAGE->navbar->add(get_string('pluginname', 'local_edqscore'));

$result = local_edqscore_compute_edq_for_user($courseid, (int) $USER->id, $groupid);
$scope = $result['scope'];

$cansettings = has_capability('local/edqscore:configurecourse', $context);

$renderable = new course_page(
    $course,
    $USER,
    $scope,
    $cansettings,
    $pageurl,
    $result['assignments'],
    $result['forums'],
    $result['quizzes'],
    $result['edq']
);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_edqscore/course_page', $renderable->export_for_template($OUTPUT));
echo $OUTPUT->footer();

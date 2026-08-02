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
 * EdQ Score — Assignments detail page.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/edqscore/lib.php');
require_once($CFG->dirroot . '/local/edqscore/classes/analytics.php');

use local_edqscore\analytics;
use local_edqscore\output\assignments_page;

$courseid = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT);

require_login($courseid, false);
$course = get_course($courseid);
$context = context_course::instance($courseid);
require_capability('local/edqscore:view', $context);

$pageurl = new moodle_url('/local/edqscore/assignments.php', ['id' => $courseid]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('sectionassignments', 'local_edqscore') . ': ' . format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_cacheable(false);
$title = get_string('pluginname', 'local_edqscore');
$PAGE->navbar->add($title, new moodle_url('/local/edqscore/course.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('sectionassignments', 'local_edqscore'));

$scope = local_edqscore_get_teaching_scope($courseid, (int) $USER->id, $groupid);
$userids = $scope['userids'];
$thresholds = local_edqscore_get_course_thresholds($courseid);

$assignments = analytics::get_assignments(
    $courseid,
    $userids,
    $thresholds->assigngradinghours,
    $thresholds->assigncountfrom,
    $thresholds->assignlatehours
);
if (!local_edqscore_show_ungraded($courseid)) {
    $assignments = array_values(array_filter($assignments, fn($a) => $a->isgraded));
}

$cansettings = has_capability('local/edqscore:configurecourse', $context);

$renderable = new assignments_page($course, $scope, $cansettings, $pageurl, $assignments, $thresholds, $context);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_edqscore/assignments_page', $renderable->export_for_template($OUTPUT));
echo $OUTPUT->footer();

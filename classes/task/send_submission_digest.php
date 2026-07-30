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

namespace local_edqscore\task;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../lib.php');

/**
 * Daily scheduled task: for every course, tell every instructor with
 * local/edqscore:view which of their students (same group-visibility rule
 * as the dashboard) submitted an assignment in the last 24 hours.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_submission_digest extends \core\task\scheduled_task {

    /**
     * Task name shown in Site administration > Server > Scheduled tasks.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_submissiondigest', 'local_edqscore');
    }

    /**
     * Run the task: one notification per (course, instructor) pair that
     * has at least one qualifying submission in the lookback window.
     */
    public function execute() {
        global $DB;

        $since = time() - DAYSECS;
        $courses = $DB->get_recordset_select(
            'course',
            'id <> ? AND visible = 1',
            [SITEID],
            '',
            'id, fullname, shortname'
        );
        foreach ($courses as $course) {
            $this->send_for_course($course, $since);
        }
        $courses->close();
    }

    /**
     * Send the digest to every instructor of one course.
     *
     * @param \stdClass $course
     * @param int $since only submissions at or after this timestamp
     */
    protected function send_for_course(\stdClass $course, int $since): void {
        if (!local_edqscore_get_course_thresholds($course->id)->submissiondigestenabled) {
            return;
        }

        $context = \context_course::instance($course->id);
        $instructors = get_users_by_capability($context, 'local/edqscore:view');

        foreach ($instructors as $instructor) {
            $scope = local_edqscore_get_teaching_scope($course->id, (int) $instructor->id);
            if ($scope['scope'] === 'none') {
                continue;
            }
            $submissions = \local_edqscore\analytics::get_recent_submissions($course->id, $scope['userids'], $since);
            if (!$submissions) {
                continue;
            }
            $this->send_message($course, $instructor, $submissions);
        }
    }

    /**
     * Build and send a single instructor's digest for a single course.
     *
     * @param \stdClass $course
     * @param \stdClass $instructor
     * @param \stdClass[] $submissions as returned by analytics::get_recent_submissions()
     */
    protected function send_message(\stdClass $course, \stdClass $instructor, array $submissions): void {
        $coursename = format_string($course->fullname, true, ['context' => \context_course::instance($course->id)]);
        $count = count($submissions);
        $url = new \moodle_url('/local/edqscore/assignments.php', ['id' => $course->id]);
        $dateformat = get_string('strftimedatetimeshort', 'langconfig');

        $a = (object) ['course' => $coursename, 'count' => $count];

        $html = \html_writer::tag('p', get_string('digestsubmissions_intro', 'local_edqscore', $a));
        $items = [];
        foreach (array_slice($submissions, 0, 10) as $s) {
            $line = get_string('digestsubmissions_line', 'local_edqscore', (object) [
                'student' => fullname($s),
                'assignment' => format_string($s->assignname),
                'time' => userdate($s->timemodified, $dateformat),
            ]);
            $items[] = \html_writer::tag('li', $line);
        }
        $html .= \html_writer::tag('ul', implode('', $items));
        if ($count > 10) {
            $html .= \html_writer::tag('p', get_string('digest_more', 'local_edqscore', $count - 10));
        }
        $html .= \html_writer::link($url, get_string('sectionassignments', 'local_edqscore'));

        $message = new \core\message\message();
        $message->courseid = $course->id;
        $message->component = 'local_edqscore';
        $message->name = 'submissiondigest';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $instructor;
        $message->subject = get_string('digestsubmissions_subject', 'local_edqscore', $a);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessage = html_to_text($html);
        $message->fullmessagehtml = $html;
        $message->smallmessage = get_string('digestsubmissions_subject', 'local_edqscore', $a);
        $message->notification = 1;
        $message->contexturl = $url->out(false);
        $message->contexturlname = get_string('sectionassignments', 'local_edqscore');

        message_send($message);
    }
}

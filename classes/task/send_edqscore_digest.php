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
 * local/edqscore:view what their EdQ score currently is for that course
 * ("before clicking" into the plugin), scoped to exactly the students
 * they'd see on the dashboard itself.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_edqscore_digest extends \core\task\scheduled_task {

    /**
     * Task name shown in Site administration > Server > Scheduled tasks.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_edqscoredigest', 'local_edqscore');
    }

    /**
     * Run the task: one notification per (course, instructor) pair that
     * currently has any trackable activity and any students in scope.
     */
    public function execute() {
        global $DB;

        $courses = $DB->get_recordset_select(
            'course',
            'id <> ? AND visible = 1',
            [SITEID],
            '',
            'id, fullname, shortname'
        );
        foreach ($courses as $course) {
            $this->send_for_course($course);
        }
        $courses->close();
    }

    /**
     * Send the digest to every instructor of one course.
     *
     * @param \stdClass $course
     */
    protected function send_for_course(\stdClass $course): void {
        if (!local_edqscore_get_course_thresholds($course->id)->edqscoredigestenabled) {
            return;
        }

        $context = \context_course::instance($course->id);
        $instructors = get_users_by_capability($context, 'local/edqscore:view');

        foreach ($instructors as $instructor) {
            $result = local_edqscore_compute_edq_for_user($course->id, (int) $instructor->id);
            if ($result['scope']['scope'] === 'none' || $result['edq']['trackable'] <= 0) {
                continue;
            }
            $this->send_message($course, $instructor, $result['edq']);
        }
    }

    /**
     * Build and send a single instructor's digest for a single course.
     *
     * @param \stdClass $course
     * @param \stdClass $instructor
     * @param array $edq as returned by local_edqscore_compute_edq()
     */
    protected function send_message(\stdClass $course, \stdClass $instructor, array $edq): void {
        $coursename = format_string($course->fullname, true, ['context' => \context_course::instance($course->id)]);
        $misscount = count($edq['misses']);
        $url = new \moodle_url('/local/edqscore/edqmisses.php', ['id' => $course->id]);

        $subjecta = (object) ['course' => $coursename, 'score' => $edq['score']];
        $bodya = (object) ['course' => $coursename, 'score' => $edq['score'], 'misscount' => $misscount];

        $html = \html_writer::tag('p', get_string('digestedqscore_intro', 'local_edqscore', $bodya));
        if ($misscount > 0) {
            $items = [];
            foreach (array_slice($edq['misses'], 0, 5) as $miss) {
                $line = get_string('digestedqscore_missline', 'local_edqscore', (object) [
                    'student' => $miss->student,
                    'item' => $miss->item,
                    'reason' => $miss->reason,
                ]);
                $items[] = \html_writer::tag('li', $line);
            }
            $html .= \html_writer::tag('ul', implode('', $items));
            if ($misscount > 5) {
                $html .= \html_writer::tag('p', get_string('digest_more', 'local_edqscore', $misscount - 5));
            }
        }
        $html .= \html_writer::link($url, get_string('edqmisses', 'local_edqscore'));

        $message = new \core\message\message();
        $message->courseid = $course->id;
        $message->component = 'local_edqscore';
        $message->name = 'edqscoredigest';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $instructor;
        $message->subject = get_string('digestedqscore_subject', 'local_edqscore', $subjecta);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessage = html_to_text($html);
        $message->fullmessagehtml = $html;
        $message->smallmessage = get_string('digestedqscore_small', 'local_edqscore', $bodya);
        $message->notification = 1;
        $message->contexturl = $url->out(false);
        $message->contexturlname = get_string('edqmisses', 'local_edqscore');

        message_send($message);
    }
}

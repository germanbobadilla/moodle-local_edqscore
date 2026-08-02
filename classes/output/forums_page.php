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
 * Renderable for the Forums detail page.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forums_page implements \renderable, \templatable {

    /** @var \stdClass the course */
    protected $course;

    /** @var array teaching scope, as returned by local_edqscore_get_teaching_scope() */
    protected $scope;

    /** @var bool whether the current user can edit course settings */
    protected $cansettings;

    /** @var \moodle_url the current page, used as the toolbar's group-filter form target */
    protected $pageurl;

    /** @var \stdClass[] as returned by analytics::get_forums() */
    protected $forums;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param array $scope
     * @param bool $cansettings
     * @param \moodle_url $pageurl
     * @param array $forums
     */
    public function __construct(\stdClass $course, array $scope, bool $cansettings, \moodle_url $pageurl, array $forums) {
        $this->course = $course;
        $this->scope = $scope;
        $this->cansettings = $cansettings;
        $this->pageurl = $pageurl;
        $this->forums = $forums;
    }

    /**
     * Export context for use in the local_edqscore/forums_page template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $courseid = $this->course->id;
        $dateformat = get_string('strftimedatetimeshort', 'langconfig');

        $forums = [];
        foreach ($this->forums as $f) {
            $forumurl = $f->cmid ? (new \moodle_url('/mod/forum/view.php', ['id' => $f->cmid]))->out(false) : null;
            $participationpct = $f->totalstudents > 0 ? round(($f->participatingcount / $f->totalstudents) * 100) : 0;

            $discussions = [];
            foreach ($f->discussionlist as $d) {
                $discussurl = (new \moodle_url('/mod/forum/discuss.php', ['d' => $d->discussionid]))->out(false);
                $postanchor = $d->lastpostid ? ($discussurl . '#p' . $d->lastpostid) : $discussurl;

                $discussions[] = [
                    'name' => format_string($d->name),
                    'discussurl' => $discussurl,
                    'postanchor' => $postanchor,
                    'nopost' => $d->lastposttime === null,
                    'bystudent' => $d->lastposttime !== null && $d->lastpostbystudent,
                    'byinstructor' => $d->lastposttime !== null && !$d->lastpostbystudent,
                    'lastpostname' => $d->lastpostname,
                    'lastposttime' => $d->lastposttime ? userdate($d->lastposttime, $dateformat) : null,
                    'status' => helper::export_status_pill($d->latestatus),
                ];
            }

            $forums[] = [
                'collapseid' => 'mygama-forum-' . $f->id,
                'name' => format_string($f->name),
                'url' => $forumurl,
                'discussioncount' => $f->discussioncount,
                'postcount' => $f->postcount,
                'participationpct' => $participationpct,
                'instructorreplies' => $f->instructorreplies,
                'hasoverdue' => $f->overduecount > 0,
                'overduecount' => $f->overduecount,
                'nodiscussions' => empty($discussions),
                'discussions' => $discussions,
            ];
        }

        return [
            'backurl' => (new \moodle_url('/local/edqscore/course.php', ['id' => $courseid]))->out(false),
            'pluginname' => get_string('pluginname', 'local_edqscore'),
            'toolbar' => helper::export_toolbar($this->pageurl, $courseid, $this->scope, $this->cansettings),
            'scopenone' => $this->scope['scope'] === 'none',
            'scopenonemessage' => get_string('scopenone', 'local_edqscore'),
            'sectiontitle' => get_string('sectionforums', 'local_edqscore'),
            'sectioncount' => count($this->forums),
            'hasforums' => !empty($this->forums),
            'noactivitiesmessage' => get_string('noactivities', 'local_edqscore', get_string('sectionforums', 'local_edqscore')),
            'forums' => $forums,
            'discussionslabel' => get_string('coldiscussions', 'local_edqscore'),
            'postslabel' => get_string('colposts', 'local_edqscore'),
            'participationlabel' => get_string('colparticipation', 'local_edqscore'),
            'instructorreplieslabel' => get_string('colinstructorreplies', 'local_edqscore'),
            'overduechiplabel' => get_string('coloverdue', 'local_edqscore'),
            'allcaughtuplabel' => get_string('allcaughtup', 'local_edqscore'),
            'coldiscussionname' => get_string('coldiscussionname', 'local_edqscore'),
            'collastpost' => get_string('collastpost', 'local_edqscore'),
            'colwhen' => get_string('colwhen', 'local_edqscore'),
            'coloverdue' => get_string('coloverdue', 'local_edqscore'),
            'nopostslabel' => get_string('noposts', 'local_edqscore'),
            'studentslabel' => get_string('students', 'local_edqscore'),
            'instructorlabel' => get_string('instructor', 'local_edqscore'),
            'nodiscussionsmessage' => get_string('nodiscussions', 'local_edqscore'),
        ];
    }
}

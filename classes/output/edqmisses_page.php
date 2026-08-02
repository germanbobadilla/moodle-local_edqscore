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
 * Renderable for the "What's affecting your EdQ score" page.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edqmisses_page implements \renderable, \templatable {

    /** @var \stdClass the course */
    protected $course;

    /** @var array teaching scope, as returned by local_edqscore_get_teaching_scope() */
    protected $scope;

    /** @var bool whether the current user can edit course settings */
    protected $cansettings;

    /** @var \moodle_url the current page, used as the toolbar's group-filter form target */
    protected $pageurl;

    /** @var array EdQ result, as returned by local_edqscore_compute_edq() */
    protected $edq;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param array $scope
     * @param bool $cansettings
     * @param \moodle_url $pageurl
     * @param array $edq
     */
    public function __construct(\stdClass $course, array $scope, bool $cansettings, \moodle_url $pageurl, array $edq) {
        $this->course = $course;
        $this->scope = $scope;
        $this->cansettings = $cansettings;
        $this->pageurl = $pageurl;
        $this->edq = $edq;
    }

    /**
     * Export context for use in the local_edqscore/edqmisses_page template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $misses = [];
        foreach ($this->edq['misses'] as $miss) {
            $row = [
                'item' => $miss->item,
                'url' => $miss->url ? $miss->url->out(false) : null,
                'student' => $miss->student,
                'reason' => $miss->reason,
                'accent' => $miss->accent,
                'status' => null,
            ];
            if ($miss->status) {
                $row['status'] = helper::export_status_pill($miss->status);
            }
            $misses[] = $row;
        }

        return [
            'backurl' => (new \moodle_url('/local/edqscore/course.php', ['id' => $this->course->id]))->out(false),
            'pluginname' => get_string('pluginname', 'local_edqscore'),
            'toolbar' => helper::export_toolbar($this->pageurl, $this->course->id, $this->scope, $this->cansettings),
            'scopenone' => $this->scope['scope'] === 'none',
            'scopenonemessage' => get_string('scopenone', 'local_edqscore'),
            'edqmissestitle' => get_string('edqmisses', 'local_edqscore'),
            'edqdesc' => get_string('edqdesc', 'local_edqscore'),
            'edqlabel' => get_string('edq', 'local_edqscore'),
            'edqscore' => $this->edq['score'],
            'edqcolor' => $this->edq['color'],
            'misscount' => count($misses),
            'misses' => $misses,
            'hasmisses' => !empty($misses),
            'allontimemessage' => get_string('edqmissesall100', 'local_edqscore'),
        ];
    }
}

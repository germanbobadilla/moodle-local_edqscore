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
 * Data-export helpers shared by every My GAMA / EdQ Score page renderable:
 * the toolbar, the rich combo-select widget, and the on time/late/overdue
 * status pill. Kept as plain data shaping (no markup) so every page's
 * export_for_template() can reuse the same shape without duplicating logic.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Export the data needed by the templates/toolbar.mustache partial.
     *
     * @param \moodle_url $pageurl the current page, used as the group-filter form's action
     * @param int $courseid
     * @param array $scope as returned by local_edqscore_get_teaching_scope()
     * @param bool $cansettings whether to show the course-settings link
     * @return array
     */
    public static function export_toolbar(\moodle_url $pageurl, int $courseid, array $scope, bool $cansettings): array {
        $groups = [];
        foreach ($scope['selectablegroups'] as $group) {
            $groups[] = [
                'id' => $group->id,
                'name' => format_string($group->name),
                'selected' => (int) $scope['selectedgroupid'] === (int) $group->id,
            ];
        }

        return [
            'scopelabel' => local_edqscore_scope_label($scope),
            'studentcount' => count($scope['userids']),
            'studentslabel' => get_string('students', 'local_edqscore'),
            'pageurl' => $pageurl->out(false),
            'courseid' => $courseid,
            'hasgroups' => !empty($groups),
            'groups' => $groups,
            'allgroupsselected' => (int) $scope['selectedgroupid'] === 0,
            'allgroupslabel' => get_string('allgroups', 'local_edqscore'),
            'selectgrouplabel' => get_string('selectgroup', 'local_edqscore'),
            'golabel' => get_string('go', 'local_edqscore'),
            'cansettings' => $cansettings,
            'settingsurl' => (new \moodle_url('/local/edqscore/coursesettings.php', ['id' => $courseid]))->out(false),
            'coursesettingslabel' => get_string('coursesettings', 'local_edqscore'),
        ];
    }

    /**
     * Export the data needed by the templates/combo_select.mustache partial.
     *
     * @param string $name form field name, also used as the element id
     * @param array $options ordered list of ['value' => string, 'label' => string, 'desc' => string]
     * @param string $currentvalue currently selected value (may be '' for "use site default")
     * @return array
     */
    public static function export_combo(string $name, array $options, string $currentvalue): array {
        $currentlabel = '';
        $exported = [];
        foreach ($options as $opt) {
            $selected = $opt['value'] === $currentvalue;
            if ($selected) {
                $currentlabel = $opt['label'];
            }
            $exported[] = [
                'value' => $opt['value'],
                'label' => $opt['label'],
                'desc' => $opt['desc'] ?? '',
                'selected' => $selected,
            ];
        }

        return [
            'name' => $name,
            'currentlabel' => $currentlabel,
            'options' => $exported,
        ];
    }

    /**
     * Prepend the "use site default" option (with its own description) to a
     * list of real option value/label/desc triples, for export_combo().
     *
     * @param string $defaultlabel the label describing the current site default
     * @param array $baseoptions the real (non-default) option triples
     * @return array
     */
    public static function combo_options_with_default(string $defaultlabel, array $baseoptions): array {
        return array_merge([[
            'value' => '',
            'label' => get_string('usesitedefault', 'local_edqscore', $defaultlabel),
            'desc' => get_string('usesitedefault_desc', 'local_edqscore'),
        ]], $baseoptions);
    }

    /**
     * Export the label/CSS-class pair for an on time/late/overdue status,
     * for templates to render as a pill without needing to know the
     * underlying status strings themselves.
     *
     * @param string $status 'ontime'|'late'|'overdue'
     * @return array{label: string, pillclass: string}
     */
    public static function export_status_pill(string $status): array {
        switch ($status) {
            case 'late':
                return ['label' => get_string('statuslate', 'local_edqscore'), 'pillclass' => 'mygama-pill-warn'];
            case 'overdue':
                return ['label' => get_string('overdueyes', 'local_edqscore'), 'pillclass' => 'mygama-pill-bad'];
            default:
                return ['label' => get_string('overdueno', 'local_edqscore'), 'pillclass' => 'mygama-pill-good'];
        }
    }
}

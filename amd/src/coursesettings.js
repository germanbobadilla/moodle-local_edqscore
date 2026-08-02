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
 * Behaviour specific to the course settings page: the "only show submitted
 * work" disabled-note toggle (tied to the assignment count-from field), and
 * the expand-all/collapse-all button for the settings sections.
 *
 * @module     local_edqscore/coursesettings
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /**
     * Show/hide the "only show submitted work" note based on the current
     * assignment count-from selection.
     *
     * @param {string} siteDefault the site-wide default count-from mode
     * @param {string} submissionMode the value that means "count from submission date"
     */
    var initOnlyShowSubmittedToggle = function(siteDefault, submissionMode) {
        var assignSelect = document.getElementById('assigncountfrom');
        var note = document.getElementById('onlyshowsubmitted-disabled-note');
        if (!assignSelect || !note) {
            return;
        }

        var update = function() {
            var mode = assignSelect.value || siteDefault;
            var disable = (mode !== submissionMode);
            note.style.display = disable ? '' : 'none';
        };

        assignSelect.addEventListener('change', update);
    };

    /**
     * Wire up the expand-all/collapse-all button above the settings sections.
     *
     * @param {string} expandLabel button label when sections are collapsed
     * @param {string} collapseLabel button label when sections are expanded
     */
    var initExpandAll = function(expandLabel, collapseLabel) {
        var btn = document.getElementById('mygama-expandall');
        var headers = document.querySelectorAll('.mygama-settings-sectionhead');
        if (!btn || !headers.length) {
            return;
        }

        var allExpanded = function() {
            return Array.prototype.every.call(headers, function(h) {
                return h.getAttribute('aria-expanded') === 'true';
            });
        };

        var updateLabel = function() {
            btn.textContent = allExpanded() ? collapseLabel : expandLabel;
        };

        btn.addEventListener('click', function() {
            var expand = !allExpanded();
            headers.forEach(function(h) {
                var isOpen = h.getAttribute('aria-expanded') === 'true';
                if (expand !== isOpen) {
                    h.click();
                }
            });
        });

        // Bootstrap's collapse component fires these on every section, whether
        // toggled individually or via the button above — keep the label in
        // sync either way instead of guessing timing.
        document.addEventListener('shown.bs.collapse', updateLabel);
        document.addEventListener('hidden.bs.collapse', updateLabel);
        updateLabel();
    };

    return {
        init: function(siteDefault, submissionMode, expandLabel, collapseLabel) {
            initOnlyShowSubmittedToggle(siteDefault, submissionMode);
            initExpandAll(expandLabel, collapseLabel);
        }
    };
});

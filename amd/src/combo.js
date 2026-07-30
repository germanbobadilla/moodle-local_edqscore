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
 * Behaviour for the "rich dropdown" combo widget: a trigger button that
 * opens a panel listing every option with its own description and a
 * checkmark for the selected one. Backed by a plain <select> so the value
 * still posts like any other form field; clicking an option dispatches a
 * real 'change' event on that select so existing listeners keep working.
 *
 * @module     local_edqscore/combo
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /**
     * Close a single combo panel and reset its trigger's aria-expanded state.
     *
     * @param {Element} panel
     */
    var closePanel = function(panel) {
        panel.hidden = true;
        panel.closest('.mygama-combo').querySelector('.mygama-combo-trigger').setAttribute('aria-expanded', 'false');
    };

    /**
     * Wire up every .mygama-combo widget currently on the page.
     */
    var init = function() {
        document.querySelectorAll('.mygama-combo').forEach(function(combo) {
            var trigger = combo.querySelector('.mygama-combo-trigger');
            var panel = combo.querySelector('.mygama-combo-panel');
            var select = combo.querySelector('.mygama-combo-native');
            var triggerLabel = combo.querySelector('.mygama-combo-triggerlabel');

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                var opening = panel.hidden;
                document.querySelectorAll('.mygama-combo-panel').forEach(closePanel);
                if (opening) {
                    panel.hidden = false;
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });

            panel.querySelectorAll('.mygama-combo-option').forEach(function(opt) {
                opt.addEventListener('click', function() {
                    select.value = opt.getAttribute('data-value');
                    select.dispatchEvent(new Event('change', {bubbles: true}));

                    panel.querySelectorAll('.mygama-combo-option').forEach(function(o) {
                        o.classList.remove('mygama-combo-option-selected');
                        o.setAttribute('aria-selected', 'false');
                    });
                    opt.classList.add('mygama-combo-option-selected');
                    opt.setAttribute('aria-selected', 'true');
                    triggerLabel.textContent = opt.querySelector('.mygama-combo-optionlabel').textContent;
                    closePanel(panel);
                });
            });
        });

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.mygama-combo-panel').forEach(function(panel) {
                if (!panel.hidden && !panel.closest('.mygama-combo').contains(e.target)) {
                    closePanel(panel);
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.mygama-combo-panel').forEach(function(panel) {
                    if (!panel.hidden) {
                        closePanel(panel);
                    }
                });
            }
        });
    };

    return {
        init: init
    };
});

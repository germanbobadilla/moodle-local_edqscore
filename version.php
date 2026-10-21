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
 * EdQ Score teaching analytics — version information.
 *
 * Fully supported and tested on Moodle 4.5 LTS, 5.0, 5.2 and 5.3 LTS. $plugin->requires is
 * set to Moodle 4.0.0's base version, one notch lower than that supported range, so the
 * plugin is also downloadable and installable on 4.0 and 4.2 — the codebase doesn't need
 * anything newer than that floor:
 * - The question-bank schema the quiz essay-detection query reads (question_bank_entries,
 *   question_versions, question_references) was introduced in Moodle 4.0 itself (see
 *   question/upgrade.txt's "=== 4.0 ===" section) — this is the hard floor, nothing lower
 *   is possible.
 * - renderable/templatable + Mustache rendering, admin_settingpage::hide_if(), and
 *   \core\output\notification have all been stable since well before 4.0.
 * - The completion-rate card is a handful of plain CSS progress bars, not \core\chart_bar,
 *   so there's no Chart.js/AMD dependency to version-gate at all.
 * - The codebase avoids any PHP 8.0+ syntax (str_contains()/str_starts_with()/
 *   str_ends_with(), the nullsafe operator, match(), constructor property promotion,
 *   named arguments) as well as PHP 8.1+ syntax (enums, readonly properties), since 4.0's
 *   floor is PHP 7.3.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2026101700;
$plugin->requires  = 2022041900;
$plugin->component = 'local_edqscore';
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.5';

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
 * $plugin->requires is set to Moodle 4.5.0's base version so the plugin
 * installs on 4.5, 5.0, 5.1 and 5.2 — every API it relies on (renderable/
 * templatable + Mustache rendering, admin_settingpage::hide_if(),
 * \core\chart_bar, \core\output\notification, js_call_amd()) has been
 * stable since well before 4.5, and the codebase avoids PHP 8.2+-only
 * syntax (enums, readonly properties, match()) since 4.5's floor is PHP
 * 8.1 (5.0 requires 8.2, 5.2 requires 8.3 — all satisfied by 8.1-safe code).
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2026073003;
$plugin->requires  = 2024100700;
$plugin->component = 'local_edqscore';
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.4';

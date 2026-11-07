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
 * Supported and tested on Moodle 4.5 LTS to 5.3 LTS. Collapsible sections carry both the
 * Bootstrap 4 (data-toggle) and Bootstrap 5 (data-bs-toggle) attributes, because 4.5 uses
 * Bootstrap 4 and 5.0 onwards uses Bootstrap 5.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2026110701;
$plugin->requires  = 2024100700;
$plugin->supported = [405, 503];
$plugin->component = 'local_edqscore';
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.5.2';

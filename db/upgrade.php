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
 * Upgrade steps for local_edqscore.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade steps for local_edqscore.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_edqscore_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026072802) {
        $table = new xmldb_table('local_edqscore_course_settings');
        $field = new xmldb_field('showungraded', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'assigngradinghours');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072802, 'local', 'edqscore');
    }

    if ($oldversion < 2026072803) {
        $table = new xmldb_table('local_edqscore_course_settings');

        $field = new xmldb_field('assigncountfrom', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'showungraded');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('forumcountfrom', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'assigncountfrom');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072803, 'local', 'edqscore');
    }

    if ($oldversion < 2026072804) {
        $table = new xmldb_table('local_edqscore_course_settings');
        $field = new xmldb_field('onlyshowsubmitted', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'forumcountfrom');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072804, 'local', 'edqscore');
    }

    if ($oldversion < 2026072901) {
        $table = new xmldb_table('local_edqscore_course_settings');

        $field = new xmldb_field('quizgradinghours', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'onlyshowsubmitted');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('quizcountfrom', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'quizgradinghours');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072901, 'local', 'edqscore');
    }

    if ($oldversion < 2026072902) {
        $table = new xmldb_table('local_edqscore_course_settings');
        $field = new xmldb_field('quizonlymanual', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'quizcountfrom');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072902, 'local', 'edqscore');
    }

    if ($oldversion < 2026072904) {
        $table = new xmldb_table('local_edqscore_course_settings');

        $field = new xmldb_field('edqincludefeedback', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'quizonlymanual');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('edqincludequizmanual', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'edqincludefeedback');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072904, 'local', 'edqscore');
    }

    if ($oldversion < 2026072905) {
        $table = new xmldb_table('local_edqscore_course_settings');

        $field = new xmldb_field('assignlatehours', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'edqincludequizmanual');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('forumlatehours', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'assignlatehours');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('quizlatehours', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'forumlatehours');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026072905, 'local', 'edqscore');
    }

    if ($oldversion < 2026073003) {
        $table = new xmldb_table('local_edqscore_course_settings');

        $field = new xmldb_field('edqscoredigestenabled', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'quizlatehours');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field(
            'submissiondigestenabled',
            XMLDB_TYPE_INTEGER,
            '1',
            null,
            null,
            null,
            null,
            'edqscoredigestenabled'
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026073003, 'local', 'edqscore');
    }

    if ($oldversion < 2026073101) {
        // Editing teachers could change the course's own grading-turnaround
        // thresholds — the very numbers their EdQ score is measured
        // against. Course settings are now manager-only; drop the grant
        // this capability's archetype originally gave editing teachers.
        $editingteacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        if ($editingteacherrole) {
            unassign_capability('local/edqscore:configurecourse', $editingteacherrole->id);
        }

        upgrade_plugin_savepoint(true, 2026073101, 'local', 'edqscore');
    }

    return true;
}

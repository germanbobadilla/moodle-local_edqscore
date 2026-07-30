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
 * Install steps for local_edqscore.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Create a "Program Director" role that can manage EdQ Score settings and
 * view teaching analytics across groups, without granting full site
 * administration access.
 *
 * The role may already exist (e.g. carried over from a predecessor plugin
 * this one replaces) — in that case, reuse it rather than failing, but
 * still (re)assign the capabilities below unconditionally, since a reused
 * role won't yet have this plugin's own capability names on it.
 */
function xmldb_local_edqscore_install() {
    global $DB;

    // Core normally syncs db/access.php into the capabilities table *after*
    // this install callback runs, so the capabilities below don't exist yet
    // at this point unless we force the sync ourselves first.
    update_capabilities('local_edqscore');

    $shortname = 'programdirector';

    $role = $DB->get_record('role', ['shortname' => $shortname]);
    if ($role) {
        $roleid = $role->id;
    } else {
        $roledescription = 'Can view EdQ Score teaching analytics for all groups and manage its '
            . 'grading-turnaround settings, without full site administration access.';
        $roleid = create_role('Program Director', $shortname, $roledescription);
        set_role_contextlevels($roleid, [CONTEXT_SYSTEM, CONTEXT_COURSECAT, CONTEXT_COURSE]);
    }

    $systemcontext = context_system::instance();
    assign_capability('local/edqscore:view', CAP_ALLOW, $roleid, $systemcontext->id, true);
    assign_capability('local/edqscore:manage', CAP_ALLOW, $roleid, $systemcontext->id, true);
    assign_capability('local/edqscore:configurecourse', CAP_ALLOW, $roleid, $systemcontext->id, true);
    assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $roleid, $systemcontext->id, true);
    assign_capability('moodle/course:viewparticipants', CAP_ALLOW, $roleid, $systemcontext->id, true);
}

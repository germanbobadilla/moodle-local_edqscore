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
 * Site-wide default settings for local_edqscore.
 *
 * Deliberately gated on local/edqscore:manage rather than the default
 * moodle/site:config, so a role such as "Program Director" can be granted
 * access to these settings without full site administration rights. See
 * admin/tool/usertours/settings.php in core for the same pattern.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/edqscore/lib.php');

if ($hassiteconfig || has_capability('local/edqscore:manage', context_system::instance())) {
    $settings = new admin_settingpage('local_edqscore', get_string('pluginname', 'local_edqscore'), 'local/edqscore:manage');

    $settings->add(new admin_setting_heading('local_edqscore_settingsheading',
        get_string('settingsheading', 'local_edqscore'), get_string('settingsheading_desc', 'local_edqscore')));

    $settings->add(new admin_setting_heading('local_edqscore_settingsgroupgeneral',
        get_string('settingsgroupgeneral', 'local_edqscore'), ''));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/showungraded',
        get_string('showungraded', 'local_edqscore'), get_string('showungraded_desc', 'local_edqscore'), 0));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/edqincludefeedback',
        get_string('edqincludefeedback', 'local_edqscore'), get_string('edqincludefeedback_desc', 'local_edqscore'), 1));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/edqincludequizmanual',
        get_string('edqincludequizmanual', 'local_edqscore'), get_string('edqincludequizmanual_desc', 'local_edqscore'), 1));

    $countfromoptions = [
        LOCAL_EDQSCORE_COUNTFROM_SUBMISSION => get_string('countfrom_submission', 'local_edqscore'),
        LOCAL_EDQSCORE_COUNTFROM_DUEDATE => get_string('countfrom_duedate', 'local_edqscore'),
        LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK => get_string('countfrom_duedatefallback', 'local_edqscore'),
    ];

    // Assignments alone also offer the cut-off date as an anchor — the hard
    // deadline after which Moodle refuses further submissions, distinct
    // from the (soft, still-submittable) due date. Forums and quizzes have
    // no equivalent field, so they stick to $countfromoptions above.
    $assigncountfromoptions = $countfromoptions + [
        LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE => get_string('countfrom_cutoffdate', 'local_edqscore'),
        LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK => get_string('countfrom_cutoffdatefallback', 'local_edqscore'),
    ];

    $settings->add(new admin_setting_heading('local_edqscore_settingsgroupassignments',
        get_string('settingsgroupassignments', 'local_edqscore'), ''));

    $settings->add(new admin_setting_configtext('local_edqscore/assigngradinghours',
        get_string('assigngradinghours', 'local_edqscore'), get_string('assigngradinghours_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_DEFAULT_ASSIGN_HOURS, PARAM_INT));

    $settings->add(new admin_setting_configtext('local_edqscore/assignlatehours',
        get_string('assignlatehours', 'local_edqscore'), get_string('assignlatehours_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_DEFAULT_LATE_HOURS, PARAM_INT));

    $settings->add(new admin_setting_configselect('local_edqscore/assigncountfrom',
        get_string('assigncountfrom', 'local_edqscore'), get_string('assigncountfrom_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, $assigncountfromoptions));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/onlyshowsubmitted',
        get_string('onlyshowsubmitted', 'local_edqscore'), get_string('onlyshowsubmitted_desc', 'local_edqscore'), 0));

    // Doesn't make sense once assignments are tracked from the due date
    // instead of the submission date — seeing who hasn't submitted yet is
    // the whole point in that mode, so hide the toggle in that case
    // (matches Moodle's own admin_settingpage::hide_if() mechanism).
    $settings->hide_if(
        'local_edqscore/onlyshowsubmitted',
        'local_edqscore/assigncountfrom',
        'neq',
        LOCAL_EDQSCORE_COUNTFROM_SUBMISSION
    );

    $settings->add(new admin_setting_heading('local_edqscore_settingsgroupforums',
        get_string('settingsgroupforums', 'local_edqscore'), ''));

    $settings->add(new admin_setting_configtext('local_edqscore/forumgradinghours',
        get_string('forumgradinghours', 'local_edqscore'), get_string('forumgradinghours_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_DEFAULT_FORUM_HOURS, PARAM_INT));

    $settings->add(new admin_setting_configtext('local_edqscore/forumlatehours',
        get_string('forumlatehours', 'local_edqscore'), get_string('forumlatehours_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_DEFAULT_LATE_HOURS, PARAM_INT));

    $settings->add(new admin_setting_configselect('local_edqscore/forumcountfrom',
        get_string('forumcountfrom', 'local_edqscore'), get_string('forumcountfrom_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, $countfromoptions));

    $settings->add(new admin_setting_heading('local_edqscore_settingsgroupquizzes',
        get_string('settingsgroupquizzes', 'local_edqscore'), ''));

    $settings->add(new admin_setting_configtext('local_edqscore/quizgradinghours',
        get_string('quizgradinghours', 'local_edqscore'), get_string('quizgradinghours_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_DEFAULT_QUIZ_HOURS, PARAM_INT));

    $settings->add(new admin_setting_configtext('local_edqscore/quizlatehours',
        get_string('quizlatehours', 'local_edqscore'), get_string('quizlatehours_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_DEFAULT_LATE_HOURS, PARAM_INT));

    $settings->add(new admin_setting_configselect('local_edqscore/quizcountfrom',
        get_string('quizcountfrom', 'local_edqscore'), get_string('quizcountfrom_desc', 'local_edqscore'),
        LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, $countfromoptions));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/quizonlymanual',
        get_string('quizonlymanual', 'local_edqscore'), get_string('quizonlymanual_desc', 'local_edqscore'), 1));

    $settings->add(new admin_setting_heading(
        'local_edqscore_settingsgroupnotifications',
        get_string('settingsgroupnotifications', 'local_edqscore'),
        get_string('settingsgroupnotifications_desc', 'local_edqscore')
    ));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/edqscoredigestenabled',
        get_string('edqscoredigestenabled', 'local_edqscore'), get_string('edqscoredigestenabled_desc', 'local_edqscore'), 1));

    $settings->add(new admin_setting_configcheckbox('local_edqscore/submissiondigestenabled',
        get_string('submissiondigestenabled', 'local_edqscore'), get_string('submissiondigestenabled_desc', 'local_edqscore'), 1));

    $ADMIN->add('localplugins', $settings);
}

// Settings are registered directly above; prevent the plugin loader from
// trying to add anything else under this name.
$settings = null;

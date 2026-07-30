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
 * Strings for local_edqscore.
 *
 * @package    local_edqscore
 * @copyright  2026 Emvipi Baseball Institute
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'EdQ Score';
$string['edqscore:view'] = 'View EdQ Score teaching analytics';
$string['edqscore:manage'] = 'Manage EdQ Score site-wide default settings';
$string['edqscore:configurecourse'] = 'Override EdQ Score grading-turnaround thresholds for a course';

$string['scopeall'] = 'Showing all students';
$string['scopegroups'] = 'Showing students in your group(s): {$a}';
$string['scopenone'] = 'You are not a member of any group in this course, so no students are in scope. Ask an administrator to add you to a group.';

$string['students'] = 'Students';
$string['noneinscope'] = 'No students in scope';
$string['nosubmittedwork'] = 'No submitted work yet.';
$string['selectgroup'] = 'Select group';
$string['allgroups'] = 'All groups';
$string['go'] = 'Go';
$string['kpiactivities'] = 'Activities tracked';
$string['kpioverdue'] = 'Overdue gradings';

$string['sectionassignments'] = 'Assignments';
$string['sectionforums'] = 'Forums';
$string['sectionquizzes'] = 'Quizzes';

$string['colname'] = 'Name';
$string['colstudent'] = 'Student';
$string['coldue'] = 'Due';
$string['colsubmissiondate'] = 'Submission date';
$string['colgradedon'] = 'Graded on';
$string['colgrade'] = 'Grade';
$string['countsubmitted'] = 'Submitted';
$string['countgraded'] = 'Graded';
$string['coloverdue'] = 'Overdue grading';
$string['overdueyes'] = 'Overdue';
$string['overdueno'] = 'On time';
$string['statuslate'] = 'Late';
$string['allcaughtup'] = 'All caught up';
$string['coloverridden'] = 'Overridden';
$string['overriddenyes'] = 'Yes';
$string['overriddenno'] = 'No';
$string['colfeedback'] = 'Feedback';
$string['feedbackshow'] = 'Show';
$string['feedbackno'] = 'No';
$string['edq'] = 'EdQ';
$string['edqdesc'] = 'Education Quality — the share of submissions, forum replies, feedback and manually graded quiz attempts handled inside the grading-turnaround window.';
$string['edqmisses'] = "What's affecting your EdQ score";
$string['edqmissesall100'] = 'Everything is on time — nothing is pulling the score down right now.';
$string['edqmiss_notgraded'] = 'Not yet graded';
$string['edqmiss_gradedlate'] = 'Graded late';
$string['edqmiss_nofeedback'] = 'No feedback given';
$string['edqmiss_noreply'] = 'No instructor reply yet';
$string['chartcompletiontitle'] = 'Completion rate by module';
$string['viewdetails'] = 'View details';
$string['coldiscussions'] = 'Discussions';
$string['coldiscussionname'] = 'Discussion';
$string['colposts'] = 'Posts';
$string['collastpost'] = 'Last post by';
$string['colparticipation'] = 'Participation';
$string['colinstructorreplies'] = 'Instructor replies';
$string['colwhen'] = 'Last reply';
$string['nodiscussions'] = 'No discussions yet.';
$string['noposts'] = 'No posts yet';
$string['instructor'] = 'Instructor';
$string['colattempts'] = 'Attempts';
$string['collastattempt'] = 'Last attempt';
$string['colscore'] = 'Score';
$string['colcompleted'] = 'Completed';
$string['colavgscore'] = 'Avg. score';
$string['noactivities'] = 'No {$a} in this course yet.';

$string['autograded'] = 'Auto-graded';
$string['containsessay'] = 'Contains essay';

$string['settingsheading'] = 'Grading-turnaround defaults';
$string['settingsheading_desc'] = 'Flag a submission or post once it has been waiting longer than this, site-wide — "On time" up to this threshold, "Late" beyond it (see the "considered late" settings below for when that becomes "Overdue"). Individual courses can override these defaults.';
$string['assigngradinghours'] = 'Assignment grading hours';
$string['assigngradinghours_desc'] = 'Hours an assignment submission can go ungraded before it stops counting as "On time".';
$string['forumgradinghours'] = 'Forum grading hours';
$string['forumgradinghours_desc'] = 'Hours a graded forum post can go ungraded — or, for ungraded forums, a student post can go without an instructor reply — before it stops counting as "On time".';
$string['assignlatehours'] = 'Assignment considered late after';
$string['assignlatehours_desc'] = 'Hours past the grading-hours threshold above before an assignment flips from "Late" to "Overdue". Applies even to work that\'s since been graded — grading it doesn\'t cap it at "Late" if it took this long.';
$string['forumlatehours'] = 'Forum considered late after';
$string['forumlatehours_desc'] = 'Hours past the grading-hours threshold above before a forum item flips from "Late" to "Overdue". Applies even to work that\'s since been graded/replied to — doing so doesn\'t cap it at "Late" if it took this long.';
$string['quizlatehours'] = 'Quiz considered late after';
$string['quizlatehours_desc'] = 'Hours past the grading-hours threshold above before an essay-question quiz flips from "Late" to "Overdue". Applies even to work that\'s since been graded — grading it doesn\'t cap it at "Late" if it took this long.';

$string['showungraded'] = 'Show ungraded items';
$string['showungraded_desc'] = 'Items with no grading configured at all (e.g. a general discussion forum, or an assignment/quiz with no grade) are hidden from the board by default, so instructors only see what actually needs grading. Turn this on to show them too.';

$string['edqincludefeedback'] = 'Include feedback on assignments for the EdQ';
$string['edqincludefeedback_desc'] = 'Counts whether feedback text was left on each submitted assignment as its own trackable/on-time pair in the EdQ score, alongside grading turnaround. Only submitted work is counted — a student who never submitted isn\'t held against the instructor for missing feedback.';
$string['edqincludequizmanual'] = 'Include manually graded quizzes in the EdQ';
$string['edqincludequizmanual_desc'] = 'Counts grading turnaround on quizzes containing an essay question towards the EdQ score. Fully auto-graded quizzes never count towards EdQ regardless of this setting, since there\'s no manual grading action to measure.';
$string['edqincludefeedbackon'] = 'Include feedback on assignments';
$string['edqincludefeedbackoff'] = 'Exclude feedback on assignments';
$string['edqincludequizmanualon'] = 'Include manually graded quizzes';
$string['edqincludequizmanualoff'] = 'Exclude manually graded quizzes';
$string['edqincludefeedback_opt_on_desc'] = 'Counts whether feedback was left on each submitted assignment as part of the EdQ score.';
$string['edqincludefeedback_opt_off_desc'] = 'Leaves assignment feedback out of the EdQ score entirely.';
$string['edqincludequizmanual_opt_on_desc'] = 'Counts grading turnaround on essay-question quizzes towards the EdQ score.';
$string['edqincludequizmanual_opt_off_desc'] = 'Leaves quiz grading turnaround out of the EdQ score entirely.';

$string['countfrom_submission'] = 'Submission date';
$string['countfrom_duedate'] = 'Due date';
$string['countfrom_duedatefallback'] = 'Due date (submission date if no due date is set)';
$string['countfrom_cutoffdate'] = 'Cut-off date';
$string['countfrom_cutoffdatefallback'] = 'Cut-off date (due date if no cut-off is set)';
$string['countfrom_submission_desc'] = 'The turnaround clock starts when the student actually submitted or posted.';
$string['countfrom_duedate_desc'] = 'The clock starts at the due date, regardless of when the student actually submitted or posted.';
$string['countfrom_duedatefallback_desc'] = 'Uses the due date if one is set, otherwise falls back to the submission/post date.';
$string['countfrom_cutoffdate_desc'] = 'The clock starts at the assignment\'s cut-off date — the hard deadline after which Moodle blocks further submissions — regardless of when the student actually submitted. Assignments only; if no cut-off date is set, the clock never starts.';
$string['countfrom_cutoffdatefallback_desc'] = 'Uses the assignment\'s cut-off date if one is set, otherwise falls back to the due date. Assignments only.';
$string['assigncountfrom'] = 'Count assignment turnaround from';
$string['assigncountfrom_desc'] = 'What the assignment grading-turnaround clock counts from: when each student actually submitted, the assignment\'s due date, or its cut-off date (the hard deadline after which no further submissions are accepted) — regardless of when they submitted.';
$string['forumcountfrom'] = 'Count forum turnaround from';
$string['forumcountfrom_desc'] = 'What the forum grading/reply-turnaround clock counts from: when the student actually posted, or the forum\'s due date regardless of when they posted.';
$string['quizgradinghours'] = 'Quiz grading hours';
$string['quizgradinghours_desc'] = 'Hours an essay-question quiz attempt can go unmarked before it stops counting as "On time". Only applies to quizzes containing at least one essay question — fully auto-graded quizzes are scored the instant they\'re submitted, so this never applies to them.';
$string['quizcountfrom'] = 'Count quiz turnaround from';
$string['quizcountfrom_desc'] = 'What the essay-grading turnaround clock counts from: when the student submitted their attempt, or the quiz\'s close date regardless of when they submitted. Only applies to quizzes with essay questions.';
$string['quizonlymanual'] = 'Show only manually graded quizzes';
$string['quizonlymanual_desc'] = 'Fully auto-graded quizzes are scored the instant a student submits — there\'s nothing for the instructor to do. On by default so the board only shows quizzes that actually need the instructor\'s attention (i.e. contain at least one essay question). Turn off to also list auto-graded quizzes for reference.';
$string['quizonlymanualon'] = 'Show only manually graded quizzes';
$string['quizonlymanualoff'] = 'Show all quizzes, including fully auto-graded ones';
$string['quizonlymanual_opt_on_desc'] = 'Hide fully auto-graded quizzes from the board — only show quizzes with an essay question.';
$string['quizonlymanual_opt_off_desc'] = 'Show every quiz, including fully auto-graded ones, for reference.';

$string['onlyshowsubmitted'] = 'Only show submitted work';
$string['onlyshowsubmitted_desc'] = 'Hide students who haven\'t submitted an assignment from the detail list (they\'re still counted in the totals, just not listed as a row). Only takes effect when assignments are tracked by submission date — when tracking by due date, seeing who hasn\'t submitted yet is the point, so this is ignored (your choice here is kept and applies again if you switch back).';
$string['onlyshowsubmittedon'] = 'Only show submitted work';
$string['onlyshowsubmittedoff'] = 'Show everyone, including unsubmitted';
$string['onlyshowsubmitted_disabled'] = 'Not currently applied: assignments in this course are tracked by due date, so unsubmitted work always shows regardless of this setting.';
$string['onlyshowsubmitted_opt_on_desc'] = 'Hide students who haven\'t submitted from the detail list.';
$string['onlyshowsubmitted_opt_off_desc'] = 'Show all students in scope, including those who haven\'t submitted.';
$string['showungradedon'] = 'Show ungraded items';
$string['showungradedoff'] = 'Hide ungraded items';
$string['showungraded_opt_on_desc'] = 'Items with no grading configured are shown on the board too.';
$string['showungraded_opt_off_desc'] = 'Items with no grading configured (e.g. a general discussion forum) are hidden, so instructors only see what needs grading.';
$string['usesitedefault'] = 'Use site default (currently: {$a})';
$string['usesitedefault_desc'] = 'Follows whatever the site-wide setting is currently set to — if that changes later, this course follows along automatically.';
$string['shown'] = 'Shown';
$string['hidden'] = 'Hidden';

$string['settingsgroupgeneral'] = 'General';
$string['settingsgroupassignments'] = 'Assignments';
$string['settingsgroupforums'] = 'Forums';
$string['settingsgroupquizzes'] = 'Quizzes';
$string['settingsgroupnotifications'] = 'Notifications';
$string['settingsgroupnotifications_desc'] = 'Daily digest notifications, delivered via Moodle\'s own notification system (Web popup by default; each instructor can turn on email too in their Notification preferences). Turn either digest off entirely here, site-wide or per course below.';

$string['edqscoredigestenabled'] = 'Send EdQ Score digest';
$string['edqscoredigestenabled_desc'] = 'Once a day, notify every instructor of their current EdQ score and top misses for each course they can see on the dashboard.';
$string['edqscoredigestenabled_opt_on_desc'] = 'Send the daily EdQ Score digest for this course.';
$string['edqscoredigestenabled_opt_off_desc'] = 'Never send the EdQ Score digest for this course, regardless of the site default.';

$string['submissiondigestenabled'] = 'Send assignment submission digest';
$string['submissiondigestenabled_desc'] = 'Once a day, notify every instructor which of their students submitted an assignment in the last 24 hours.';
$string['submissiondigestenabled_opt_on_desc'] = 'Send the daily submission digest for this course.';
$string['submissiondigestenabled_opt_off_desc'] = 'Never send the submission digest for this course, regardless of the site default.';

$string['digestenabledon'] = 'On';
$string['digestenabledoff'] = 'Off';

$string['coursesettings'] = 'Course settings';
$string['coursesettingsintro'] = 'Override the site-wide grading-turnaround thresholds for this course only. Leave a field blank to use the default.';
$string['expandall'] = 'Expand all';
$string['collapseall'] = 'Collapse all';
$string['usedefault'] = 'Default: {$a}h';
$string['savechanges'] = 'Save changes';
$string['settingssaved'] = 'Course settings saved.';

$string['privacy:metadata'] = 'EdQ Score does not store personal data; it only reports on Moodle activity data (submissions, posts, quiz attempts) that other Moodle plugins already store, and a per-course grading-turnaround configuration.';

$string['messageprovider:edqscoredigest'] = 'EdQ Score digest';
$string['messageprovider:submissiondigest'] = "Notification of your students' assignment submissions";

$string['task_edqscoredigest'] = 'Send EdQ Score digest notifications';
$string['task_submissiondigest'] = 'Send assignment submission digest notifications';

$string['digestedqscore_subject'] = 'Your EdQ score for {$a->course}: {$a->score}%';
$string['digestedqscore_small'] = 'Your EdQ score for {$a->course} is {$a->score}% ({$a->misscount} items need attention).';
$string['digestedqscore_intro'] = 'Your EdQ score for {$a->course} is {$a->score}%. {$a->misscount} item(s) are pulling it down:';
$string['digestedqscore_missline'] = '{$a->student} — {$a->item} ({$a->reason})';

$string['digestsubmissions_subject'] = '{$a->count} new assignment submission(s) in {$a->course}';
$string['digestsubmissions_intro'] = '{$a->count} student(s) submitted an assignment in {$a->course} in the last day:';
$string['digestsubmissions_line'] = '{$a->student} — {$a->assignment} ({$a->time})';

$string['digest_more'] = '+ {$a} more';

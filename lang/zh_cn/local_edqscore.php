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
 * Strings for local_edqscore (Simplified Chinese).
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'EdQ 评分';
$string['edqscore:view'] = '查看 EdQ 评分教学分析';
$string['edqscore:manage'] = '管理 EdQ 评分的全站默认设置';
$string['edqscore:configurecourse'] = '为课程覆盖 EdQ 评分的批改时效阈值';

$string['scopeall'] = '显示所有学生';
$string['scopegroups'] = '显示您所在小组的学生：{$a}';
$string['scopenone'] = '您不属于本课程的任何小组，因此没有学生在您的查看范围内。请联系管理员将您加入某个小组。';

$string['students'] = '学生';
$string['noneinscope'] = '查看范围内没有学生';
$string['nosubmittedwork'] = '目前还没有提交的作业。';
$string['selectgroup'] = '选择小组';
$string['allgroups'] = '所有小组';
$string['go'] = '前往';
$string['kpiactivities'] = '已跟踪的活动';
$string['kpioverdue'] = '逾期批改数';

$string['sectionassignments'] = '作业';
$string['sectionforums'] = '论坛';
$string['sectionquizzes'] = '测验';

$string['colname'] = '名称';
$string['colstudent'] = '学生';
$string['coldue'] = '截止日期';
$string['colsubmissiondate'] = '提交日期';
$string['colgradedon'] = '批改日期';
$string['colgrade'] = '成绩';
$string['countsubmitted'] = '已提交';
$string['countgraded'] = '已批改';
$string['coloverdue'] = '逾期批改';
$string['overdueyes'] = '已逾期';
$string['overdueno'] = '按时';
$string['statuslate'] = '迟交';
$string['allcaughtup'] = '全部完成';
$string['coloverridden'] = '已覆盖';
$string['overriddenyes'] = '是';
$string['overriddenno'] = '否';
$string['colfeedback'] = '反馈';
$string['feedbackshow'] = '显示';
$string['feedbackno'] = '无';
$string['edq'] = 'EdQ';
$string['edq_help'] = 'EdQ（教学质量）是您的批改时效评分：在批改时限内完成批改的学生提交、论坛回复和人工批改的测验尝试所占的比例。迟交的作业仍可获得部分分数；只有超过逾期阈值仍未处理的作业才会拉低评分。';
$string['edqnodata'] = '暂无数据';
$string['edqnodatadesc'] = '本课程目前还没有可跟踪的活动。学生提交作业后，EdQ 评分将反映批改速度。';
$string['edqmisses'] = '需要关注';
$string['edqmissesall100'] = '一切按时完成——目前没有拉低评分的项目。';
$string['edqmiss_notgraded'] = '尚未批改';
$string['edqmiss_gradedlate'] = '批改延迟';
$string['edqmiss_nofeedback'] = '未给出反馈';
$string['edqmiss_noreply'] = '教师尚未回复';
$string['chartcompletiontitle'] = '各模块完成率';
$string['viewdetails'] = '查看详情';
$string['coldiscussions'] = '讨论';
$string['coldiscussionname'] = '讨论主题';
$string['colposts'] = '帖子';
$string['collastpost'] = '最后发帖人';
$string['colparticipation'] = '参与度';
$string['colinstructorreplies'] = '教师回复数';
$string['colwhen'] = '最后回复时间';
$string['nodiscussions'] = '暂无讨论。';
$string['noposts'] = '暂无帖子';
$string['instructor'] = '教师';
$string['colattempts'] = '尝试次数';
$string['collastattempt'] = '最后一次尝试';
$string['colscore'] = '得分';
$string['colcompleted'] = '已完成';
$string['colavgscore'] = '平均分';
$string['noactivities'] = '本课程目前还没有{$a}。';

$string['autograded'] = '自动评分';
$string['containsessay'] = '包含问答题';

$string['settingsheading'] = '批改时效默认设置';
$string['settingsheading_desc'] = '当提交或发帖等待时间超过此时限时予以标记（全站生效）——在此阈值内为"按时"，超过后为"迟交"（关于何时进一步变为"逾期"，请参见下方的"视为迟交"设置）。各课程可单独覆盖这些默认设置。';
$string['assigngradinghours'] = '作业批改时限（小时）';
$string['assigngradinghours_desc'] = '作业提交在被视为仍"按时"的前提下，最多可以未批改多少小时。';
$string['forumgradinghours'] = '论坛批改时限（小时）';
$string['forumgradinghours_desc'] = '需要评分的论坛帖子在被视为仍"按时"的前提下，最多可以未批改多少小时——对于不评分的论坛，则指学生帖子最多可以多少小时未获得教师回复。';
$string['assignlatehours'] = '作业视为迟交的时限（小时）';
$string['assignlatehours_desc'] = '超过上方批改时限多少小时后，作业状态从"迟交"变为"逾期"。即使作业后来已被批改，只要耗时达到此时限，也不会被限定为"迟交"。';
$string['forumlatehours'] = '论坛视为迟交的时限（小时）';
$string['forumlatehours_desc'] = '超过上方批改时限多少小时后，论坛项目状态从"迟交"变为"逾期"。即使后来已被批改或回复，只要耗时达到此时限，也不会被限定为"迟交"。';
$string['quizlatehours'] = '测验视为迟交的时限（小时）';
$string['quizlatehours_desc'] = '超过上方批改时限多少小时后，含问答题的测验状态从"迟交"变为"逾期"。即使后来已被批改，只要耗时达到此时限，也不会被限定为"迟交"。';

$string['showungraded'] = '显示未设置评分的项目';
$string['showungraded_desc'] = '完全未配置评分的项目（例如一般性讨论论坛，或没有设置分值的作业/测验）默认会从看板中隐藏，使教师只看到真正需要批改的内容。开启此项可同时显示这些项目。';

$string['edqincludefeedback'] = '将作业反馈计入 EdQ 评分';
$string['edqincludefeedback_desc'] = '将每份已提交作业是否留有反馈文字，作为 EdQ 评分中一个独立的"可跟踪/按时"项与批改时效一并计算。仅计算已提交的作业——从未提交的学生不会因缺少反馈而影响教师的评分。';
$string['edqincludequizmanual'] = '将人工批改的测验计入 EdQ 评分';
$string['edqincludequizmanual_desc'] = '将含问答题的测验批改时效计入 EdQ 评分。无论此设置如何，完全自动评分的测验都不会计入 EdQ，因为没有需要衡量的人工批改动作。';
$string['edqincludefeedbackon'] = '包含作业反馈';
$string['edqincludefeedbackoff'] = '不包含作业反馈';
$string['edqincludequizmanualon'] = '包含人工批改的测验';
$string['edqincludequizmanualoff'] = '不包含人工批改的测验';
$string['edqincludefeedback_opt_on_desc'] = '将每份已提交作业是否留有反馈计入 EdQ 评分。';
$string['edqincludefeedback_opt_off_desc'] = '完全不将作业反馈计入 EdQ 评分。';
$string['edqincludequizmanual_opt_on_desc'] = '将含问答题测验的批改时效计入 EdQ 评分。';
$string['edqincludequizmanual_opt_off_desc'] = '完全不将测验批改时效计入 EdQ 评分。';

$string['countfrom_submission'] = '提交日期';
$string['countfrom_duedate'] = '截止日期';
$string['countfrom_duedatefallback'] = '截止日期（若未设置截止日期则使用提交日期）';
$string['countfrom_cutoffdate'] = '关闭日期';
$string['countfrom_cutoffdatefallback'] = '关闭日期（若未设置关闭日期则使用截止日期）';
$string['countfrom_submission_desc'] = '时效计时从学生实际提交或发帖的时间开始。';
$string['countfrom_duedate_desc'] = '计时从截止日期开始，与学生实际提交或发帖的时间无关。';
$string['countfrom_duedatefallback_desc'] = '若设置了截止日期则使用截止日期，否则回退使用提交/发帖日期。';
$string['countfrom_cutoffdate_desc'] = '计时从作业的关闭日期开始——即 Moodle 阻止继续提交的最终期限——与学生实际提交时间无关。仅适用于作业；若未设置关闭日期，则计时不会开始。';
$string['countfrom_cutoffdatefallback_desc'] = '若设置了关闭日期则使用关闭日期，否则回退使用截止日期。仅适用于作业。';
$string['assigncountfrom'] = '作业时效计时起点';
$string['assigncountfrom_desc'] = '作业批改时效计时的起点：学生实际提交的时间、作业截止日期，或作业关闭日期（不再接受提交的最终期限），均与学生实际提交时间无关。';
$string['forumcountfrom'] = '论坛时效计时起点';
$string['forumcountfrom_desc'] = '论坛批改/回复时效计时的起点：学生实际发帖的时间，或论坛截止日期（与学生实际发帖时间无关）。';
$string['quizgradinghours'] = '测验批改时限（小时）';
$string['quizgradinghours_desc'] = '含问答题的测验尝试在被视为仍"按时"的前提下，最多可以未批改多少小时。仅适用于至少含一道问答题的测验——完全自动评分的测验在提交后立即得分，因此此设置对其不适用。';
$string['quizcountfrom'] = '测验时效计时起点';
$string['quizcountfrom_desc'] = '问答题批改时效计时的起点：学生提交尝试的时间，或测验的关闭日期（与学生实际提交时间无关）。仅适用于含问答题的测验。';
$string['quizonlymanual'] = '仅显示人工批改的测验';
$string['quizonlymanual_desc'] = '完全自动评分的测验在学生提交的瞬间即可得分——教师无需进行任何操作。默认开启，使看板只显示真正需要教师关注的测验（即至少含一道问答题）。关闭后也会列出自动评分的测验以供参考。';
$string['quizonlymanualon'] = '仅显示人工批改的测验';
$string['quizonlymanualoff'] = '显示所有测验，包括完全自动评分的测验';
$string['quizonlymanual_opt_on_desc'] = '在看板中隐藏完全自动评分的测验，仅显示含问答题的测验。';
$string['quizonlymanual_opt_off_desc'] = '显示所有测验，包括完全自动评分的测验，以供参考。';

$string['onlyshowsubmitted'] = '仅显示已提交的作业';
$string['onlyshowsubmitted_desc'] = '在详细列表中隐藏尚未提交作业的学生（仍计入总数，只是不作为单独一行列出）。仅在作业按提交日期计时时生效——按截止日期计时时，查看哪些学生尚未提交正是其意义所在，因此此设置会被忽略（但您的选择会被保留，切换回按提交日期计时后会重新生效）。';
$string['onlyshowsubmittedon'] = '仅显示已提交的作业';
$string['onlyshowsubmittedoff'] = '显示所有学生，包括未提交的';
$string['onlyshowsubmitted_disabled'] = '当前未生效：本课程的作业按截止日期计时，因此无论此设置如何，未提交的作业始终会显示。';
$string['onlyshowsubmitted_opt_on_desc'] = '在详细列表中隐藏尚未提交的学生。';
$string['onlyshowsubmitted_opt_off_desc'] = '显示查看范围内的所有学生，包括尚未提交的学生。';
$string['showungradedon'] = '显示未设置评分的项目';
$string['showungradedoff'] = '隐藏未设置评分的项目';
$string['showungraded_opt_on_desc'] = '看板中也会显示未配置评分的项目。';
$string['showungraded_opt_off_desc'] = '隐藏未配置评分的项目（例如一般性讨论论坛），使教师只看到需要批改的内容。';
$string['usesitedefault'] = '使用全站默认值（当前为：{$a}）';
$string['usesitedefault_desc'] = '始终跟随全站设置当前的取值；若该设置之后发生变化，本课程会自动随之更新。';
$string['shown'] = '显示';
$string['hidden'] = '隐藏';

$string['settingsgroupgeneral'] = '常规';
$string['settingsgroupassignments'] = '作业';
$string['settingsgroupforums'] = '论坛';
$string['settingsgroupquizzes'] = '测验';
$string['settingsgroupnotifications'] = '通知';
$string['settingsgroupnotifications_desc'] = '每日摘要通知，通过 Moodle 自带的通知系统发送（默认采用网页弹出提醒；每位教师也可在其通知偏好设置中另行开启邮件提醒）。可在此处（全站或按课程，见下方）彻底关闭任一摘要。';

$string['edqscoredigestenabled'] = '发送 EdQ 评分摘要';
$string['edqscoredigestenabled_desc'] = '每天向每位教师发送通知，告知其在看板中可见的各课程当前 EdQ 评分及主要待处理事项。';
$string['edqscoredigestenabled_opt_on_desc'] = '为本课程发送每日 EdQ 评分摘要。';
$string['edqscoredigestenabled_opt_off_desc'] = '无论全站默认设置如何，均不为本课程发送 EdQ 评分摘要。';

$string['submissiondigestenabled'] = '发送作业提交摘要';
$string['submissiondigestenabled_desc'] = '每天向每位教师发送通知，告知过去 24 小时内哪些学生提交了作业。';
$string['submissiondigestenabled_opt_on_desc'] = '为本课程发送每日提交摘要。';
$string['submissiondigestenabled_opt_off_desc'] = '无论全站默认设置如何，均不为本课程发送提交摘要。';

$string['digestenabledon'] = '开启';
$string['digestenabledoff'] = '关闭';

$string['coursesettings'] = '课程设置';
$string['coursesettingsintro'] = '仅为本课程覆盖全站批改时效阈值。留空字段将使用默认值。';
$string['expandall'] = '全部展开';
$string['collapseall'] = '全部折叠';
$string['usedefault'] = '默认值：{$a} 小时';
$string['savechanges'] = '保存更改';
$string['settingssaved'] = '课程设置已保存。';

$string['privacy:metadata'] = 'EdQ 评分不存储任何个人数据；它仅基于其他 Moodle 插件已经存储的 Moodle 活动数据（提交、帖子、测验尝试）以及按课程设置的批改时效配置进行报告。';

$string['messageprovider:edqscoredigest'] = 'EdQ 评分摘要';
$string['messageprovider:submissiondigest'] = '您的学生作业提交情况通知';

$string['task_edqscoredigest'] = '发送 EdQ 评分摘要通知';
$string['task_submissiondigest'] = '发送作业提交摘要通知';

$string['digestedqscore_subject'] = '您在{$a->course}的 EdQ 评分：{$a->score}%';
$string['digestedqscore_small'] = '您在{$a->course}的 EdQ 评分为 {$a->score}%（有 {$a->misscount} 项需要关注）。';
$string['digestedqscore_intro'] = '您在{$a->course}的 EdQ 评分为 {$a->score}%。以下 {$a->misscount} 项正在拉低该评分：';
$string['digestedqscore_missline'] = '{$a->student} — {$a->item}（{$a->reason}）';

$string['digestsubmissions_subject'] = '{$a->course}有 {$a->count} 份新作业提交';
$string['digestsubmissions_intro'] = '过去一天内，{$a->course}有 {$a->count} 名学生提交了作业：';
$string['digestsubmissions_line'] = '{$a->student} — {$a->assignment}（{$a->time}）';

$string['digest_more'] = '还有 {$a} 项';

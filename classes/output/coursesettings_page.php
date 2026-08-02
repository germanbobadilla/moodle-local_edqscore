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

namespace local_edqscore\output;

/**
 * Renderable for the per-course settings form: four collapsible sections
 * (General / Assignments / Forums / Quizzes), each holding a mix of plain
 * number inputs and rich combo-select dropdowns.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursesettings_page implements \renderable, \templatable {

    /** @var \stdClass the course */
    protected $course;

    /** @var \moodle_url the form's post target / this page's own url */
    protected $pageurl;

    /** @var \stdClass|false the existing per-course override row, or false if none */
    protected $existing;

    /** @var array site-wide default values, keyed by setting name */
    protected $defaults;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param \moodle_url $pageurl
     * @param \stdClass|false $existing
     * @param array $defaults
     */
    public function __construct(\stdClass $course, \moodle_url $pageurl, $existing, array $defaults) {
        $this->course = $course;
        $this->pageurl = $pageurl;
        $this->existing = $existing;
        $this->defaults = $defaults;
    }

    /**
     * Value currently stored for a nullable per-course override field, as a
     * string ('' when unset, meaning "use site default").
     *
     * @param string $field
     * @return string
     */
    protected function existing_value(string $field): string {
        if ($this->existing && $this->existing->$field !== null) {
            return (string) $this->existing->$field;
        }
        return '';
    }

    /**
     * Build a plain number-input field.
     *
     * @param string $name
     * @param string $labelstring
     * @param int $default
     * @param string $descstring
     * @return array
     */
    protected function number_field(string $name, string $labelstring, int $default, string $descstring): array {
        return [
            'iscombo' => false,
            'isnumber' => true,
            'label' => get_string($labelstring, 'local_edqscore'),
            'name' => $name,
            'value' => $this->existing_value($name),
            'placeholder' => get_string('usedefault', 'local_edqscore', $default),
            'desc' => get_string($descstring, 'local_edqscore'),
        ];
    }

    /**
     * Build a rich combo-select field.
     *
     * @param string $name
     * @param string $labelstring
     * @param string $defaultlabel
     * @param array $baseoptions
     * @param bool $hasnote
     * @param bool $notevisible
     * @param string $notestring
     * @return array
     */
    protected function combo_field(
        string $name,
        string $labelstring,
        string $defaultlabel,
        array $baseoptions,
        bool $hasnote = false,
        bool $notevisible = false,
        string $notestring = ''
    ): array {
        $options = helper::combo_options_with_default($defaultlabel, $baseoptions);
        return [
            'iscombo' => true,
            'isnumber' => false,
            'label' => get_string($labelstring, 'local_edqscore'),
            'combo' => helper::export_combo($name, $options, $this->existing_value($name)),
            'hasnote' => $hasnote,
            'noteid' => $hasnote ? ($name . '-disabled-note') : null,
            'notevisible' => $notevisible,
            'note' => $notestring,
        ];
    }

    /**
     * Export context for use in the local_edqscore/coursesettings_page template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $courseid = $this->course->id;
        $d = $this->defaults;

        $countfrombase = [
            ['value' => LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, 'label' => get_string('countfrom_submission', 'local_edqscore'),
                'desc' => get_string('countfrom_submission_desc', 'local_edqscore')],
            [
                'value' => LOCAL_EDQSCORE_COUNTFROM_DUEDATE,
                'label' => get_string('countfrom_duedate', 'local_edqscore'),
                'desc' => get_string('countfrom_duedate_desc', 'local_edqscore'),
            ],
            [
                'value' => LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK,
                'label' => get_string('countfrom_duedatefallback', 'local_edqscore'),
                'desc' => get_string('countfrom_duedatefallback_desc', 'local_edqscore'),
            ],
        ];
        $countfromlabels = [
            LOCAL_EDQSCORE_COUNTFROM_SUBMISSION => get_string('countfrom_submission', 'local_edqscore'),
            LOCAL_EDQSCORE_COUNTFROM_DUEDATE => get_string('countfrom_duedate', 'local_edqscore'),
            LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK => get_string('countfrom_duedatefallback', 'local_edqscore'),
        ];

        // Assignments alone also offer the cut-off date as an anchor — the
        // hard deadline after which Moodle refuses further submissions,
        // distinct from the (soft, still-submittable) due date. Forums and
        // quizzes have no equivalent field, so they stick to $countfrombase
        // and $countfromlabels above.
        $assigncountfrombase = array_merge($countfrombase, [
            [
                'value' => LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE,
                'label' => get_string('countfrom_cutoffdate', 'local_edqscore'),
                'desc' => get_string('countfrom_cutoffdate_desc', 'local_edqscore'),
            ],
            [
                'value' => LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK,
                'label' => get_string('countfrom_cutoffdatefallback', 'local_edqscore'),
                'desc' => get_string('countfrom_cutoffdatefallback_desc', 'local_edqscore'),
            ],
        ]);
        $assigncountfromlabels = $countfromlabels + [
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE => get_string('countfrom_cutoffdate', 'local_edqscore'),
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK => get_string('countfrom_cutoffdatefallback', 'local_edqscore'),
        ];

        $assigncountfromvalue = $this->existing_value('assigncountfrom');
        $assigncountfromeffective = $assigncountfromvalue !== '' ? $assigncountfromvalue : $d['assigncountfrom'];
        $onlyshowsubmitteddisabled = $assigncountfromeffective !== LOCAL_EDQSCORE_COUNTFROM_SUBMISSION;

        $general = [
            $this->combo_field(
                'showungraded',
                'showungraded',
                get_string($d['showungraded'] ? 'shown' : 'hidden', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('showungradedon', 'local_edqscore'),
                        'desc' => get_string('showungraded_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('showungradedoff', 'local_edqscore'),
                        'desc' => get_string('showungraded_opt_off_desc', 'local_edqscore')],
                ]
            ),
            $this->combo_field(
                'edqincludefeedback',
                'edqincludefeedback',
                get_string($d['edqincludefeedback'] ? 'edqincludefeedbackon' : 'edqincludefeedbackoff', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('edqincludefeedbackon', 'local_edqscore'),
                        'desc' => get_string('edqincludefeedback_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('edqincludefeedbackoff', 'local_edqscore'),
                        'desc' => get_string('edqincludefeedback_opt_off_desc', 'local_edqscore')],
                ]
            ),
            $this->combo_field(
                'edqincludequizmanual',
                'edqincludequizmanual',
                get_string($d['edqincludequizmanual'] ? 'edqincludequizmanualon' : 'edqincludequizmanualoff', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('edqincludequizmanualon', 'local_edqscore'),
                        'desc' => get_string('edqincludequizmanual_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('edqincludequizmanualoff', 'local_edqscore'),
                        'desc' => get_string('edqincludequizmanual_opt_off_desc', 'local_edqscore')],
                ]
            ),
        ];

        $assignments = [
            $this->number_field('assigngradinghours', 'assigngradinghours', $d['assigngradinghours'], 'assigngradinghours_desc'),
            $this->number_field('assignlatehours', 'assignlatehours', $d['assignlatehours'], 'assignlatehours_desc'),
            $this->combo_field(
                'assigncountfrom',
                'assigncountfrom',
                $assigncountfromlabels[$d['assigncountfrom']],
                $assigncountfrombase
            ),
            $this->combo_field(
                'onlyshowsubmitted',
                'onlyshowsubmitted',
                get_string($d['onlyshowsubmitted'] ? 'onlyshowsubmittedon' : 'onlyshowsubmittedoff', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('onlyshowsubmittedon', 'local_edqscore'),
                        'desc' => get_string('onlyshowsubmitted_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('onlyshowsubmittedoff', 'local_edqscore'),
                        'desc' => get_string('onlyshowsubmitted_opt_off_desc', 'local_edqscore')],
                ],
                true,
                $onlyshowsubmitteddisabled,
                get_string('onlyshowsubmitted_disabled', 'local_edqscore')
            ),
        ];

        $forums = [
            $this->number_field('forumgradinghours', 'forumgradinghours', $d['forumgradinghours'], 'forumgradinghours_desc'),
            $this->number_field('forumlatehours', 'forumlatehours', $d['forumlatehours'], 'forumlatehours_desc'),
            $this->combo_field(
                'forumcountfrom',
                'forumcountfrom',
                $countfromlabels[$d['forumcountfrom']],
                $countfrombase
            ),
        ];

        $quizzes = [
            $this->number_field('quizgradinghours', 'quizgradinghours', $d['quizgradinghours'], 'quizgradinghours_desc'),
            $this->number_field('quizlatehours', 'quizlatehours', $d['quizlatehours'], 'quizlatehours_desc'),
            $this->combo_field(
                'quizcountfrom',
                'quizcountfrom',
                $countfromlabels[$d['quizcountfrom']],
                $countfrombase
            ),
            $this->combo_field(
                'quizonlymanual',
                'quizonlymanual',
                get_string($d['quizonlymanual'] ? 'quizonlymanualon' : 'quizonlymanualoff', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('quizonlymanualon', 'local_edqscore'),
                        'desc' => get_string('quizonlymanual_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('quizonlymanualoff', 'local_edqscore'),
                        'desc' => get_string('quizonlymanual_opt_off_desc', 'local_edqscore')],
                ]
            ),
        ];

        $notifications = [
            $this->combo_field(
                'edqscoredigestenabled',
                'edqscoredigestenabled',
                get_string($d['edqscoredigestenabled'] ? 'digestenabledon' : 'digestenabledoff', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('digestenabledon', 'local_edqscore'),
                        'desc' => get_string('edqscoredigestenabled_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('digestenabledoff', 'local_edqscore'),
                        'desc' => get_string('edqscoredigestenabled_opt_off_desc', 'local_edqscore')],
                ]
            ),
            $this->combo_field(
                'submissiondigestenabled',
                'submissiondigestenabled',
                get_string($d['submissiondigestenabled'] ? 'digestenabledon' : 'digestenabledoff', 'local_edqscore'),
                [
                    ['value' => '1', 'label' => get_string('digestenabledon', 'local_edqscore'),
                        'desc' => get_string('submissiondigestenabled_opt_on_desc', 'local_edqscore')],
                    ['value' => '0', 'label' => get_string('digestenabledoff', 'local_edqscore'),
                        'desc' => get_string('submissiondigestenabled_opt_off_desc', 'local_edqscore')],
                ]
            ),
        ];

        return [
            'backurl' => (new \moodle_url('/local/edqscore/course.php', ['id' => $courseid]))->out(false),
            'coursefullname' => format_string($this->course->fullname),
            'coursesettingsintro' => get_string('coursesettingsintro', 'local_edqscore'),
            'expandalllabel' => get_string('expandall', 'local_edqscore'),
            'formurl' => $this->pageurl->out(false),
            'sesskey' => sesskey(),
            'sections' => [
                ['id' => 'mygama-section-general', 'title' => get_string('settingsgroupgeneral', 'local_edqscore'),
                    'expanded' => true, 'fields' => $general],
                ['id' => 'mygama-section-assignments', 'title' => get_string('settingsgroupassignments', 'local_edqscore'),
                    'expanded' => false, 'fields' => $assignments],
                ['id' => 'mygama-section-forums', 'title' => get_string('settingsgroupforums', 'local_edqscore'),
                    'expanded' => false, 'fields' => $forums],
                ['id' => 'mygama-section-quizzes', 'title' => get_string('settingsgroupquizzes', 'local_edqscore'),
                    'expanded' => false, 'fields' => $quizzes],
                ['id' => 'mygama-section-notifications', 'title' => get_string('settingsgroupnotifications', 'local_edqscore'),
                    'expanded' => false, 'fields' => $notifications],
            ],
            'savechangeslabel' => get_string('savechanges', 'local_edqscore'),
            'assigncountfromsitedefault' => $d['assigncountfrom'],
            'countfromsubmission' => LOCAL_EDQSCORE_COUNTFROM_SUBMISSION,
            'expandcollapsedata' => [
                'expandlabel' => get_string('expandall', 'local_edqscore'),
                'collapselabel' => get_string('collapseall', 'local_edqscore'),
            ],
        ];
    }
}

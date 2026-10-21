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

namespace local_edqscore;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/edqscore/lib.php');

/**
 * Unit tests for the pure grading-turnaround logic in lib.php: the anchor-resolution,
 * on-time/late/overdue classification, and the EdQ score calculation itself. These three
 * functions take plain scalars/arrays and have no side effects, so they're tested directly
 * without needing any course/activity fixtures.
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::local_edqscore_resolve_countfrom
 * @covers     ::local_edqscore_grading_status
 * @covers     ::local_edqscore_compute_edq
 */
final class lib_test extends \advanced_testcase {
    // Tests for local_edqscore_resolve_countfrom().

    public function test_resolve_countfrom_submission_mode_always_returns_submission(): void {
        $this->assertSame(500, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_SUBMISSION,
            100,
            500,
            900
        ));
        // Even with no due date or cutoff date set, submission mode just returns the
        // submission date verbatim — it never falls back to anything else.
        $this->assertSame(500, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_SUBMISSION,
            0,
            500,
            0
        ));
    }

    public function test_resolve_countfrom_duedate_mode_requires_a_real_duedate(): void {
        $this->assertSame(100, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_DUEDATE,
            100,
            500,
            900
        ));
        // No due date configured (0) means "can't measure this one" -> null, not a
        // silent fallback to the submission date.
        $this->assertNull(local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_DUEDATE,
            0,
            500,
            900
        ));
    }

    public function test_resolve_countfrom_duedate_fallback_mode_falls_back_to_submission(): void {
        $this->assertSame(100, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK,
            100,
            500,
            900
        ));
        $this->assertSame(500, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_DUEDATE_FALLBACK,
            0,
            500,
            900
        ));
    }

    public function test_resolve_countfrom_cutoffdate_mode_requires_a_real_cutoffdate(): void {
        $this->assertSame(900, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE,
            100,
            500,
            900
        ));
        $this->assertNull(local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE,
            100,
            500,
            0
        ));
    }

    public function test_resolve_countfrom_cutoffdate_fallback_mode_prefers_cutoff_then_duedate(): void {
        $this->assertSame(900, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK,
            100,
            500,
            900
        ));
        // No cutoff -> falls back to the due date.
        $this->assertSame(100, local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK,
            100,
            500,
            0
        ));
        // Neither cutoff nor due date -> nothing to measure from.
        $this->assertNull(local_edqscore_resolve_countfrom(
            LOCAL_EDQSCORE_COUNTFROM_CUTOFFDATE_FALLBACK,
            0,
            500,
            0
        ));
    }

    public function test_resolve_countfrom_unknown_mode_defaults_to_submission(): void {
        // The switch's default case mirrors LOCAL_EDQSCORE_COUNTFROM_SUBMISSION, so a
        // stored setting value that doesn't match any known mode degrades safely instead
        // of throwing or silently measuring from nothing.
        $this->assertSame(500, local_edqscore_resolve_countfrom('not_a_real_mode', 100, 500, 900));
    }

    // Tests for local_edqscore_grading_status().

    public function test_grading_status_ontime_boundary_is_inclusive(): void {
        $anchor = 1000;
        $ontimehours = 48;
        $latehours = 120;
        $now = $anchor + (200 * HOURSECS);

        // Done exactly at the on-time threshold counts as on time (the check is <=).
        $this->assertSame('ontime', local_edqscore_grading_status(
            $anchor + (48 * HOURSECS),
            $anchor,
            $ontimehours,
            $latehours,
            $now
        ));
        // One second past it tips into late.
        $this->assertSame('late', local_edqscore_grading_status(
            $anchor + (48 * HOURSECS) + 1,
            $anchor,
            $ontimehours,
            $latehours,
            $now
        ));
    }

    public function test_grading_status_late_boundary_is_exclusive_of_overdue(): void {
        $anchor = 1000;
        $ontimehours = 48;
        $latehours = 120;
        $now = $anchor + (200 * HOURSECS);

        // Just under the late threshold is still "late".
        $this->assertSame('late', local_edqscore_grading_status(
            $anchor + (120 * HOURSECS) - 1,
            $anchor,
            $ontimehours,
            $latehours,
            $now
        ));
        // Exactly at (or past) the late threshold tips into "overdue" (the check is <, not <=).
        $this->assertSame('overdue', local_edqscore_grading_status(
            $anchor + (120 * HOURSECS),
            $anchor,
            $ontimehours,
            $latehours,
            $now
        ));
    }

    public function test_grading_status_still_overdue_even_once_done_if_it_took_too_long(): void {
        // Being graded doesn't cap the status at "late" once it's crossed the overdue
        // line — a late grade on something that sat for 300 hours is still "overdue",
        // not "late", even though it's finished now.
        $anchor = 1000;
        $donetime = $anchor + (300 * HOURSECS);
        $this->assertSame('overdue', local_edqscore_grading_status(
            $donetime,
            $anchor,
            48,
            120,
            $donetime + 1000
        ));
    }

    public function test_grading_status_null_donetime_measures_against_now(): void {
        // Nothing graded yet: elapsed time is measured from "now", not from a done time
        // that doesn't exist — this is how a still-pending item correctly becomes
        // "overdue" once it's been waiting long enough.
        $anchor = 1000;
        $faroverdue = $anchor + (500 * HOURSECS);
        $this->assertSame('overdue', local_edqscore_grading_status(null, $anchor, 48, 120, $faroverdue));

        $stillontime = $anchor + (10 * HOURSECS);
        $this->assertSame('ontime', local_edqscore_grading_status(null, $anchor, 48, 120, $stillontime));
    }

    // Tests for local_edqscore_compute_edq().

    /**
     * Minimal thresholds object accepted by compute_edq() (it only reads these two flags).
     */
    private function make_thresholds(bool $includefeedback = false, bool $includequizmanual = true): \stdClass {
        return (object) [
            'edqincludefeedback' => $includefeedback,
            'edqincludequizmanual' => $includequizmanual,
        ];
    }

    /**
     * Build a minimal assignment fixture with the aggregate counts compute_edq() reads.
     *
     * @param int $submitted total submissions
     * @param int $overdue how many of those are "not on time" (late + truly overdue combined)
     * @param int $late the subset of $overdue that is merely late, not fully overdue
     * @param int $feedback how many submissions have feedback left on them
     * @return \stdClass
     */
    private function make_assignment(int $submitted, int $overdue, int $late, int $feedback = 0): \stdClass {
        return (object) [
            'name' => 'Test assignment',
            'cmid' => 0,
            'submittedcount' => $submitted,
            'overduecount' => $overdue,
            'latecount' => $late,
            'feedbackcount' => $feedback,
            'students' => [],
        ];
    }

    public function test_compute_edq_with_no_trackable_items_scores_100_but_flags_not_trackable(): void {
        $this->resetAfterTest();
        $result = local_edqscore_compute_edq([], [], [], $this->make_thresholds());

        // A trackable count of zero is the signal callers use to show a neutral "no data
        // yet" state instead of treating this 100 as a real result — see course_page.php.
        $this->assertSame(0, $result['trackable']);
        $this->assertSame(100, $result['score']);
        $this->assertSame([], $result['misses']);
    }

    public function test_compute_edq_all_on_time_scores_100(): void {
        $this->resetAfterTest();
        $assignments = [$this->make_assignment(submitted: 10, overdue: 0, late: 0)];
        $result = local_edqscore_compute_edq($assignments, [], [], $this->make_thresholds());

        $this->assertSame(10, $result['trackable']);
        $this->assertSame(10, $result['ontime']);
        $this->assertSame(100, $result['score']);
    }

    public function test_compute_edq_weights_late_items_at_half_credit(): void {
        $this->resetAfterTest();
        // Overduecount is really "not on time" (late + truly-overdue combined — see
        // analytics.php, where $row->overdue is set whenever latestatus !== 'ontime'),
        // and latecount is the subset of that which is merely late rather than fully
        // overdue, so latecount <= overduecount always holds in real data. Here: 10
        // submitted, 4 not on time, 2 of those 4 are late (the other 2 are overdue).
        // True on-time count = 10 - 4 = 6. Adding the late subset back at half credit:
        // ontime = (10 - 4) + (2 * 0.5) = 6 + 1 = 7 -> score = round(7/10 * 100) = 70.
        $assignments = [$this->make_assignment(submitted: 10, overdue: 4, late: 2)];
        $result = local_edqscore_compute_edq($assignments, [], [], $this->make_thresholds());

        $this->assertSame(10, $result['trackable']);
        $this->assertEqualsWithDelta(7.0, $result['ontime'], 0.001);
        $this->assertSame(70, $result['score']);
    }

    public function test_compute_edq_score_color_thresholds(): void {
        $this->resetAfterTest();
        // Score of 90 or more -> good.
        $good = local_edqscore_compute_edq(
            [$this->make_assignment(submitted: 100, overdue: 5, late: 0)],
            [],
            [],
            $this->make_thresholds()
        );
        $this->assertSame(95, $good['score']);
        $this->assertSame('var(--edq-hero-good)', $good['color']);

        // 70-89 -> warn.
        $warn = local_edqscore_compute_edq(
            [$this->make_assignment(submitted: 100, overdue: 20, late: 0)],
            [],
            [],
            $this->make_thresholds()
        );
        $this->assertSame(80, $warn['score']);
        $this->assertSame('var(--edq-hero-warn)', $warn['color']);

        // Score under 70 -> bad.
        $bad = local_edqscore_compute_edq(
            [$this->make_assignment(submitted: 100, overdue: 50, late: 0)],
            [],
            [],
            $this->make_thresholds()
        );
        $this->assertSame(50, $bad['score']);
        $this->assertSame('var(--edq-hero-bad)', $bad['color']);
    }

    public function test_compute_edq_excludes_non_essay_quizzes_entirely(): void {
        $this->resetAfterTest();
        // A fully auto-graded quiz (hasessay=false) must never affect trackable/ontime at
        // all, regardless of its overdue/completed counts — it has no grading turnaround
        // to measure.
        $autograded = (object) [
            'name' => 'Auto quiz',
            'hasessay' => false,
            'completedcount' => 50,
            'overduecount' => 50,
            'latecount' => 0,
            'students' => [],
        ];
        $result = local_edqscore_compute_edq([], [], [$autograded], $this->make_thresholds());

        $this->assertSame(0, $result['trackable']);
        $this->assertSame(100, $result['score']);
    }

    public function test_compute_edq_respects_edqincludequizmanual_toggle(): void {
        $this->resetAfterTest();
        $essayquiz = (object) [
            'name' => 'Essay quiz',
            'hasessay' => true,
            'completedcount' => 10,
            'overduecount' => 5,
            'latecount' => 0,
            'students' => [],
        ];

        $included = local_edqscore_compute_edq([], [], [$essayquiz], $this->make_thresholds(includequizmanual: true));
        $this->assertSame(10, $included['trackable']);

        $excluded = local_edqscore_compute_edq([], [], [$essayquiz], $this->make_thresholds(includequizmanual: false));
        $this->assertSame(0, $excluded['trackable']);
    }
}

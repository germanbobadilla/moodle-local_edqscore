# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`local_edqscore` ("EdQ Score") is a Moodle **local plugin**: a per-course
grading-turnaround analytics dashboard. It tracks how quickly
assignments, forum posts and quiz attempts are graded or replied to,
rolls that into a single EdQ score, and breaks down exactly what's
pulling it down. It stores no activity data of its own — only reads core
Moodle tables (`assign_*`, `forum_*`, `quiz_*`, etc.) plus its own
per-course threshold-override table.

This directory (`public/local/edqscore`) is nested inside a full Moodle
core checkout at the repo root (one level up from `public/`). Build/lint
tooling (Grunt, phpcs) lives at that root, not in this plugin directory —
commands below assume you `cd` there first, then target this plugin with
a `--root` / path argument. This plugin directory has its own git remote
(`germanbobadilla/moodle-local_edqscore`) and its own commit history,
separate from the surrounding Moodle checkout. Pushes go straight to
`main` — there's no branch/PR flow in use so far.

Internal CSS/JS class names still say `mygama-*` throughout (e.g.
`mygama-toolbar`, `mygama-combo`) — a leftover from a "My GAMA" branding
predating the "EdQ Score" rename. Harmless; not worth a mass rename
unless you're already touching those files for another reason.

Copyright holder on every file header is German Bobadilla, MA
(`@copyright 2026 German Bobadilla, MA`) — copy that docblock verbatim
into any new file.

## Commands

Run from the Moodle root (one level above `public/`), not from inside
this plugin directory. Requires `npm install` at that root first (not
done in this checkout by default).

- **Build AMD JS** (after editing anything in `amd/src/`): `npx grunt
  amd --root=public/local/edqscore`. The committed `amd/build/*.min.js`
  files must stay in sync with `amd/src/` — always rebuild before
  committing a JS change.
- **PHP coding standard** (Moodle's `phpcs.xml.dist` at the root
  references the `moodle` ruleset): `vendor/bin/phpcs
  public/local/edqscore` (needs `composer install` first).
- **Install/upgrade the plugin** against a running Moodle site: `php
  admin/cli/upgrade.php`, or via *Site administration > Notifications* in
  the browser.
- There is currently no PHPUnit test suite for this plugin (no `tests/`
  directory).

## Architecture

**Request flow, one controller per page** — `course.php`,
`assignments.php`, `forums.php`, `quizzes.php`, `edqmisses.php`,
`coursesettings.php`. Each follows the same shape: resolve the viewing
teacher's *scope* → resolve effective *thresholds* → query
`classes/analytics.php` → filter → hand off to a
`classes/output/*_page.php` renderable → render via the matching
`templates/*.mustache`. When adding a new detail page, copy this
pipeline rather than inventing a new shape.

**`lib.php`** is the shared core, used by every controller and by the
scheduled tasks:
- `local_edqscore_get_teaching_scope()` — who a given user can see (own
  group(s), or the whole course for managers/anyone with
  `moodle/site:accessallgroups`), and which groups they can filter by.
- `local_edqscore_get_course_thresholds()` — per-course override merged
  over site-wide defaults, one field at a time (NULL override field =
  fall through to site default).
- `local_edqscore_resolve_countfrom()` / `local_edqscore_grading_status()`
  — turn a due/submission/cutoff timestamp into ontime/late/overdue,
  given the configured "count from" mode and thresholds.
- `local_edqscore_compute_edq()` — the actual score: weighted by raw
  counts (not an average of percentages), full credit for on-time,
  partial for late, zero for overdue. Shared by `course.php` (the ring)
  and `edqmisses.php` (the full breakdown) so they can't drift apart, and
  reused by the digest task for the same reason.

**`classes/analytics.php`** holds one static method per activity type
(`get_assignments()`, `get_forums()`, `get_quizzes()`,
`get_recent_submissions()`), each doing its own direct SQL against core
Moodle tables and returning "one card per activity" with a nested
per-student (or per-discussion, for forums) detail list. Forums have two
different definitions of "overdue" depending on whether the forum is
graded (rated) or not — see the docblock there before changing that
logic.

**Capabilities** (`db/access.php`): `local/edqscore:view` (teacher/
editingteacher/manager), `local/edqscore:manage` (site-wide defaults,
manager-only, deliberately separate from `moodle/site:config`),
`local/edqscore:configurecourse` (per-course override, **manager-only** —
editing teachers can view the board but not change the thresholds it's
measured against; see `memory.md` for why this changed from the
original `editingteacher`-included default). There's no forced custom
role — a site wanting a broader "Program Director"-style role creates one
itself via *Define roles* (README documents which capabilities to grant
it).

**Schema/upgrades** (`db/upgrade.php`, `db/install.xml`): the plugin has
exactly one table, `local_edqscore_course_settings`, one row per course
with per-course override columns (NULL = use site default). Every new
override field needs: a column in `db/install.xml`, an `add_field()` step
in `db/upgrade.php` guarded by `$oldversion <` + a matching
`upgrade_plugin_savepoint()`, a bump to `$plugin->version` in
`version.php`, and read/write handling in `lib.php`'s
`local_edqscore_get_course_thresholds()` + `coursesettings.php`'s
submit handler.

**Output layer**: `classes/output/*_page.php` are `renderable` +
`templatable` classes whose `export_for_template()` shapes data for the
matching Mustache template — no markup in PHP. `classes/output/helper.php`
holds cross-page export logic (the toolbar, the rich combo-select
dropdown, the on-time/late/overdue status pill) so every page reuses the
same shape instead of duplicating it.

**Scheduled tasks** (`classes/task/`): two daily digests
(`send_edqscore_digest`, `send_submission_digest`), each iterating every
visible course and every instructor with `local/edqscore:view`, reusing
the same scope/threshold/analytics pipeline as the dashboard so the email
can never show something different from what the instructor would see by
clicking in.

## Conventions

Comments in this codebase explain *why*, liberally — non-obvious business
rules (e.g. "graded late still counts as overdue, not late, once it
crosses X hours") get a docblock note even where the code itself is
short. Match that existing house style rather than stripping comments
down to nothing.

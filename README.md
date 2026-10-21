# EdQ Score

**EdQ Score** (Education Quality Score) is a Moodle local plugin that gives
instructors a per-course grading-turnaround dashboard — how quickly
assignments, forum posts and quiz attempts are being graded or replied to,
rolled up into a single EdQ score, with a breakdown of exactly what's
pulling it down.

## Description

EdQ Score adds a teaching-analytics board to every course. For the
students in an instructor's scope (their own group(s), or the whole
course for editing teachers/managers), it tracks:

* **Assignments** — submission/grading turnaround, feedback given, overdue items.
* **Forums** — post/reply turnaround, instructor participation.
* **Quizzes** — grading turnaround on attempts containing essay questions.

These roll up into a single **EdQ score**, plus a "Needs your attention"
page listing the specific misses (not graded, graded late, no feedback, no
instructor reply) behind it.

Grading-turnaround thresholds (how many hours before an item counts as
"late" or "overdue") are configurable site-wide, with optional per-course
overrides — including what date each turnaround clock starts counting
from (submission, due date, or cut-off date).

Two optional daily digest notifications, delivered through Moodle's own
notification system, can nudge instructors: one summarising their current
EdQ score and top misses per course, another listing new assignment
submissions from the last 24 hours.

## Requirements

* Fully supported and tested on **Moodle 4.5 LTS, 5.0, 5.2 and 5.3 LTS**.
* Also downloadable and installable on **Moodle 4.0 and 4.2**
  (`$plugin->requires = 2022041900`, Moodle 4.0's base version — the floor
  the quiz essay-detection query's question-bank schema requires).

## Installation

Install the plugin like any other Moodle local plugin, in this case in
`local/edqscore`:

```sh
git clone https://github.com/germanbobadilla/moodle-local_edqscore.git local/edqscore
```

Then log in as an admin and visit *Site administration > Notifications*
to complete the installation, or run:

```sh
php admin/cli/upgrade.php
```

See <https://docs.moodle.org/en/Installing_plugins> for more general
installation help.

## Usage

Once installed, teachers and editing teachers see an **EdQ Score** link in
the course navigation. Users with `local/edqscore:manage` can set
site-wide defaults under *Site administration > Plugins > Local plugins >
EdQ Score*; users with `local/edqscore:configurecourse` (managers, by
default) can override those defaults per course from the dashboard's
*Course settings* page. Editing teachers can view the board but not
change the thresholds it's measured against.

Sites that want a "Program Director" style role — full visibility and
threshold control across a program without full site administration
access — can create one via *Site administration > Users > Permissions >
Define roles* and grant it `local/edqscore:view`, `local/edqscore:manage`,
`local/edqscore:configurecourse`, and (to see across all groups)
`moodle/site:accessallgroups`. The plugin doesn't create this role for
you, since not every site organises staff this way.

## Settings

Site-wide defaults live under *Site administration > Plugins > Local
plugins > EdQ Score* (`local/edqscore:manage`). Every threshold below can
also be overridden per course from the dashboard's *Course settings* page
(`local/edqscore:configurecourse`) — a course override replaces the site
default for that course only; leaving a course-level field blank falls
back to the site default.

**General**

| Setting | Default | Description |
| --- | --- | --- |
| Show ungraded items | Off | Items with no grading configured at all (e.g. a general discussion forum, or an assignment/quiz with no grade) are hidden from the board by default, so instructors only see what actually needs grading. |
| Include feedback on assignments for the EdQ | On | Counts whether feedback text was left on each *submitted* assignment as its own trackable/on-time pair in the EdQ score, alongside grading turnaround. |
| Include manually graded quizzes in the EdQ | On | Counts grading turnaround on quizzes containing an essay question towards the EdQ score. Fully auto-graded quizzes never count, regardless of this setting — there's no manual grading action to measure. |

**Assignments / Forums / Quizzes** (one group per activity type)

| Setting | Default | Description |
| --- | --- | --- |
| *(Activity)* grading hours | 48h (assignments/quizzes), 72h (forums) | Hours an item can go ungraded/unreplied-to before it stops counting as "On time". |
| *(Activity)* considered late after | 120h | Hours past the grading-hours threshold before an item flips from "Late" to "Overdue" — applies even to work that's since been handled; grading it late doesn't cap it back at "Late". |
| Count *(activity)* turnaround from | Submission date | The anchor the turnaround clock starts from: submission date, due date, or due date falling back to submission date if none is set. Assignments additionally offer the cut-off date (the hard deadline after which Moodle refuses further submissions), with its own fallback-to-due-date option. |

Assignments also have **Only show submitted work** (default off): hides
students who haven't submitted from the detail list (they're still
counted in the totals). Only takes effect when assignments are tracked by
submission date — when tracking by due date, seeing who hasn't submitted
yet is the point, so the setting is ignored (but kept, and re-applies if
you switch back). Quizzes also have **Show only manually graded quizzes**
(default on): hides fully auto-graded quizzes from the board, since
there's nothing for the instructor to do on those.

**Notifications**

| Setting | Default | Description |
| --- | --- | --- |
| Send EdQ Score digest | On | Daily notification summarising an instructor's current EdQ score and top misses, per course. |
| Send assignment submission digest | On | Daily notification listing new assignment submissions from the last 24 hours. |

## Capabilities

| Capability | Description |
| --- | --- |
| `local/edqscore:view` | View the EdQ Score board for a course. |
| `local/edqscore:manage` | Edit the site-wide default thresholds. |
| `local/edqscore:configurecourse` | Override thresholds for one course. |

## Support

Please use the [GitHub issue tracker](https://github.com/germanbobadilla/moodle-local_edqscore/issues)
to report bugs or request features.

## License

This program is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
[GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html)
for more details.

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

These roll up into a single **EdQ score**, plus a "What's affecting your
EdQ score" page listing the specific misses (not graded, graded late, no
feedback, no instructor reply) behind it.

Grading-turnaround thresholds (how many hours before an item counts as
"late" or "overdue") are configurable site-wide, with optional per-course
overrides — including what date each turnaround clock starts counting
from (submission, due date, or cut-off date).

Two optional daily digest notifications, delivered through Moodle's own
notification system, can nudge instructors: one summarising their current
EdQ score and top misses per course, another listing new assignment
submissions from the last 24 hours.

## Requirements

* Moodle 4.5 or later (`$plugin->requires = 2024100700`).

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
EdQ Score*; users with `local/edqscore:configurecourse` (editing teachers
and managers, by default) can override those defaults per course from the
dashboard's *Course settings* page.

A **Program Director** role is created automatically on install, scoped
to view/manage/configure the plugin plus `moodle/site:accessallgroups`
and `moodle/course:viewparticipants`, for staff who need visibility
across a program without full site administration access.

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

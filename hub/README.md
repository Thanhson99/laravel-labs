# Laravel Labs Hub

Laravel practice workspace for the Laravel Labs learning portal. The goal of this app is learning by building: open a native practice exercise, code inside this Laravel project, run verification, then use the JSON-backed tools only as reference.

The primary app experience is code-first practice:

- Blade dashboard: `/`
- Native practice workspace: `/practice`
- Practice exercise detail: `/practice/{exercise}`
- Content-to-practice map: `/practice/content-map`
- Content-backed drill: `/practice/content-drill`
- Question drill set: `/practice/question-drills`
- Technology practice board: `/practice/technology-board`
- Technology practice matrix: `/practice/technology-matrix`
- Source practice pack index: `/practice/source-packs`
- Source practice pack: `/practice/source-packs/{sourceKey}`
- Implementation blueprint: `/practice/implementation-blueprint`
- Guided implementation checklist: `/practice/guided-checklist`
- Implementation starter kit: `/practice/starter-kit`
- Record practice workspace: `/practice/record-workspace`
- Practice queue: `/practice/queue`
- Content practice syllabus: `/practice/syllabus`
- Practice sprint: `/practice/sprint`
- Practice TDD lab: `/practice/tdd-lab`
- Practice review lab: `/practice/review-lab`
- Practice remediation lab: `/practice/remediation-lab`
- Practice pull request lab: `/practice/pull-request-lab`
- Practice assessment lab: `/practice/assessment-lab`
- Practice retrospective lab: `/practice/retrospective-lab`
- Practice portfolio lab: `/practice/portfolio-lab`
- Practice capstone lab: `/practice/capstone-lab`
- Practice mentor feedback lab: `/practice/mentor-feedback-lab`
- Practice checkpoint exam lab: `/practice/checkpoint-exam-lab`
- Practice mastery path lab: `/practice/mastery-path-lab`
- Practice rotation lab: `/practice/rotation-lab`
- Practice weekly report lab: `/practice/weekly-report-lab`
- Practice demo script lab: `/practice/demo-script-lab`
- Practice live coding lab: `/practice/live-coding-lab`
- Practice bug-fix lab: `/practice/bug-fix-lab`
- Practice refactor lab: `/practice/refactor-lab`
- Practice release readiness lab: `/practice/release-readiness-lab`
- Practice interview defense lab: `/practice/interview-defense-lab`
- Practice knowledge gap lab: `/practice/knowledge-gap-lab`
- Practice spaced repetition lab: `/practice/spaced-repetition-lab`
- Practice mastery evidence lab: `/practice/mastery-evidence-lab`
- Practice competency map lab: `/practice/competency-map-lab`
- Practice next challenge lab: `/practice/next-challenge-lab`
- Practice challenge execution lab: `/practice/challenge-execution-lab`
- Practice challenge evidence review lab: `/practice/challenge-evidence-review-lab`
- Practice challenge promotion lab: `/practice/challenge-promotion-lab`
- Practice next session handoff lab: `/practice/next-session-handoff-lab`
- Practice session replay lab: `/practice/session-replay-lab`
- Practice session debrief lab: `/practice/session-debrief-lab`
- Practice session archive lab: `/practice/session-archive-lab`
- Practice archive retrieval lab: `/practice/archive-retrieval-lab`
- Practice evidence reuse plan lab: `/practice/evidence-reuse-plan-lab`
- Implementation verification plan: `/practice/verification-plan`
- Daily practice session: `/practice-sessions/today`
- Thin-controller practice note: `/practice-notes/thin-controller`
- Runnable workbench example: `/workbench/name-normalizer`
- Practice API: `/api/practice`
- Content-to-practice API: `GET /api/practice/content-map`
- Content-backed drill API: `GET /api/practice/content-drill`
- Question drill set API: `GET /api/practice/question-drills`
- Technology board API: `GET /api/practice/technology-board`
- Technology matrix API: `GET /api/practice/technology-matrix`
- Source pack index API: `GET /api/practice/source-packs`
- Source practice pack API: `GET /api/practice/source-packs/{sourceKey}`
- Implementation blueprint API: `GET /api/practice/implementation-blueprint`
- Guided checklist API: `GET /api/practice/guided-checklist`
- Starter kit API: `GET /api/practice/starter-kit`
- Record workspace API: `GET /api/practice/record-workspace`
- Practice queue API: `GET /api/practice/queue`
- Content practice syllabus API: `GET /api/practice/syllabus`
- Practice sprint API: `GET /api/practice/sprint`
- Practice TDD lab API: `GET /api/practice/tdd-lab`
- Practice review lab API: `GET /api/practice/review-lab`
- Practice remediation lab API: `GET /api/practice/remediation-lab`
- Practice pull request lab API: `GET /api/practice/pull-request-lab`
- Practice assessment lab API: `GET /api/practice/assessment-lab`
- Practice retrospective lab API: `GET /api/practice/retrospective-lab`
- Practice portfolio lab API: `GET /api/practice/portfolio-lab`
- Practice capstone lab API: `GET /api/practice/capstone-lab`
- Practice mentor feedback lab API: `GET /api/practice/mentor-feedback-lab`
- Practice checkpoint exam lab API: `GET /api/practice/checkpoint-exam-lab`
- Practice mastery path lab API: `GET /api/practice/mastery-path-lab`
- Practice rotation lab API: `GET /api/practice/rotation-lab`
- Practice weekly report lab API: `GET /api/practice/weekly-report-lab`
- Practice demo script lab API: `GET /api/practice/demo-script-lab`
- Practice live coding lab API: `GET /api/practice/live-coding-lab`
- Practice bug-fix lab API: `GET /api/practice/bug-fix-lab`
- Practice refactor lab API: `GET /api/practice/refactor-lab`
- Practice release readiness lab API: `GET /api/practice/release-readiness-lab`
- Practice interview defense lab API: `GET /api/practice/interview-defense-lab`
- Practice knowledge gap lab API: `GET /api/practice/knowledge-gap-lab`
- Practice spaced repetition lab API: `GET /api/practice/spaced-repetition-lab`
- Practice mastery evidence lab API: `GET /api/practice/mastery-evidence-lab`
- Practice competency map lab API: `GET /api/practice/competency-map-lab`
- Practice next challenge lab API: `GET /api/practice/next-challenge-lab`
- Practice challenge execution lab API: `GET /api/practice/challenge-execution-lab`
- Practice challenge evidence review lab API: `GET /api/practice/challenge-evidence-review-lab`
- Practice challenge promotion lab API: `GET /api/practice/challenge-promotion-lab`
- Practice next session handoff lab API: `GET /api/practice/next-session-handoff-lab`
- Practice session replay lab API: `GET /api/practice/session-replay-lab`
- Practice session debrief lab API: `GET /api/practice/session-debrief-lab`
- Practice session archive lab API: `GET /api/practice/session-archive-lab`
- Practice archive retrieval lab API: `GET /api/practice/archive-retrieval-lab`
- Practice evidence reuse plan lab API: `GET /api/practice/evidence-reuse-plan-lab`
- Verification plan API: `GET /api/practice/verification-plan`
- Validated practice topic API: `POST /api/practice/topics`
- Quality-gate practice API: `POST /api/practice/quality-gate`
- Runtime smoke-check API: `GET /api/practice/runtime-smoke-check`
- Session-plan API: `GET /api/practice/session-plan`
- Progress checklist API: `POST /api/practice/progress-checklist`
- Practice labs: `/labs`
- Searchable question bank: `/questions`
- Source viewer: `/sources/{sourceKey}`
- Practice quiz: `/quiz`
- Study plan: `/study-plan`
- Content analytics: `/analytics`
- JSON API: `/api/learning`

## Learning Flow

1. Open `/labs` and choose a technology track.
1. Open `/practice` and choose a native exercise.
2. Build the suggested task inside this Laravel project.
3. For exercises with a workbench, open the runnable page and modify the app code.
4. Run the listed commands and tests.
5. Use `/quiz` or `/questions` only after coding, as reference/review.

Current native practice implementations:

- `GET /practice/content-map` maps JSON content/question records to concrete Laravel practice tasks.
- `GET /practice/content-drill` turns one JSON content/question record into a focused coding drill.
- `GET /practice/question-drills` turns filtered question records into drill cards with exact record links.
- `GET /practice/technology-board` groups one technology's records by source file and links to packs/workspaces.
- `GET /practice/technology-matrix` groups content/question records by technology and links each group to exercises/drills.
- `GET /practice/source-packs` lists JSON files with record counts, technologies, and practice links.
- `GET /practice/source-packs/{sourceKey}` turns one JSON file into technology paths, sample drills, workflow, and commands.
- `GET /practice/implementation-blueprint` turns one content/question record into route, class, file, and test names.
- `GET /practice/guided-checklist` turns an implementation blueprint into TDD steps and progress payload.
- `GET /practice/starter-kit` turns a guided checklist into starter snippets for Laravel files.
- `GET /practice/record-workspace` composes source, drill, blueprint, checklist, and snippets for one JSON record.
- `GET /practice/queue` turns filtered question records into ordered record-workspace tasks.
- `GET /practice/syllabus` composes technologies, source packs, exercises, boards, and queues into one practice-first learning path.
- `GET /practice/sprint` turns syllabus phases into a short set of workspace tasks with verification links.
- `GET /practice/tdd-lab` turns one record into Red-Green-Refactor steps with snippets and verification data.
- `GET /practice/review-lab` turns one TDD lab into a route, validation, controller, service, test, and verification review checklist.
- `GET /practice/remediation-lab` turns review findings into concrete fix tasks with verification commands.
- `GET /practice/pull-request-lab` turns one remediated implementation into branch, commit, PR summary, changed files, and verification artifacts.
- `GET /practice/assessment-lab` turns one PR lab into a 100-point self-assessment rubric with evidence.
- `GET /practice/retrospective-lab` turns one assessment into wins, weak spots, next actions, and follow-up lab links.
- `GET /practice/portfolio-lab` turns one retrospective into a portfolio entry with source, skills, evidence, and writeup template.
- `GET /practice/capstone-lab` turns a technology board and queue into a mini-project with deliverables.
- `GET /practice/mentor-feedback-lab` turns one capstone into mentor-style feedback, risks, questions, and action items.
- `GET /practice/checkpoint-exam-lab` turns mentor feedback into a timed practice exam with coding tasks and pass criteria.
- `GET /practice/mastery-path-lab` turns syllabus phases into a multi-technology practice path.
- `GET /practice/rotation-lab` turns a mastery path into a day-by-day practice schedule.
- `GET /practice/weekly-report-lab` turns a rotation into a weekly progress report.
- `GET /practice/demo-script-lab` turns a weekly report into a code demo script with verification evidence.
- `GET /practice/live-coding-lab` turns a demo script into timed live coding rounds with scorecard and recovery notes.
- `GET /practice/bug-fix-lab` turns live coding rounds into bug-fix drills with diagnosis steps and patch targets.
- `GET /practice/refactor-lab` turns bug-fix drills into safe refactor tasks with architecture guardrails.
- `GET /practice/release-readiness-lab` turns refactor tasks into release notes, smoke checks, rollback notes, and handoff evidence.
- `GET /practice/interview-defense-lab` turns release evidence into technical defense cards with answer outlines and scoring rubric.
- `GET /practice/knowledge-gap-lab` turns weak defense answers into coding actions, evidence rechecks, and next-session plans.
- `GET /practice/spaced-repetition-lab` turns knowledge gaps into day 1, day 3, and day 7 coding review checkpoints.
- `GET /practice/mastery-evidence-lab` turns repeated checkpoints into mastery evidence cards with scores and next harder labs.
- `GET /practice/competency-map-lab` turns mastery evidence into a capability map with levels and next actions.
- `GET /practice/next-challenge-lab` turns competency maps into the next recommended Laravel challenge route.
- `GET /practice/challenge-execution-lab` turns next challenge cards into executable practice steps with route, command, evidence, and exit criteria.
- `GET /practice/challenge-evidence-review-lab` turns executable challenge steps into evidence review cards with questions, pass signals, risk checks, and follow-up actions.
- `GET /practice/challenge-promotion-lab` turns evidence review cards into promote-or-repeat decisions with proof checklist, repeat triggers, and next routes.
- `GET /practice/next-session-handoff-lab` turns promotion decisions into next-session handoff cards with route, preflight checklist, coding focus, and done evidence.
- `GET /practice/session-replay-lab` turns next-session handoff cards into replay rounds with before-check, coding run, after-check, evidence capture, and retry policy.
- `GET /practice/session-debrief-lab` turns replay rounds into debrief cards with result notes, lesson prompts, blocker checks, and next actions.
- `GET /practice/session-archive-lab` turns debrief cards into archive entries with proof bundle, learning summary, blocker status, retrieval tags, and next reference.
- `GET /practice/archive-retrieval-lab` turns archive entries into retrieval cards for portfolio, interview defense, and review reuse.
- `GET /practice/evidence-reuse-plan-lab` turns retrieval cards into concrete portfolio, interview, and review reuse tasks.
- `GET /practice/verification-plan` creates focused commands, smoke request, and quality-gate payload for one record workspace.
- `App\Practice\Php\NameNormalizer` for typed PHP function practice.
- `GET /practice-notes/thin-controller` for route, controller, service, and Blade practice.
- `POST /api/practice/name-normalizer` for API input/output practice.
- `POST /api/practice/topics` for Form Request validation and JSON response practice.
- `POST /api/practice/quality-gate` for service tests, API tests, and verification workflow practice.
- `GET /api/practice/runtime-smoke-check` for Docker/runtime configuration practice.
- `GET /api/practice/session-plan` for service composition, filtering, web/API controller, and Blade practice.
- `POST /api/practice/progress-checklist` for nested validation and state-transition service practice.
- `GET /api/practice/content-map` for content-aware technology mapping practice.
- `GET /api/practice/content-drill` for source-specific files, steps, commands, and acceptance criteria.
- `GET /api/practice/question-drills` for question-backed drill card generation.
- `GET /api/practice/technology-board` for technology-focused source grouping.
- `GET /api/practice/technology-matrix` for content coverage grouped by technology.
- `GET /api/practice/source-packs` for source-file practice pack discovery.
- `GET /api/practice/source-packs/{sourceKey}` for source-file-level practice packs.
- `GET /api/practice/implementation-blueprint` for record-specific implementation naming.
- `GET /api/practice/guided-checklist` for guided TDD implementation steps.
- `GET /api/practice/starter-kit` for starter code snippets tied to one JSON record.
- `GET /api/practice/record-workspace` for the complete source-to-code workspace.
- `GET /api/practice/queue` for queueing multiple record workspaces with progress payload.
- `GET /api/practice/syllabus` for a technology and source-pack practice syllabus.
- `GET /api/practice/sprint` for a short content-backed implementation sprint.
- `GET /api/practice/tdd-lab` for a Red-Green-Refactor lab from one content record.
- `GET /api/practice/review-lab` for a layer-by-layer Laravel review checklist.
- `GET /api/practice/remediation-lab` for concrete fix tasks after a review lab.
- `GET /api/practice/pull-request-lab` for PR draft artifacts after remediation.
- `GET /api/practice/assessment-lab` for self-assessment rubric data.
- `GET /api/practice/retrospective-lab` for retrospective prompts and next actions.
- `GET /api/practice/portfolio-lab` for a reusable portfolio entry artifact.
- `GET /api/practice/capstone-lab` for a technology-level capstone mini-project.
- `GET /api/practice/mentor-feedback-lab` for mentor-style feedback on a capstone.
- `GET /api/practice/checkpoint-exam-lab` for a timed technology checkpoint exam.
- `GET /api/practice/mastery-path-lab` for a multi-technology mastery path.
- `GET /api/practice/rotation-lab` for a day-by-day practice schedule.
- `GET /api/practice/weekly-report-lab` for weekly report data from a rotation.
- `GET /api/practice/demo-script-lab` for demo script data from a weekly report.
- `GET /api/practice/live-coding-lab` for timed live coding session data from a demo script.
- `GET /api/practice/bug-fix-lab` for bug-fix drill data from live coding rounds.
- `GET /api/practice/refactor-lab` for refactor task data from bug-fix drills.
- `GET /api/practice/release-readiness-lab` for release readiness data from refactor tasks.
- `GET /api/practice/interview-defense-lab` for technical defense cards from release evidence.
- `GET /api/practice/knowledge-gap-lab` for knowledge-gap cards from interview defense output.
- `GET /api/practice/spaced-repetition-lab` for repeated coding review checkpoints from knowledge gaps.
- `GET /api/practice/mastery-evidence-lab` for mastery evidence cards from repeated checkpoints.
- `GET /api/practice/competency-map-lab` for competency cards from mastery evidence.
- `GET /api/practice/next-challenge-lab` for next challenge recommendations from competency maps.
- `GET /api/practice/challenge-execution-lab` for executable challenge steps from next challenge cards.
- `GET /api/practice/challenge-evidence-review-lab` for evidence review cards from executable challenge steps.
- `GET /api/practice/challenge-promotion-lab` for promote-or-repeat decisions from evidence review cards.
- `GET /api/practice/next-session-handoff-lab` for next-session handoff cards from promotion decisions.
- `GET /api/practice/session-replay-lab` for replay rounds from next-session handoff cards.
- `GET /api/practice/session-debrief-lab` for debrief cards from replay rounds.
- `GET /api/practice/session-archive-lab` for archive entries from debrief cards.
- `GET /api/practice/archive-retrieval-lab` for retrieval cards from archive entries.
- `GET /api/practice/evidence-reuse-plan-lab` for reuse plans from retrieval cards.
- `GET /api/practice/verification-plan` for verification commands and smoke-check data.

## Run With PHP

```powershell
composer install
$env:SESSION_DRIVER="file"
$env:CACHE_STORE="file"
$env:QUEUE_CONNECTION="sync"
$env:HUB_PORT="8088"
php artisan serve --host=127.0.0.1 --port=$env:HUB_PORT
```

Open:

```text
http://127.0.0.1:{HUB_PORT}
```

## Run With Docker

This machine has standalone `docker-compose`, so use:

```powershell
docker-compose up --build
```

Open:

```text
http://localhost:{HUB_PORT}
```

Set these in `.env` to change the exposed or container port without editing Docker files:

```text
HUB_PORT=8088
HUB_INTERNAL_PORT=8088
HUB_RUNTIME_BASE_URL=http://localhost:8088
```

The Docker container mounts `../data` read-only at `/var/www/data` and sets:

```text
HUB_INTERNAL_PORT=8088
HUB_RUNTIME_BASE_URL=http://localhost:8088
LABS_CONTENT_PATH=/var/www/data
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

## Verify

```powershell
$env:CACHE_STORE="file"
$env:SESSION_DRIVER="file"
php artisan test
vendor\bin\pint --test
docker-compose config
```

If an existing local `.env` was generated before the hub defaults were updated, make sure these values are file-backed:

```text
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

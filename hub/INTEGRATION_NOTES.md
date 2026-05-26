# Laravel Labs Hub Integration Notes

This file tracks what has been integrated into the aggregate Laravel project.

## Product Direction

The hub is a learning-by-building Laravel app. Native practice exercises should be the primary experience. JSON content from `../data/**` is reference material and can generate extra prompts, but the project itself is where the learner codes.

- open a native practice exercise
- code the task inside this Laravel app
- run commands/tests
- review JSON references or quiz yourself after implementation

## Done

- Created `hub/` as the aggregate Laravel application.
- Installed Laravel framework through Composer.
- Added Blade pages for:
  - dashboard
  - JSON source list
  - searchable question bank
  - individual JSON source inspection
- Added a repository contract and JSON repository implementation.
- Integrated all JSON files under `../data/**` as the read-only content source.
- Extracted question-like content from:
  - `questions`
  - `items`
  - `phases[*].topics`
  - `phases[*].examples`
  - `sections[*].items`
- Preserved bilingual EN/VI content by reading language suffixes from file names.
- Rendered code snippets from JSON content in escaped `<pre><code>` blocks.
- Added JSON API routes under `/api/learning`.
- Added Docker support with `Dockerfile`, `docker-compose.yml`, and a container entrypoint.
- Added a service layer for quiz generation through `LearningQuizService`.
- Added practice mode UI at `/quiz`.
- Added quiz API at `/api/learning/quiz`.
- Added `LearningStudyPlanService` for source/topic-level study plans.
- Added study-plan UI at `/study-plan`.
- Added study-plan API at `/api/learning/study-plan`.
- Added `LearningAnalyticsService` for source density and content coverage reporting.
- Added analytics UI at `/analytics`.
- Added analytics API at `/api/learning/analytics`.
- Added `LearningLabService` to turn content topics into hands-on labs.
- Added labs UI at `/labs`.
- Added labs API at `/api/learning/labs`.
- Added native practice catalog in `config/practice.php`.
- Added primary practice workspace at `/practice`.
- Added practice API at `/api/practice`.
- Added runnable name-normalizer workbench at `/workbench/name-normalizer`.
- Added native practice implementation in `App\Practice\Php\NameNormalizer`.
- Added validated practice topic API at `/api/practice/topics`.
- Added `StorePracticeTopicRequest`, `PracticeTopicController`, and `PracticeTopicApiTest` as a complete API + validation practice slice.
- Added thin-controller practice note page at `/practice-notes/thin-controller`.
- Added `PracticeNoteService`, `NoteController`, `practice.note` Blade view, and `PracticeNoteTest` as a complete Laravel HTTP practice slice.
- Added quality-gate practice API at `/api/practice/quality-gate`.
- Added `PracticeQualityGateService`, `EvaluateQualityGateRequest`, `PracticeQualityGateController`, unit tests, and API feature tests as a complete Testing + Quality practice slice.
- Removed the default unit example test and replaced it with behavior-focused practice tests.
- Renamed the default feature example test to `DashboardPracticeTest`.
- Added runtime smoke-check API at `/api/practice/runtime-smoke-check`.
- Added `RuntimeSmokeCheckService`, `RuntimeSmokeCheckController`, unit tests, and API feature tests as a complete Docker + Runtime practice slice.
- Renamed `PracticeWorkbenchTest` to `NameNormalizerWorkbenchTest`.
- Added daily practice session page at `/practice-sessions/today`.
- Added session-plan API at `/api/practice/session-plan`.
- Added `PracticeSessionPlannerService`, web/API session controllers, Blade session view, and feature tests as a Laravel service-composition practice slice.
- Renamed API session controller to `PracticeSessionPlanController`.
- Added progress checklist API at `/api/practice/progress-checklist`.
- Added `PracticeProgressChecklistService`, `SummarizePracticeProgressRequest`, `PracticeProgressChecklistController`, unit tests, and API feature tests as a state-transition practice slice.
- Renamed learning reference tests to `LearningReferencePagesTest` and `LearningContentApiTest`.
- Added content-to-practice map page at `/practice/content-map`.
- Added content-to-practice map API at `/api/practice/content-map`.
- Added `ContentPracticeMapperService`, web/API controllers, Blade view, and feature tests to map JSON content/question records to native Laravel practice tasks by technology.
- Added content-backed drill page at `/practice/content-drill`.
- Added content-backed drill API at `/api/practice/content-drill`.
- Added `ContentPracticeDrillService`, web/API controllers, Blade view, and feature tests to turn one JSON record into files, implementation steps, commands, and acceptance criteria.
- Added record-specific drill links from the quiz page.
- Added question drill set page at `/practice/question-drills`.
- Added question drill set API at `/api/practice/question-drills`.
- Added `QuestionDrillSetService`, web/API controllers, Blade view, and feature tests to turn filtered JSON question records into coding drill cards.
- Added technology practice matrix page at `/practice/technology-matrix`.
- Added technology practice matrix API at `/api/practice/technology-matrix`.
- Added `TechnologyPracticeMatrixService`, web/API controllers, Blade view, and feature tests to group content/question records by inferred technology and link them to native exercises and sample drills.
- Added technology practice board page at `/practice/technology-board`.
- Added technology practice board API at `/api/practice/technology-board`.
- Added `TechnologyPracticeBoardService`, web/API controllers, Blade view, and feature tests to group one technology's records by source file and link them to source packs, queues, and workspaces.
- Added source practice pack page at `/practice/source-packs/{sourceKey}`.
- Added source practice pack API at `/api/practice/source-packs/{sourceKey}`.
- Added `SourcePracticePackService`, web/API controllers, Blade view, and feature tests to turn one JSON source file into technology paths, sample drills, workflow, and commands.
- Added source practice pack index page at `/practice/source-packs`.
- Added source practice pack index API at `/api/practice/source-packs`.
- Added `SourcePracticePackIndexService`, web/API controllers, Blade view, and feature tests to list JSON source files with record counts, inferred technologies, and links to packs/workspaces.
- Added implementation blueprint page at `/practice/implementation-blueprint`.
- Added implementation blueprint API at `/api/practice/implementation-blueprint`.
- Added `ContentImplementationBlueprintService`, web/API controllers, Blade view, and feature tests to turn one JSON record into concrete route, class, file, and test names.
- Added guided implementation checklist page at `/practice/guided-checklist`.
- Added guided implementation checklist API at `/api/practice/guided-checklist`.
- Added `GuidedImplementationChecklistService`, web/API controllers, Blade view, and feature tests to turn a blueprint into TDD steps and a progress-checklist payload.
- Added implementation starter kit page at `/practice/starter-kit`.
- Added implementation starter kit API at `/api/practice/starter-kit`.
- Added `ImplementationStarterKitService`, web/API controllers, Blade view, and feature tests to turn a guided checklist into starter code snippets for tests, requests, controllers, and services.
- Added record practice workspace page at `/practice/record-workspace`.
- Added record practice workspace API at `/api/practice/record-workspace`.
- Added `RecordPracticeWorkspaceService`, web/API controllers, Blade view, and feature tests to compose source, drill, blueprint, checklist, and starter snippets for one JSON record.
- Added practice queue page at `/practice/queue`.
- Added practice queue API at `/api/practice/queue`.
- Added `PracticeQueueService`, web/API controllers, Blade view, and feature tests to turn filtered question records into ordered record-workspace tasks with estimated minutes and progress payload.
- Added content practice syllabus page at `/practice/syllabus`.
- Added content practice syllabus API at `/api/practice/syllabus`.
- Added `ContentPracticeSyllabusService`, web/API controllers, Blade view, and feature tests to compose technology phases, source packs, board links, queue links, and exercises into one code-first learning path.
- Added practice sprint page at `/practice/sprint`.
- Added practice sprint API at `/api/practice/sprint`.
- Added `PracticeSprintService`, web/API controllers, Blade view, and feature tests to turn syllabus phases into short queues of workspace tasks with verification-plan links.
- Added practice TDD lab page at `/practice/tdd-lab`.
- Added practice TDD lab API at `/api/practice/tdd-lab`.
- Added `PracticeTddLabService`, web/API controllers, Blade view, and feature tests to turn one content/question record into Red-Green-Refactor stages with snippets, smoke request, commands, and quality-gate payload.
- Added practice review lab page at `/practice/review-lab`.
- Added practice review lab API at `/api/practice/review-lab`.
- Added `PracticeReviewLabService`, web/API controllers, Blade view, and feature tests to turn one TDD lab into a layer-by-layer Laravel review checklist with progress payload.
- Added practice remediation lab page at `/practice/remediation-lab`.
- Added practice remediation lab API at `/api/practice/remediation-lab`.
- Added `PracticeRemediationLabService`, web/API controllers, Blade view, and feature tests to turn review checklist items into concrete fix tasks with files, actions, verification commands, and progress payload.
- Added practice pull request lab page at `/practice/pull-request-lab`.
- Added practice pull request lab API at `/api/practice/pull-request-lab`.
- Added `PracticePullRequestLabService`, web/API controllers, Blade view, and feature tests to turn remediation tasks into branch, commit, PR summary, changed files, verification evidence, and review checklist artifacts.
- Added practice assessment lab page at `/practice/assessment-lab`.
- Added practice assessment lab API at `/api/practice/assessment-lab`.
- Added `PracticeAssessmentLabService`, web/API controllers, Blade view, and feature tests to turn PR artifacts into a 100-point self-assessment rubric with evidence and progress payload.
- Added practice retrospective lab page at `/practice/retrospective-lab`.
- Added practice retrospective lab API at `/api/practice/retrospective-lab`.
- Added `PracticeRetrospectiveLabService`, web/API controllers, Blade view, and feature tests to turn assessment rubrics into wins, weak spots, next actions, next lab links, and progress payload.
- Added practice portfolio lab page at `/practice/portfolio-lab`.
- Added practice portfolio lab API at `/api/practice/portfolio-lab`.
- Added `PracticePortfolioLabService`, web/API controllers, Blade view, and feature tests to turn retrospective output into a reusable portfolio entry with source reference, practiced skills, evidence, writeup template, and next improvement.
- Added practice capstone lab page at `/practice/capstone-lab`.
- Added practice capstone lab API at `/api/practice/capstone-lab`.
- Added `PracticeCapstoneLabService`, web/API controllers, Blade view, and feature tests to turn one technology board and queue into a mini-project with source coverage, tasks, deliverables, artifact links, and progress payload.
- Added practice mentor feedback lab page at `/practice/mentor-feedback-lab`.
- Added practice mentor feedback lab API at `/api/practice/mentor-feedback-lab`.
- Added `PracticeMentorFeedbackLabService`, web/API controllers, Blade view, and feature tests to turn capstone tasks into mentor feedback, risks, questions, review focus, and action items.
- Added practice checkpoint exam lab page at `/practice/checkpoint-exam-lab`.
- Added practice checkpoint exam lab API at `/api/practice/checkpoint-exam-lab`.
- Added `PracticeCheckpointExamLabService`, web/API controllers, Blade view, and feature tests to turn mentor feedback into a timed exam with warmup questions, coding tasks, oral review, pass criteria, and progress payload.
- Added practice mastery path lab page at `/practice/mastery-path-lab`.
- Added practice mastery path lab API at `/api/practice/mastery-path-lab`.
- Added `PracticeMasteryPathLabService`, web/API controllers, Blade view, and feature tests to turn syllabus phases into multi-technology milestones with capstone, checkpoint, mentor feedback, source packs, and progress payload.
- Added practice rotation lab page at `/practice/rotation-lab`.
- Added practice rotation lab API at `/api/practice/rotation-lab`.
- Added `PracticeRotationLabService`, web/API controllers, Blade view, and feature tests to turn mastery paths into day-by-day practice schedules with lab links, required outputs, and progress payload.
- Added practice weekly report lab page at `/practice/weekly-report-lab`.
- Added practice weekly report lab API at `/api/practice/weekly-report-lab`.
- Added `PracticeWeeklyReportLabService`, web/API controllers, Blade view, and feature tests to turn rotations into weekly progress reports with daily outputs, evidence checklist, blockers, next week plan, and progress payload.
- Added practice demo script lab page at `/practice/demo-script-lab`.
- Added practice demo script lab API at `/api/practice/demo-script-lab`.
- Added `PracticeDemoScriptLabService`, web/API controllers, Blade view, and feature tests to turn weekly reports into presentation-ready demo scripts with speaker notes, actions, verification, evidence, and handoff payloads.
- Added practice live coding lab page at `/practice/live-coding-lab`.
- Added practice live coding lab API at `/api/practice/live-coding-lab`.
- Added `PracticeLiveCodingLabService`, web/API controllers, Blade view, and feature tests to turn demo scripts into timed live coding rounds with coding prompts, narration, verification commands, scorecard, recovery notes, and progress payload.
- Added practice bug-fix lab page at `/practice/bug-fix-lab`.
- Added practice bug-fix lab API at `/api/practice/bug-fix-lab`.
- Added `PracticeBugFixLabService`, web/API controllers, Blade view, and feature tests to turn live coding rounds into bug reports, diagnosis steps, patch targets, verification commands, pass signals, evidence, and review questions.
- Added practice refactor lab page at `/practice/refactor-lab`.
- Added practice refactor lab API at `/api/practice/refactor-lab`.
- Added `PracticeRefactorLabService`, web/API controllers, Blade view, and feature tests to turn bug-fix drills into safe refactor tasks with target files, safe steps, guardrails, verification commands, evidence, architecture checks, and progress payload.
- Added practice release readiness lab page at `/practice/release-readiness-lab`.
- Added practice release readiness lab API at `/api/practice/release-readiness-lab`.
- Added `PracticeReleaseReadinessLabService`, web/API controllers, Blade view, and feature tests to turn refactor tasks into release notes, smoke checks, rollback notes, verification evidence, handoff checklist, and progress payload.
- Added practice interview defense lab page at `/practice/interview-defense-lab`.
- Added practice interview defense lab API at `/api/practice/interview-defense-lab`.
- Added `PracticeInterviewDefenseLabService`, web/API controllers, Blade view, and feature tests to turn release evidence into technical defense questions, answer outlines, evidence to cite, follow-up risks, scoring rubric, and progress payload.
- Added practice knowledge gap lab page at `/practice/knowledge-gap-lab`.
- Added practice knowledge gap lab API at `/api/practice/knowledge-gap-lab`.
- Added `PracticeKnowledgeGapLabService`, web/API controllers, Blade view, and feature tests to turn interview defense cards into knowledge-gap cards, coding actions, review prompts, evidence rechecks, verification hints, next-session plan, and progress payload.
- Added practice spaced repetition lab page at `/practice/spaced-repetition-lab`.
- Added practice spaced repetition lab API at `/api/practice/spaced-repetition-lab`.
- Added `PracticeSpacedRepetitionLabService`, web/API controllers, Blade view, and feature tests to turn knowledge-gap cards into day 1, day 3, and day 7 coding review checkpoints with recall prompts, verification hints, promotion criteria, and progress payload.
- Added practice mastery evidence lab page at `/practice/mastery-evidence-lab`.
- Added practice mastery evidence lab API at `/api/practice/mastery-evidence-lab`.
- Added `PracticeMasteryEvidenceLabService`, web/API controllers, Blade view, and feature tests to turn spaced repetition checkpoints into mastery evidence cards with scores, proof items, missing evidence, next harder labs, and progress payload.
- Added practice competency map lab page at `/practice/competency-map-lab`.
- Added practice competency map lab API at `/api/practice/competency-map-lab`.
- Added `PracticeCompetencyMapLabService`, web/API controllers, Blade view, and feature tests to turn mastery evidence into competency levels, proof summaries, readiness, next actions, map summary, and progress payload.
- Added practice next challenge lab page at `/practice/next-challenge-lab`.
- Added practice next challenge lab API at `/api/practice/next-challenge-lab`.
- Added `PracticeNextChallengeLabService`, web/API controllers, Blade view, and feature tests to turn competency maps into challenge cards with recommended routes, verification commands, evidence requirements, challenge summary, and progress payload.
- Added practice challenge execution lab page at `/practice/challenge-execution-lab`.
- Added practice challenge execution lab API at `/api/practice/challenge-execution-lab`.
- Added `PracticeChallengeExecutionLabService`, web/API controllers, Blade view, and feature tests to turn next challenge cards into route-specific execution steps with commands, evidence, exit criteria, session summary, and progress payload.
- Added practice challenge evidence review lab page at `/practice/challenge-evidence-review-lab`.
- Added practice challenge evidence review lab API at `/api/practice/challenge-evidence-review-lab`.
- Added `PracticeChallengeEvidenceReviewLabService`, web/API controllers, Blade view, and feature tests to turn executable challenge steps into evidence review cards with review questions, pass signals, risk checks, follow-up actions, and progress payload.
- Added practice challenge promotion lab page at `/practice/challenge-promotion-lab`.
- Added practice challenge promotion lab API at `/api/practice/challenge-promotion-lab`.
- Added `PracticeChallengePromotionLabService`, web/API controllers, Blade view, and feature tests to turn evidence review cards into promote-or-repeat decisions with proof checklist, repeat triggers, next routes, learner notes, and progress payload.
- Added practice next session handoff lab page at `/practice/next-session-handoff-lab`.
- Added practice next session handoff lab API at `/api/practice/next-session-handoff-lab`.
- Added `PracticeNextSessionHandoffLabService`, web/API controllers, Blade view, and feature tests to turn promotion decisions into next-session handoff cards with route, preflight checklist, coding focus, done evidence, note prompts, and progress payload.
- Added practice session replay lab page at `/practice/session-replay-lab`.
- Added practice session replay lab API at `/api/practice/session-replay-lab`.
- Added `PracticeSessionReplayLabService`, web/API controllers, Blade view, and feature tests to turn next-session handoff cards into replay rounds with before-check, coding run, after-check, evidence capture, retry policy, and progress payload.
- Added practice session debrief lab page at `/practice/session-debrief-lab`.
- Added practice session debrief lab API at `/api/practice/session-debrief-lab`.
- Added `PracticeSessionDebriefLabService`, web/API controllers, Blade view, and feature tests to turn replay rounds into debrief cards with result notes, lesson prompts, blocker checks, next actions, and progress payload.
- Added practice session archive lab page at `/practice/session-archive-lab`.
- Added practice session archive lab API at `/api/practice/session-archive-lab`.
- Added `PracticeSessionArchiveLabService`, web/API controllers, Blade view, and feature tests to turn debrief cards into archive entries with proof bundle, learning summary, blocker status, retrieval tags, next reference, and progress payload.
- Added practice archive retrieval lab page at `/practice/archive-retrieval-lab`.
- Added practice archive retrieval lab API at `/api/practice/archive-retrieval-lab`.
- Added `PracticeArchiveRetrievalLabService`, web/API controllers, Blade view, and feature tests to turn archive entries into retrieval cards with search keys, retrieval prompts, reuse targets, proof quotes, refresh checks, and progress payload.
- Added practice evidence reuse plan lab page at `/practice/evidence-reuse-plan-lab`.
- Added practice evidence reuse plan lab API at `/api/practice/evidence-reuse-plan-lab`.
- Added `PracticeEvidenceReusePlanLabService`, web/API controllers, Blade view, and feature tests to turn retrieval cards into concrete portfolio, interview, and review reuse tasks with quality checks and progress payload.
- Added implementation verification plan page at `/practice/verification-plan`.
- Added implementation verification plan API at `/api/practice/verification-plan`.
- Added `ImplementationVerificationPlanService`, web/API controllers, Blade view, and feature tests to generate focused test commands, route checks, smoke request data, and quality-gate payloads for one JSON record.
- Expanded content-backed technology inference and starter snippets so JSON records can generate concrete code examples for API validation, authorization, Eloquent/database, file storage, async jobs/events, cache/performance, container bindings, realtime events, Blade UI, PHP, and default Laravel HTTP slices.
- Added technology code examples at `/practice/technology-code-examples` and `/api/practice/technology-code-examples` so learners can read generated code examples grouped directly by inferred JSON technologies.
- Added record-level technology code example details at `/practice/technology-code-examples/{technology}` and `/api/practice/technology-code-examples/{technology}` so one technology can show multiple source records with workspace links and generated snippets.
- Added technology implementation labs at `/practice/technology-implementation-lab/{technology}` and `/api/practice/technology-implementation-lab/{technology}` so one technology can become ordered phases, workspace tasks, verification commands, and a progress-checklist payload.
- Added technology commit plans at `/practice/technology-commit-plan/{technology}` and `/api/practice/technology-commit-plan/{technology}` so one implementation lab can become a branch name, commit message, changed-file list, verification evidence, review checklist, and progress payload.
- Added technology portfolio artifacts at `/practice/technology-portfolio-artifact/{technology}` and `/api/practice/technology-portfolio-artifact/{technology}` so content-backed implementation work can become source coverage, changed-file evidence, talking points, and a README-style artifact.
- Added technology interview packs at `/practice/technology-interview-pack/{technology}` and `/api/practice/technology-interview-pack/{technology}` so portfolio artifacts can become interview questions, answer outlines, evidence to cite, and an oral practice script.
- Added technology skill assessments at `/practice/technology-skill-assessment/{technology}` and `/api/practice/technology-skill-assessment/{technology}` so interview packs can become a 100-point rubric, readiness signals, improvement tasks, and progress items.
- Added technology remediation plans at `/practice/technology-remediation-plan/{technology}` and `/api/practice/technology-remediation-plan/{technology}` so skill assessments can become repair tasks, focus files, verification commands, next routes, and progress items.
- Added technology mastery checkpoints at `/practice/technology-mastery-checkpoint/{technology}` and `/api/practice/technology-mastery-checkpoint/{technology}` so remediation plans can become promote-or-repeat decisions, proof checklists, next challenges, handoffs, and progress items.
- Added technology spaced reviews at `/practice/technology-spaced-review/{technology}` and `/api/practice/technology-spaced-review/{technology}` so mastery checkpoints can become day 1, day 3, and day 7 recall, rebuild, defense, promotion, and progress items.
- Added technology evidence archives at `/practice/technology-evidence-archive/{technology}` and `/api/practice/technology-evidence-archive/{technology}` so spaced reviews can become archive IDs, retrieval keys, proof bundles, reuse targets, prompts, and progress items.
- Added technology learning pipelines at `/practice/technology-learning-pipeline/{technology}` and `/api/practice/technology-learning-pipeline/{technology}` to centralize the full technology-specific flow from code examples through evidence archive and reduce navigation friction across the generated practice routes.
- Added technology pipeline indexes at `/practice/technology-pipelines` and `/api/practice/technology-pipelines` so all inferred JSON technologies can be discovered, filtered, and opened as full learning pipelines from one screen.
- Added technology quality plans at `/practice/technology-quality-plan` and `/api/practice/technology-quality-plan` so each inferred JSON technology has baseline test counts, Pint commands, acceptance checks, risk notes, and a reused quality-gate status.
- Added configuration readiness at `/practice/configuration-readiness` and `/api/practice/configuration-readiness` so `config/app.php`, `config/auth.php`, and the quality-gate service can be studied as a testable runtime contract.
- Added configuration test plans at `/practice/configuration-test-plan` and `/api/practice/configuration-test-plan` so app/auth readiness checks become grouped PHPUnit assertions, starter snippets, and verification commands.
- Added configuration change checklists at `/practice/configuration-change-checklist` and `/api/practice/configuration-change-checklist` so app, auth, and quality-gate config changes have impact notes, before/after checks, rollback guidance, review questions, and verification commands.
- Added configuration deployment plans at `/practice/configuration-deployment-plan` and `/api/practice/configuration-deployment-plan` so config changes move through preflight answers, deployment steps, runtime smoke checks, rollback actions, evidence, and quality-gate status.
- Added configuration release evidence at `/practice/configuration-release-evidence` and `/api/practice/configuration-release-evidence` so configuration deployment proof can be packaged into PR-ready release summaries, API evidence, rollback evidence, portfolio notes, commands, and quality-gate status.
- Added configuration interview briefs at `/practice/configuration-interview-brief` and `/api/practice/configuration-interview-brief` so release evidence becomes interview questions, answer outlines, follow-ups, evidence references, rehearsal checks, and quality-gate status.
- Added configuration mastery checkpoints at `/practice/configuration-mastery-checkpoint` and `/api/practice/configuration-mastery-checkpoint` so configuration practice can be scored, promoted or repeated, handed off, and tied back to quality-gate status.
- Added configuration spaced reviews at `/practice/configuration-spaced-review` and `/api/practice/configuration-spaced-review` so configuration mastery becomes day 1, day 3, and day 7 recall, rebuild, defense, promotion criteria, and quality-gate status.
- Added configuration evidence archives at `/practice/configuration-evidence-archive` and `/api/practice/configuration-evidence-archive` so spaced review evidence becomes retrieval keys, proof bundles, reuse targets, retrieval prompts, commands, and quality-gate status.
- Added configuration learning pipelines at `/practice/configuration-learning-pipeline` and `/api/practice/configuration-learning-pipeline` so the full app/auth configuration flow can be navigated from readiness through evidence archive with progress payloads.
- Added configuration practice dashboards at `/practice/configuration-dashboard` and `/api/practice/configuration-dashboard` so configuration practice has one compact status, next-stage, stage-group, archive, command, and progress summary.
- Added configuration risk registers at `/practice/configuration-risk-register` and `/api/practice/configuration-risk-register` so app/auth/quality-gate configuration risks have severity, signals, mitigation, owner routes, review cadence, and commands.
- Added configuration remediation plans at `/practice/configuration-remediation-plan` and `/api/practice/configuration-remediation-plan` so configuration risks become file-focused repair tasks with actions, owner routes, verification commands, done signals, and completion criteria.
- Added configuration pull request plans at `/practice/configuration-pull-request-plan` and `/api/practice/configuration-pull-request-plan` so remediation work becomes branch, commit, changed-file, PR summary, review checklist, verification, and evidence artifacts.
- Added configuration assessments at `/practice/configuration-assessment` and `/api/practice/configuration-assessment` so configuration remediation PR work can be scored with a rubric, readiness signals, improvement tasks, evidence, and commands.
- Added configuration decision records at `/practice/configuration-decision-record` and `/api/practice/configuration-decision-record` so assessment output becomes an ADR-style record with context, decision, alternatives, consequences, evidence, and commands.
- Added configuration operations runbooks at `/practice/configuration-operations-runbook` and `/api/practice/configuration-operations-runbook` so accepted configuration decisions become operational triggers, diagnostics, rollback, handoff, evidence, and commands.
- Added configuration incident drills at `/practice/configuration-incident-drill` and `/api/practice/configuration-incident-drill` so operations runbooks become scenario-driven practice with timeline, diagnosis steps, patch plan, recovery evidence, and handoff.
- Added configuration incident postmortems at `/practice/configuration-incident-postmortem` and `/api/practice/configuration-incident-postmortem` so incident drills become blameless learning records with impact, root cause, action items, spaced-review inputs, evidence, and commands.
- Expanded configuration spaced reviews so day 1, day 3, and day 7 cards include incident-memory prompts from the configuration postmortem.
- Expanded configuration evidence archives so postmortem and incident recovery memory are stored with retrieval keys, proof bundle entries, reuse targets, and retrieval prompts.
- Added configuration archive retrieval at `/practice/configuration-archive-retrieval` and `/api/practice/configuration-archive-retrieval` so archived proof can be practiced through portfolio, interview, and incident recovery retrieval cases.
- Added configuration evidence reuse plans at `/practice/configuration-evidence-reuse-plan` and `/api/practice/configuration-evidence-reuse-plan` so retrieved proof becomes portfolio notes, interview answers, review comments, and incident recovery notes.
- Added configuration portfolio briefs at `/practice/configuration-portfolio-brief` and `/api/practice/configuration-portfolio-brief` so reused configuration evidence becomes a portfolio-ready artifact with proof table, talking points, and review checklist.
- Added configuration portfolio reviews at `/practice/configuration-portfolio-review` and `/api/practice/configuration-portfolio-review` so portfolio briefs can be scored for clarity, proof quality, interview readiness, review discipline, and quality status.
- Added configuration publication checklists at `/practice/configuration-publication-checklist` and `/api/practice/configuration-publication-checklist` so reviewed portfolio artifacts can be approved, held, or routed into portfolio/interview/code-review channels.
- Added configuration handoff packets at `/practice/configuration-handoff-packet` and `/api/practice/configuration-handoff-packet` so approved configuration evidence can be carried into the next study session, interview rehearsal, or code review.
- Added configuration next-session plans at `/practice/configuration-next-session-plan` and `/api/practice/configuration-next-session-plan` so handoff packets become timed follow-up sessions with preflight checks, practice blocks, deliverables, and stop criteria.
- Added configuration session debriefs at `/practice/configuration-session-debrief` and `/api/practice/configuration-session-debrief` so next-session outputs become captured evidence, blockers, and follow-up actions.
- Added configuration session archives at `/practice/configuration-session-archive` and `/api/practice/configuration-session-archive` so follow-up session debriefs become retrievable archive entries with evidence tags, prompts, and reuse paths.
- Added configuration archive refresh plans at `/practice/configuration-archive-refresh-plan` and `/api/practice/configuration-archive-refresh-plan` so archived configuration evidence has refresh triggers, tasks, rerun commands, and remediation triggers.
- Added configuration maintenance roadmaps at `/practice/configuration-maintenance-roadmap` and `/api/practice/configuration-maintenance-roadmap` so refreshed configuration evidence has a long-running cadence, owners, health signals, and escalation paths.
- Expanded the configuration learning pipeline and dashboard to include risk register, remediation plan, PR plan, and assessment stages before deployment and release evidence.

## Pending

- Database-backed progress/user tracking.
- Authentication around personal study progress.
- Node/Vite UI build, because Node is not installed on the current host machine.
- SQLite migrations on the host, because the current host PHP installation is missing the SQLite PDO driver. The Docker image installs SQLite support.

## Current Routes

- `/` - dashboard and integration status
- `/practice` - primary code-first practice workspace
- `/practice/{exercise}` - native practice exercise detail
- `/practice/content-map` - map content and question records to native practice tasks
- `/practice/content-drill` - turn one content/question record into a focused coding drill
- `/practice/question-drills` - turn filtered question records into coding drill cards
- `/practice/technology-matrix` - group content/question records by technology and practice path
- `/practice/technology-pipelines` - discover all inferred technology learning pipelines
- `/practice/technology-quality-plan` - quality gates and verification commands for inferred technology pipelines
- `/practice/technology-code-examples` - generated code examples grouped by inferred JSON technologies
- `/practice/technology-code-examples/{technology}` - record-level code examples for one inferred technology
- `/practice/technology-implementation-lab/{technology}` - phased implementation lab for one inferred technology
- `/practice/technology-commit-plan/{technology}` - commit-ready artifacts for one inferred technology lab
- `/practice/technology-portfolio-artifact/{technology}` - portfolio artifact for one inferred technology lab
- `/practice/technology-interview-pack/{technology}` - interview defense pack for one inferred technology artifact
- `/practice/technology-skill-assessment/{technology}` - scored skill assessment for one inferred technology
- `/practice/technology-remediation-plan/{technology}` - remediation plan for one inferred technology assessment
- `/practice/technology-mastery-checkpoint/{technology}` - promote-or-repeat checkpoint for one inferred technology
- `/practice/technology-spaced-review/{technology}` - spaced review schedule for one inferred technology
- `/practice/technology-evidence-archive/{technology}` - evidence archive for one inferred technology review
- `/practice/technology-learning-pipeline/{technology}` - complete navigation pipeline for one inferred technology
- `/practice/technology-board` - source-grouped practice board for one technology
- `/practice/source-packs/{sourceKey}` - turn one JSON source file into a practice pack
- `/practice/source-packs` - list source files that can become practice packs
- `/practice/implementation-blueprint` - turn one content/question record into implementation names
- `/practice/guided-checklist` - turn one blueprint into TDD steps and progress payload
- `/practice/starter-kit` - turn one guided checklist into starter code snippets
- `/practice/record-workspace` - full source-to-code workspace for one JSON record
- `/practice/queue` - ordered queue of record workspaces from filtered content/question records
- `/practice/syllabus` - code-first practice syllabus from technologies and source packs
- `/practice/sprint` - short content-backed sprint with workspace and verification tasks
- `/practice/tdd-lab` - Red-Green-Refactor lab from one content/question record
- `/practice/review-lab` - layer-by-layer review checklist for one content-backed implementation
- `/practice/remediation-lab` - concrete fix tasks from one review checklist
- `/practice/pull-request-lab` - PR draft artifacts for one content-backed implementation
- `/practice/assessment-lab` - self-assessment rubric for one content-backed implementation
- `/practice/retrospective-lab` - wins, weak spots, and next actions after assessment
- `/practice/portfolio-lab` - portfolio entry artifact for one content-backed implementation
- `/practice/capstone-lab` - technology-level mini-project from source coverage and queue tasks
- `/practice/mentor-feedback-lab` - mentor-style feedback for one technology capstone
- `/practice/checkpoint-exam-lab` - timed checkpoint exam for one technology
- `/practice/mastery-path-lab` - multi-technology mastery path from syllabus phases
- `/practice/rotation-lab` - day-by-day practice schedule from mastery path milestones
- `/practice/weekly-report-lab` - weekly progress report from a practice rotation
- `/practice/demo-script-lab` - demo script from a weekly practice report
- `/practice/live-coding-lab` - timed live coding session from a demo script
- `/practice/bug-fix-lab` - bug-fix drills from live coding rounds
- `/practice/refactor-lab` - safe refactor tasks from bug-fix drills
- `/practice/release-readiness-lab` - release readiness artifacts from refactor tasks
- `/practice/interview-defense-lab` - technical defense cards from release evidence
- `/practice/knowledge-gap-lab` - knowledge-gap coding actions from interview defenses
- `/practice/spaced-repetition-lab` - repeated coding review checkpoints from knowledge gaps
- `/practice/mastery-evidence-lab` - mastery evidence cards from repeated checkpoints
- `/practice/competency-map-lab` - competency map from mastery evidence
- `/practice/next-challenge-lab` - next challenge recommendations from competency maps
- `/practice/challenge-execution-lab` - executable steps from next challenge cards
- `/practice/challenge-evidence-review-lab` - evidence review cards from executable challenge steps
- `/practice/challenge-promotion-lab` - promote-or-repeat decisions from evidence review cards
- `/practice/next-session-handoff-lab` - next-session handoff cards from promotion decisions
- `/practice/session-replay-lab` - replay rounds from next-session handoff cards
- `/practice/session-debrief-lab` - debrief cards from replay rounds
- `/practice/session-archive-lab` - archive entries from debrief cards
- `/practice/archive-retrieval-lab` - retrieval cards from archive entries
- `/practice/evidence-reuse-plan-lab` - reuse plans from retrieval cards
- `/practice/verification-plan` - verification commands and smoke checks for one record workspace
- `/practice/configuration-dashboard` - compact dashboard for app/auth configuration practice
- `/practice/configuration-risk-register` - risk register for app/auth/quality-gate configuration practice
- `/practice/configuration-remediation-plan` - remediation tasks for app/auth/quality-gate configuration risks
- `/practice/configuration-pull-request-plan` - pull request artifacts for app/auth/quality-gate remediation work
- `/practice/configuration-assessment` - scored rubric for app/auth/quality-gate remediation PR readiness
- `/practice/configuration-decision-record` - ADR-style decision record for app/auth/quality-gate configuration work
- `/practice/configuration-operations-runbook` - operations runbook for app/auth/quality-gate configuration work
- `/practice/configuration-incident-drill` - incident drill for app/auth/quality-gate configuration recovery practice
- `/practice/configuration-incident-postmortem` - postmortem for app/auth/quality-gate configuration recovery practice
- `/practice/configuration-learning-pipeline` - complete app/auth configuration learning pipeline
- `/practice/configuration-readiness` - app/auth configuration readiness checks with quality-gate output
- `/practice/configuration-test-plan` - PHPUnit assertion plan for app/auth configuration contracts
- `/practice/configuration-change-checklist` - review checklist for app/auth/quality-gate configuration changes
- `/practice/configuration-deployment-plan` - deployment plan for app/auth/quality-gate configuration changes
- `/practice/configuration-release-evidence` - release evidence artifact for app/auth/quality-gate configuration work
- `/practice/configuration-interview-brief` - interview brief for app/auth/quality-gate configuration work
- `/practice/configuration-mastery-checkpoint` - promote-or-repeat checkpoint for app/auth/quality-gate configuration work
- `/practice/configuration-spaced-review` - day 1, day 3, and day 7 review for app/auth/quality-gate configuration work
- `/practice/configuration-evidence-archive` - evidence archive for app/auth/quality-gate configuration work
- `/practice/configuration-archive-retrieval` - retrieval drills for archived app/auth/quality-gate configuration evidence
- `/practice/configuration-evidence-reuse-plan` - reuse plan for retrieved app/auth/quality-gate configuration evidence
- `/practice/configuration-portfolio-brief` - portfolio-ready brief for reused app/auth/quality-gate configuration evidence
- `/practice/configuration-portfolio-review` - scored review for reused app/auth/quality-gate configuration portfolio evidence
- `/practice/configuration-publication-checklist` - publication checklist for reused app/auth/quality-gate configuration portfolio evidence
- `/practice/configuration-handoff-packet` - handoff packet for approved app/auth/quality-gate configuration portfolio evidence
- `/practice/configuration-next-session-plan` - next-session plan for approved app/auth/quality-gate configuration portfolio evidence
- `/practice/configuration-session-debrief` - session debrief for approved app/auth/quality-gate configuration portfolio evidence
- `/practice/configuration-session-archive` - session archive for approved app/auth/quality-gate configuration portfolio evidence
- `/practice/configuration-archive-refresh-plan` - refresh plan for archived app/auth/quality-gate configuration evidence
- `/practice/configuration-maintenance-roadmap` - maintenance roadmap for archived app/auth/quality-gate configuration evidence
- `/practice-sessions/today` - daily native practice session page
- `/practice-notes/thin-controller` - thin-controller Laravel HTTP practice page
- `/workbench/name-normalizer` - runnable PHP foundation workbench
- `/questions` - searchable JSON-backed question bank
- `/sources/{sourceKey}` - decoded source file and extracted records
- `/quiz` - randomized practice cards from JSON content
- `/study-plan` - generated study plan from JSON source modules
- `/analytics` - content analytics dashboard
- `/labs` - hands-on labs generated from content topics
- `/api/learning` - JSON API summary
- `/api/learning/sources` - JSON source list
- `/api/learning/sources/{sourceKey}` - one decoded JSON source
- `/api/learning/questions` - filtered JSON question bank
- `/api/learning/quiz` - generated practice set
- `/api/learning/study-plan` - generated study plan
- `/api/learning/analytics` - content analytics report
- `/api/learning/labs` - hands-on labs generated from content topics
- `/api/practice` - native practice catalog API
- `/api/practice/{exercise}` - native practice exercise API
- `/api/practice/content-map` - content/question record to native practice task API
- `/api/practice/content-drill` - content/question record to focused coding drill API
- `/api/practice/question-drills` - question record to drill-card API
- `/api/practice/technology-matrix` - technology coverage to practice matrix API
- `/api/practice/technology-pipelines` - discover all inferred technology learning pipelines
- `/api/practice/technology-quality-plan` - quality gates and verification commands for inferred technology pipelines
- `/api/practice/technology-code-examples` - generated code examples grouped by inferred JSON technologies
- `/api/practice/technology-code-examples/{technology}` - record-level code examples for one inferred technology
- `/api/practice/technology-implementation-lab/{technology}` - phased implementation lab for one inferred technology
- `/api/practice/technology-commit-plan/{technology}` - commit-ready artifacts for one inferred technology lab
- `/api/practice/technology-portfolio-artifact/{technology}` - portfolio artifact for one inferred technology lab
- `/api/practice/technology-interview-pack/{technology}` - interview defense pack for one inferred technology artifact
- `/api/practice/technology-skill-assessment/{technology}` - scored skill assessment for one inferred technology
- `/api/practice/technology-remediation-plan/{technology}` - remediation plan for one inferred technology assessment
- `/api/practice/technology-mastery-checkpoint/{technology}` - promote-or-repeat checkpoint for one inferred technology
- `/api/practice/technology-spaced-review/{technology}` - spaced review schedule for one inferred technology
- `/api/practice/technology-evidence-archive/{technology}` - evidence archive for one inferred technology review
- `/api/practice/technology-learning-pipeline/{technology}` - complete navigation pipeline for one inferred technology
- `/api/practice/technology-board` - technology-focused source board API
- `/api/practice/source-packs/{sourceKey}` - source file to practice pack API
- `/api/practice/source-packs` - source file to practice pack index API
- `/api/practice/implementation-blueprint` - content/question record to implementation blueprint API
- `/api/practice/guided-checklist` - implementation blueprint to guided checklist API
- `/api/practice/starter-kit` - guided checklist to starter code snippets API
- `/api/practice/record-workspace` - full source-to-code workspace API
- `/api/practice/queue` - filtered question records to ordered practice queue API
- `/api/practice/syllabus` - code-first practice syllabus API
- `/api/practice/sprint` - short content-backed sprint API
- `/api/practice/tdd-lab` - Red-Green-Refactor content-backed lab API
- `/api/practice/review-lab` - layer-by-layer review checklist API
- `/api/practice/remediation-lab` - concrete fix-task API from one review checklist
- `/api/practice/pull-request-lab` - PR draft artifacts API
- `/api/practice/assessment-lab` - self-assessment rubric API
- `/api/practice/retrospective-lab` - retrospective next-action API
- `/api/practice/portfolio-lab` - portfolio entry artifact API
- `/api/practice/capstone-lab` - technology-level mini-project API
- `/api/practice/mentor-feedback-lab` - mentor-style capstone feedback API
- `/api/practice/checkpoint-exam-lab` - timed checkpoint exam API
- `/api/practice/mastery-path-lab` - multi-technology mastery path API
- `/api/practice/rotation-lab` - day-by-day practice schedule API
- `/api/practice/weekly-report-lab` - weekly progress report API
- `/api/practice/demo-script-lab` - weekly report to demo script API
- `/api/practice/live-coding-lab` - demo script to timed live coding API
- `/api/practice/bug-fix-lab` - live coding to bug-fix drill API
- `/api/practice/refactor-lab` - bug-fix drill to refactor task API
- `/api/practice/release-readiness-lab` - refactor task to release readiness API
- `/api/practice/interview-defense-lab` - release evidence to interview defense API
- `/api/practice/knowledge-gap-lab` - interview defense to knowledge-gap API
- `/api/practice/spaced-repetition-lab` - knowledge-gap to repeated review API
- `/api/practice/mastery-evidence-lab` - repeated review to mastery evidence API
- `/api/practice/competency-map-lab` - mastery evidence to competency map API
- `/api/practice/next-challenge-lab` - competency map to next challenge API
- `/api/practice/challenge-execution-lab` - next challenge to executable steps API
- `/api/practice/challenge-evidence-review-lab` - executable steps to evidence review API
- `/api/practice/challenge-promotion-lab` - evidence review to promotion decision API
- `/api/practice/next-session-handoff-lab` - promotion decision to next-session handoff API
- `/api/practice/session-replay-lab` - next-session handoff to replay rounds API
- `/api/practice/session-debrief-lab` - replay rounds to debrief cards API
- `/api/practice/session-archive-lab` - debrief cards to archive entries API
- `/api/practice/archive-retrieval-lab` - archive entries to retrieval cards API
- `/api/practice/evidence-reuse-plan-lab` - retrieval cards to reuse plans API
- `/api/practice/verification-plan` - record workspace to verification plan API
- `/api/practice/configuration-dashboard` - compact dashboard for app/auth configuration practice
- `/api/practice/configuration-risk-register` - risk register for app/auth/quality-gate configuration practice
- `/api/practice/configuration-remediation-plan` - remediation tasks for app/auth/quality-gate configuration risks
- `/api/practice/configuration-pull-request-plan` - pull request artifacts for app/auth/quality-gate remediation work
- `/api/practice/configuration-assessment` - scored rubric for app/auth/quality-gate remediation PR readiness
- `/api/practice/configuration-decision-record` - ADR-style decision record for app/auth/quality-gate configuration work
- `/api/practice/configuration-operations-runbook` - operations runbook for app/auth/quality-gate configuration work
- `/api/practice/configuration-incident-drill` - incident drill for app/auth/quality-gate configuration recovery practice
- `/api/practice/configuration-incident-postmortem` - postmortem for app/auth/quality-gate configuration recovery practice
- `/api/practice/configuration-learning-pipeline` - complete app/auth configuration learning pipeline
- `/api/practice/configuration-readiness` - app/auth configuration readiness checks with quality-gate output
- `/api/practice/configuration-test-plan` - PHPUnit assertion plan for app/auth configuration contracts
- `/api/practice/configuration-change-checklist` - review checklist for app/auth/quality-gate configuration changes
- `/api/practice/configuration-deployment-plan` - deployment plan for app/auth/quality-gate configuration changes
- `/api/practice/configuration-release-evidence` - release evidence artifact for app/auth/quality-gate configuration work
- `/api/practice/configuration-interview-brief` - interview brief for app/auth/quality-gate configuration work
- `/api/practice/configuration-mastery-checkpoint` - promote-or-repeat checkpoint for app/auth/quality-gate configuration work
- `/api/practice/configuration-spaced-review` - day 1, day 3, and day 7 review for app/auth/quality-gate configuration work
- `/api/practice/configuration-evidence-archive` - evidence archive for app/auth/quality-gate configuration work
- `/api/practice/configuration-archive-retrieval` - retrieval drills for archived app/auth/quality-gate configuration evidence
- `/api/practice/configuration-evidence-reuse-plan` - reuse plan for retrieved app/auth/quality-gate configuration evidence
- `/api/practice/configuration-portfolio-brief` - portfolio-ready brief for reused app/auth/quality-gate configuration evidence
- `/api/practice/configuration-portfolio-review` - scored review for reused app/auth/quality-gate configuration portfolio evidence
- `/api/practice/configuration-publication-checklist` - publication checklist for reused app/auth/quality-gate configuration portfolio evidence
- `/api/practice/configuration-handoff-packet` - handoff packet for approved app/auth/quality-gate configuration portfolio evidence
- `/api/practice/configuration-next-session-plan` - next-session plan for approved app/auth/quality-gate configuration portfolio evidence
- `/api/practice/configuration-session-debrief` - session debrief for approved app/auth/quality-gate configuration portfolio evidence
- `/api/practice/configuration-session-archive` - session archive for approved app/auth/quality-gate configuration portfolio evidence
- `/api/practice/configuration-archive-refresh-plan` - refresh plan for archived app/auth/quality-gate configuration evidence
- `/api/practice/configuration-maintenance-roadmap` - maintenance roadmap for archived app/auth/quality-gate configuration evidence
- `/api/practice/name-normalizer` - runnable practice API for normalized names
- `/api/practice/topics` - validated practice topic API exercise
- `/api/practice/quality-gate` - testing and Pint quality-gate practice API
- `/api/practice/runtime-smoke-check` - Docker and runtime configuration smoke-check API
- `/api/practice/session-plan` - filtered daily practice session API
- `/api/practice/progress-checklist` - checklist progress summary API

## Docker

Run from the hub folder:

```powershell
cd hub
docker-compose up --build
```

Then open:

```text
http://localhost:8088
```

To change the port:

```powershell
Set `HUB_PORT`, `HUB_INTERNAL_PORT`, and `HUB_RUNTIME_BASE_URL` in `.env`, then run:
docker-compose up --build
```

## Configuration

The hub reads content from:

```text
LABS_CONTENT_PATH=../data
HUB_RUNTIME_BASE_URL=http://localhost:8088
HUB_PORT=8088
HUB_INTERNAL_PORT=8088
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

If the data folder moves, update `LABS_CONTENT_PATH` in the hub environment.

Runtime URLs, app/auth defaults, and configuration-practice artifact IDs are env-backed through `config/*.php` and documented in `.env.example`. Keep environment-specific URLs, ticket IDs, incident IDs, archive prefixes, and secret service credentials out of services, views, and route files.

Use file-backed session/cache defaults while the hub is a read-only JSON browser. Switch to database-backed drivers later when study progress or auth needs persistence.

If `.env` already existed before this default was added, update the local values to:

```text
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

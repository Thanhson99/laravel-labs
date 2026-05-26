# AI Change Log And Findings

This file records durable findings and decisions from AI sessions. Keep it concise and useful for future work.

## 2026-05-25 Hub Route Organization Rules

Refactored `hub` route definitions so `routes/web.php` and `routes/api.php` act as entry points and feature routes live under grouped files in `routes/web/**` and `routes/api/**`.

Added route and folder responsibility rules:

- split large route maps by feature/workflow
- keep generic parameter routes after explicit routes
- use short English file headers, group comments, and route-purpose comments in Laravel learning/practice route files
- enforce route file documentation with a unit test so missing route comments fail verification
- keep strict responsibilities across routes, controllers, requests, services/actions, repositories/queries, views, config, and tests
- enforce English-only hub UI copy with a unit test that blocks common Vietnamese no-accent phrases in views, config-driven practice copy, and practice services

## 2026-05-26 Hub Environment Configuration Hardening

- Moved important hub configuration values behind env-backed config entries: app timezone/cipher, auth password-reset timing, hub runtime base URL, content path, and configuration-practice artifact IDs/root-cause/archive suffixes.
- Expanded `hub/.env.example` so every `env()` key used by `hub/config/**` is documented with local-safe defaults or blank placeholders for secrets.
- Updated local `hub/.env` with non-sensitive defaults only; existing secrets are not documented in notes or copied into examples.
- Kept service code reading IDs and runtime metadata through `config()` instead of embedding environment-specific names directly in services.
- Replaced static portal Hub workbench URLs with `{{HUB_BASE_URL}}` tokens and added renderer-side resolution through `data-hub-base-url` or `window.LARAVEL_LABS_HUB_BASE_URL`, keeping host/port out of JSON content.
- Moved Hub Docker host/container port settings to `HUB_PORT` and `HUB_INTERNAL_PORT`; Docker Compose and the Dockerfile now consume those variables instead of requiring file edits for port changes.

## 2026-05-25 Hub App

- Added `hub/` as the aggregate Laravel application for Laravel Labs.
- Integrated `data/**` JSON content into Laravel through a repository contract and Blade views.
- Added `hub/INTEGRATION_NOTES.md` to track completed and pending technology integration.
- Added Docker support and JSON API routes for the hub app.
- Added quiz practice mode through `LearningQuizService`, `/quiz`, and `/api/learning/quiz`.
- Added study-plan generation through `LearningStudyPlanService`, `/study-plan`, and `/api/learning/study-plan`.
- Added content analytics through `LearningAnalyticsService`, `/analytics`, and `/api/learning/analytics`.
- Added hands-on lab generation through `LearningLabService`, `/labs`, and `/api/learning/labs`.
- Repositioned `hub` as a native code-first practice workspace. Added `config/practice.php`, `/practice`, `/practice/{exercise}`, and `/api/practice`; JSON-backed pages are now reference tools rather than the primary app experience.
- Added the first runnable practice workbench: `App\Practice\Php\NameNormalizer`, `/workbench/name-normalizer`, and `/api/practice/name-normalizer`.
- Added the first validated API practice slice: `StorePracticeTopicRequest`, `PracticeTopicController`, `POST /api/practice/topics`, and `PracticeTopicApiTest`.
- Added the first Laravel HTTP practice slice: `PracticeNoteService`, `NoteController`, `/practice-notes/thin-controller`, `practice.note`, and `PracticeNoteTest`.
- Added the first Testing + Quality practice slice: `PracticeQualityGateService`, `EvaluateQualityGateRequest`, `PracticeQualityGateController`, `POST /api/practice/quality-gate`, unit tests, and API feature tests.
- Renamed the default feature example test to `DashboardPracticeTest`.
- Added the first Docker + Runtime practice slice: `RuntimeSmokeCheckService`, `RuntimeSmokeCheckController`, `GET /api/practice/runtime-smoke-check`, unit tests, and API feature tests.
- Renamed `PracticeWorkbenchTest` to `NameNormalizerWorkbenchTest`.
- Added a Laravel service-composition practice slice: `PracticeSessionPlannerService`, web/API session controllers, `/practice-sessions/today`, `GET /api/practice/session-plan`, Blade view, and feature tests.
- Renamed the API session controller to `PracticeSessionPlanController`.
- Added a state-transition practice slice: `PracticeProgressChecklistService`, `SummarizePracticeProgressRequest`, `PracticeProgressChecklistController`, `POST /api/practice/progress-checklist`, unit tests, and API feature tests.
- Renamed learning reference tests to `LearningReferencePagesTest` and `LearningContentApiTest`.
- Added a content-aware practice bridge: `ContentPracticeMapperService`, `/practice/content-map`, `GET /api/practice/content-map`, Blade view, and feature tests. It maps JSON content/question records to native Laravel practice tasks by inferred technology.
- Added content-backed drills: `ContentPracticeDrillService`, `/practice/content-drill`, `GET /api/practice/content-drill`, Blade view, and feature tests. A drill turns one JSON content/question record into source-specific files, steps, commands, and acceptance criteria.
- Added quiz-to-drill behavior: quiz cards now link to content drills by exact record id.
- Added question-backed drill sets: `QuestionDrillSetService`, `/practice/question-drills`, `GET /api/practice/question-drills`, Blade view, and feature tests.
- Added technology practice board: `TechnologyPracticeBoardService`, `/practice/technology-board`, `GET /api/practice/technology-board`, Blade view, and feature tests. It groups one technology's records by source file and links to source packs, queues, and record workspaces.
- Added technology practice matrix: `TechnologyPracticeMatrixService`, `/practice/technology-matrix`, `GET /api/practice/technology-matrix`, Blade view, and feature tests. It groups content/question records by inferred technology and links each group to a native exercise and sample drill.
- Added source practice pack index: `SourcePracticePackIndexService`, `/practice/source-packs`, `GET /api/practice/source-packs`, Blade view, and feature tests. It lists JSON source files with record counts, inferred technologies, and links to packs/workspaces.
- Added source practice packs: `SourcePracticePackService`, `/practice/source-packs/{sourceKey}`, `GET /api/practice/source-packs/{sourceKey}`, Blade view, and feature tests. A pack turns one JSON source file into technology paths, sample drills, workflow, and commands.
- Added implementation blueprints: `ContentImplementationBlueprintService`, `/practice/implementation-blueprint`, `GET /api/practice/implementation-blueprint`, Blade view, and feature tests. A blueprint turns one JSON content/question record into concrete route, class, file, and test names.
- Added guided implementation checklists: `GuidedImplementationChecklistService`, `/practice/guided-checklist`, `GET /api/practice/guided-checklist`, Blade view, and feature tests. A checklist turns a blueprint into TDD steps and a progress-checklist payload.
- Added implementation starter kits: `ImplementationStarterKitService`, `/practice/starter-kit`, `GET /api/practice/starter-kit`, Blade view, and feature tests. A starter kit turns one guided checklist into starter snippets for focused tests, Form Requests, controllers, and services.
- Added record practice workspaces: `RecordPracticeWorkspaceService`, `/practice/record-workspace`, `GET /api/practice/record-workspace`, Blade view, and feature tests. A workspace composes source, drill, blueprint, checklist, and starter snippets for one JSON record.
- Added practice queues: `PracticeQueueService`, `/practice/queue`, `GET /api/practice/queue`, Blade view, and feature tests. A queue turns filtered question records into ordered record-workspace tasks with estimated minutes and a progress payload.
- Added content practice syllabus: `ContentPracticeSyllabusService`, `/practice/syllabus`, `GET /api/practice/syllabus`, Blade view, and feature tests. A syllabus composes technology phases, source packs, board links, queues, and native exercises into one code-first learning path.
- Added practice sprints: `PracticeSprintService`, `/practice/sprint`, `GET /api/practice/sprint`, Blade view, and feature tests. A sprint turns syllabus phases into short queues of record workspaces with verification-plan links.
- Added practice TDD labs: `PracticeTddLabService`, `/practice/tdd-lab`, `GET /api/practice/tdd-lab`, Blade view, and feature tests. A lab turns one content/question record into Red-Green-Refactor stages with starter snippets, smoke request data, verification commands, and quality-gate payload.
- Added practice review labs: `PracticeReviewLabService`, `/practice/review-lab`, `GET /api/practice/review-lab`, Blade view, and feature tests. A review lab turns one TDD lab into route, validation, controller, service, test, and verification checklist items.
- Added practice remediation labs: `PracticeRemediationLabService`, `/practice/remediation-lab`, `GET /api/practice/remediation-lab`, Blade view, and feature tests. A remediation lab turns review checklist items into concrete fix tasks with file targets, actions, and verification commands.
- Added practice pull request labs: `PracticePullRequestLabService`, `/practice/pull-request-lab`, `GET /api/practice/pull-request-lab`, Blade view, and feature tests. A PR lab turns remediation output into branch, commit, PR summary, changed files, verification evidence, and review checklist artifacts.
- Added practice assessment labs: `PracticeAssessmentLabService`, `/practice/assessment-lab`, `GET /api/practice/assessment-lab`, Blade view, and feature tests. An assessment lab turns one PR lab into a 100-point rubric with changed-file, verification, review-checklist, and quality-gate evidence.
- Added practice retrospective labs: `PracticeRetrospectiveLabService`, `/practice/retrospective-lab`, `GET /api/practice/retrospective-lab`, Blade view, and feature tests. A retrospective lab turns one assessment into wins, weak spots, next actions, next lab links, and progress payload.
- Added practice portfolio labs: `PracticePortfolioLabService`, `/practice/portfolio-lab`, `GET /api/practice/portfolio-lab`, Blade view, and feature tests. A portfolio lab turns one retrospective into a reusable learning artifact with source reference, practiced skills, evidence, writeup template, and next improvement.
- Added practice capstone labs: `PracticeCapstoneLabService`, `/practice/capstone-lab`, `GET /api/practice/capstone-lab`, Blade view, and feature tests. A capstone lab turns one technology board and queue into a mini-project with source coverage, tasks, deliverables, artifact links, and progress payload.
- Added practice mentor feedback labs: `PracticeMentorFeedbackLabService`, `/practice/mentor-feedback-lab`, `GET /api/practice/mentor-feedback-lab`, Blade view, and feature tests. A mentor feedback lab turns capstone tasks into feedback items, risks, mentor questions, review focus, action items, and progress payload.
- Added practice checkpoint exam labs: `PracticeCheckpointExamLabService`, `/practice/checkpoint-exam-lab`, `GET /api/practice/checkpoint-exam-lab`, Blade view, and feature tests. A checkpoint exam lab turns mentor feedback into a timed technology exam with warmup questions, coding tasks, oral review, pass criteria, and progress payload.
- Added practice mastery path labs: `PracticeMasteryPathLabService`, `/practice/mastery-path-lab`, `GET /api/practice/mastery-path-lab`, Blade view, and feature tests. A mastery path lab turns syllabus phases into multi-technology milestones with capstone, checkpoint, mentor feedback, source packs, and progress payload.
- Added practice rotation labs: `PracticeRotationLabService`, `/practice/rotation-lab`, `GET /api/practice/rotation-lab`, Blade view, and feature tests. A rotation lab turns mastery paths into day-by-day schedules with technology focus, capstone/checkpoint/mentor links, required outputs, and progress payload.
- Added practice weekly report labs: `PracticeWeeklyReportLabService`, `/practice/weekly-report-lab`, `GET /api/practice/weekly-report-lab`, Blade view, and feature tests. A weekly report lab turns rotations into progress reports with daily outputs, technology coverage, evidence checklist, blockers, next week plan, and progress payload.
- Added practice demo script labs: `PracticeDemoScriptLabService`, `/practice/demo-script-lab`, `GET /api/practice/demo-script-lab`, Blade view, and feature tests. A demo script lab turns weekly reports into speaker notes, demo actions, verification steps, evidence, rehearsal checklist, and handoff payload.
- Added practice live coding labs: `PracticeLiveCodingLabService`, `/practice/live-coding-lab`, `GET /api/practice/live-coding-lab`, Blade view, and feature tests. A live coding lab turns demo scripts into timed coding rounds with narration, verification commands, scorecard, failure recovery, and progress payload.
- Added practice bug-fix labs: `PracticeBugFixLabService`, `/practice/bug-fix-lab`, `GET /api/practice/bug-fix-lab`, Blade view, and feature tests. A bug-fix lab turns live coding rounds into bug reports, diagnosis steps, patch targets, verification commands, pass signals, evidence, and review questions.
- Added practice refactor labs: `PracticeRefactorLabService`, `/practice/refactor-lab`, `GET /api/practice/refactor-lab`, Blade view, and feature tests. A refactor lab turns bug-fix drills into safe refactor tasks with target files, safe steps, guardrails, verification commands, evidence, architecture checks, and progress payload.
- Added practice release readiness labs: `PracticeReleaseReadinessLabService`, `/practice/release-readiness-lab`, `GET /api/practice/release-readiness-lab`, Blade view, and feature tests. A release readiness lab turns refactor tasks into release notes, smoke checks, rollback notes, verification evidence, handoff checklist, and progress payload.
- Added practice interview defense labs: `PracticeInterviewDefenseLabService`, `/practice/interview-defense-lab`, `GET /api/practice/interview-defense-lab`, Blade view, and feature tests. An interview defense lab turns release evidence into technical defense questions, answer outlines, evidence to cite, follow-up risks, scoring rubric, and progress payload.
- Added practice knowledge gap labs: `PracticeKnowledgeGapLabService`, `/practice/knowledge-gap-lab`, `GET /api/practice/knowledge-gap-lab`, Blade view, and feature tests. A knowledge gap lab turns interview defense cards into coding actions, review prompts, evidence rechecks, verification hints, next-session plan, and progress payload.
- Added practice spaced repetition labs: `PracticeSpacedRepetitionLabService`, `/practice/spaced-repetition-lab`, `GET /api/practice/spaced-repetition-lab`, Blade view, and feature tests. A spaced repetition lab turns knowledge-gap cards into day 1, day 3, and day 7 coding review checkpoints with recall prompts, verification hints, promotion criteria, and progress payload.
- Added practice mastery evidence labs: `PracticeMasteryEvidenceLabService`, `/practice/mastery-evidence-lab`, `GET /api/practice/mastery-evidence-lab`, Blade view, and feature tests. A mastery evidence lab turns spaced repetition checkpoints into evidence cards with scores, proof items, missing evidence, next harder labs, and progress payload.
- Added practice competency map labs: `PracticeCompetencyMapLabService`, `/practice/competency-map-lab`, `GET /api/practice/competency-map-lab`, Blade view, and feature tests. A competency map lab turns mastery evidence into competency levels, proof summaries, readiness, next actions, map summary, and progress payload.
- Added practice next challenge labs: `PracticeNextChallengeLabService`, `/practice/next-challenge-lab`, `GET /api/practice/next-challenge-lab`, Blade view, and feature tests. A next challenge lab turns competency maps into recommended challenge routes, verification commands, evidence requirements, challenge summary, and progress payload.
- Added practice challenge execution labs: `PracticeChallengeExecutionLabService`, `/practice/challenge-execution-lab`, `GET /api/practice/challenge-execution-lab`, Blade view, and feature tests. A challenge execution lab turns next challenge cards into route-specific execution steps with verification commands, evidence, exit criteria, session summary, and progress payload.
- Added practice challenge evidence review labs: `PracticeChallengeEvidenceReviewLabService`, `/practice/challenge-evidence-review-lab`, `GET /api/practice/challenge-evidence-review-lab`, Blade view, and feature tests. A challenge evidence review lab turns executable steps into evidence review cards with review questions, pass signals, risk checks, follow-up actions, review summary, and progress payload.
- Added practice challenge promotion labs: `PracticeChallengePromotionLabService`, `/practice/challenge-promotion-lab`, `GET /api/practice/challenge-promotion-lab`, Blade view, and feature tests. A challenge promotion lab turns evidence review cards into promote-or-repeat decisions with proof checklist, repeat triggers, next routes, learner notes, promotion summary, and progress payload.
- Added practice next session handoff labs: `PracticeNextSessionHandoffLabService`, `/practice/next-session-handoff-lab`, `GET /api/practice/next-session-handoff-lab`, Blade view, and feature tests. A next session handoff lab turns promotion decisions into next-session cards with route, preflight checklist, coding focus, done evidence, note prompts, handoff summary, and progress payload.
- Added practice session replay labs: `PracticeSessionReplayLabService`, `/practice/session-replay-lab`, `GET /api/practice/session-replay-lab`, Blade view, and feature tests. A session replay lab turns next-session handoff cards into replay rounds with before-check, coding run, after-check, evidence capture, retry policy, replay summary, and progress payload.
- Added practice session debrief labs: `PracticeSessionDebriefLabService`, `/practice/session-debrief-lab`, `GET /api/practice/session-debrief-lab`, Blade view, and feature tests. A session debrief lab turns replay rounds into debrief cards with result notes, lesson prompts, blocker checks, next actions, debrief summary, and progress payload.
- Added practice session archive labs: `PracticeSessionArchiveLabService`, `/practice/session-archive-lab`, `GET /api/practice/session-archive-lab`, Blade view, and feature tests. A session archive lab turns debrief cards into archive entries with proof bundle, learning summary, blocker status, retrieval tags, next reference, archive summary, and progress payload.
- Added practice archive retrieval labs: `PracticeArchiveRetrievalLabService`, `/practice/archive-retrieval-lab`, `GET /api/practice/archive-retrieval-lab`, Blade view, and feature tests. An archive retrieval lab turns archive entries into retrieval cards with search keys, retrieval prompts, reuse targets, proof quotes, refresh checks, retrieval summary, and progress payload.
- Added practice evidence reuse plan labs: `PracticeEvidenceReusePlanLabService`, `/practice/evidence-reuse-plan-lab`, `GET /api/practice/evidence-reuse-plan-lab`, Blade view, and feature tests. An evidence reuse plan lab turns retrieval cards into concrete portfolio, interview, and review reuse tasks with proof inputs, quality checks, reuse summary, and progress payload.
- Added implementation verification plans: `ImplementationVerificationPlanService`, `/practice/verification-plan`, `GET /api/practice/verification-plan`, Blade view, and feature tests. A plan generates focused test commands, route checks, smoke request data, and quality-gate payloads for one JSON record.
- Current verification limits: Node is unavailable, and SQLite migrations can fail without the SQLite PDO driver.
- Expanded content-backed practice technology inference and starter snippets so JSON records can now produce concrete code examples for API validation, auth/policies, database/Eloquent, file storage, async events/jobs, cache/performance, container bindings, realtime events, Blade UI, PHP, and default Laravel HTTP practice slices.
- Added `hub` technology code examples at `/practice/technology-code-examples` and `/api/practice/technology-code-examples`; the page groups JSON-derived technologies and shows generated Laravel code snippets for each sample record.
- Added record-level technology code example detail routes at `/practice/technology-code-examples/{technology}` and `/api/practice/technology-code-examples/{technology}` so one technology can list multiple JSON records with generated snippets and workspace links.
- Added technology implementation lab routes at `/practice/technology-implementation-lab/{technology}` and `/api/practice/technology-implementation-lab/{technology}`; each lab turns one inferred technology into ordered implementation phases, workspace tasks, verification commands, and a progress-checklist payload.
- Added technology commit plan routes at `/practice/technology-commit-plan/{technology}` and `/api/practice/technology-commit-plan/{technology}`; each plan turns one technology implementation lab into branch, commit, changed-file, verification, evidence, review, and progress artifacts.
- Added technology portfolio artifact routes at `/practice/technology-portfolio-artifact/{technology}` and `/api/practice/technology-portfolio-artifact/{technology}`; each artifact turns content-backed implementation work into source coverage, changed-file evidence, interview talking points, and a README-style portfolio template.
- Added technology interview pack routes at `/practice/technology-interview-pack/{technology}` and `/api/practice/technology-interview-pack/{technology}`; each pack turns a technology portfolio artifact into interview questions, answer outlines, evidence to cite, an oral practice script, and progress items.
- Added technology skill assessment routes at `/practice/technology-skill-assessment/{technology}` and `/api/practice/technology-skill-assessment/{technology}`; each assessment turns an interview pack into a 100-point rubric, readiness signals, improvement tasks, and progress items.
- Added technology remediation plan routes at `/practice/technology-remediation-plan/{technology}` and `/api/practice/technology-remediation-plan/{technology}`; each plan turns a skill assessment into repair tasks, focus files, verification commands, next routes, and progress items.
- Added technology mastery checkpoint routes at `/practice/technology-mastery-checkpoint/{technology}` and `/api/practice/technology-mastery-checkpoint/{technology}`; each checkpoint turns a remediation plan into promote-or-repeat decisions, proof checklists, next challenges, handoffs, and progress items.
- Added technology spaced review routes at `/practice/technology-spaced-review/{technology}` and `/api/practice/technology-spaced-review/{technology}`; each schedule turns a mastery checkpoint into day 1, day 3, and day 7 recall, rebuild, defense, promotion criteria, and progress items.
- Added technology evidence archive routes at `/practice/technology-evidence-archive/{technology}` and `/api/practice/technology-evidence-archive/{technology}`; each archive turns a spaced review into an archive id, retrieval keys, proof bundle, reuse targets, retrieval prompts, and progress items.
- Added technology learning pipeline routes at `/practice/technology-learning-pipeline/{technology}` and `/api/practice/technology-learning-pipeline/{technology}`; each pipeline centralizes the full technology-specific flow from code examples through evidence archive and links to every generated stage.
- Added technology pipeline index routes at `/practice/technology-pipelines` and `/api/practice/technology-pipelines`; the index discovers every inferred JSON technology, preserves filters, and links each item into the full learning pipeline, code examples, and API route.
- Added technology quality plan routes at `/practice/technology-quality-plan` and `/api/practice/technology-quality-plan`; each plan reuses the quality-gate service to give inferred JSON technologies baseline test counts, commands, acceptance checks, risk notes, and ready/needs-work status.
- Added configuration readiness routes at `/practice/configuration-readiness` and `/api/practice/configuration-readiness`; the report reads `config/app.php` and `config/auth.php`, converts checks into the shared quality-gate shape, and gives learners focused verification commands for runtime/auth configuration.
- Added configuration test plan routes at `/practice/configuration-test-plan` and `/api/practice/configuration-test-plan`; the plan turns app/auth readiness checks into grouped PHPUnit assertion ideas, a starter snippet, quality-gate context, and verification commands.
- Added configuration change checklist routes at `/practice/configuration-change-checklist` and `/api/practice/configuration-change-checklist`; the checklist turns app/auth/quality-gate config changes into impact notes, before/after checks, rollback guidance, review questions, and verification commands.
- Added configuration deployment plan routes at `/practice/configuration-deployment-plan` and `/api/practice/configuration-deployment-plan`; the plan extends configuration checklists into preflight answers, deploy steps, runtime smoke checks, rollback steps, evidence, and quality-gate status.
- Added configuration release evidence routes at `/practice/configuration-release-evidence` and `/api/practice/configuration-release-evidence`; the artifact turns deployment proof into PR-ready release summaries, API evidence, rollback evidence, portfolio notes, commands, and quality-gate status.
- Added configuration interview brief routes at `/practice/configuration-interview-brief` and `/api/practice/configuration-interview-brief`; the brief turns release evidence into interview questions, answer outlines, follow-ups, evidence references, rehearsal checks, and quality-gate status.
- Added configuration mastery checkpoint routes at `/practice/configuration-mastery-checkpoint` and `/api/practice/configuration-mastery-checkpoint`; the checkpoint scores configuration practice, returns promote/repeat decisions, repeat triggers, handoff routes, next actions, commands, and quality-gate status.
- Added configuration spaced review routes at `/practice/configuration-spaced-review` and `/api/practice/configuration-spaced-review`; the schedule turns configuration mastery into day 1, day 3, and day 7 recall prompts, rebuild tasks, defense prompts, promotion criteria, commands, and quality-gate status.
- Added configuration evidence archive routes at `/practice/configuration-evidence-archive` and `/api/practice/configuration-evidence-archive`; the archive turns spaced review evidence into retrieval keys, proof bundles, reuse targets, retrieval prompts, commands, and quality-gate status.
- Added configuration learning pipeline routes at `/practice/configuration-learning-pipeline` and `/api/practice/configuration-learning-pipeline`; the pipeline centralizes the app/auth configuration flow from readiness through evidence archive and exposes stage links, archive summary, quality-gate status, and progress payload.
- Added configuration practice dashboard routes at `/practice/configuration-dashboard` and `/api/practice/configuration-dashboard`; the dashboard summarizes quality status, stage count, next recommended stage, grouped pipeline sections, archive reuse targets, commands, and progress payload.
- Added configuration risk register routes at `/practice/configuration-risk-register` and `/api/practice/configuration-risk-register`; the register maps app/auth/quality-gate risks to severity, signals, mitigations, owner routes, review cadence, commands, and dashboard status.
- Added configuration remediation plan routes at `/practice/configuration-remediation-plan` and `/api/practice/configuration-remediation-plan`; the plan converts configuration risks into file-focused repair tasks with actions, owner routes, verification commands, done signals, completion criteria, and dashboard status.
- Added configuration pull request plan routes at `/practice/configuration-pull-request-plan` and `/api/practice/configuration-pull-request-plan`; the plan packages remediation work into branch, commit, changed-file, PR summary, review checklist, verification, evidence, commands, and dashboard status.
- Added configuration assessment routes at `/practice/configuration-assessment` and `/api/practice/configuration-assessment`; the assessment scores configuration remediation PR readiness with a 100-point rubric, readiness signals, improvement tasks, evidence, commands, and dashboard status.
- Added configuration decision record routes at `/practice/configuration-decision-record` and `/api/practice/configuration-decision-record`; the record turns configuration assessment output into an ADR-style payload with context, decision, alternatives, consequences, evidence, and commands.
- Added configuration operations runbook routes at `/practice/configuration-operations-runbook` and `/api/practice/configuration-operations-runbook`; the runbook turns an accepted configuration decision into operational triggers, diagnostics, rollback, handoff, evidence, and commands.
- Added configuration incident drill routes at `/practice/configuration-incident-drill` and `/api/practice/configuration-incident-drill`; the drill turns the operations runbook into a scenario, timeline, diagnosis steps, patch plan, recovery evidence, and handoff.
- Added configuration incident postmortem routes at `/practice/configuration-incident-postmortem` and `/api/practice/configuration-incident-postmortem`; the postmortem turns the drill into a blameless learning record with impact, root cause, action items, spaced-review inputs, evidence, and commands.
- Expanded configuration spaced reviews so day 1, day 3, and day 7 cards include incident-memory prompts from the postmortem, keeping recovery lessons in the long-term review loop.
- Expanded configuration evidence archives so incident/postmortem memory is retrievable through archive keys, proof bundle entries, reuse targets, and prompts without adding service dependency cycles.
- Added configuration archive retrieval routes at `/practice/configuration-archive-retrieval` and `/api/practice/configuration-archive-retrieval`; the drill turns archived proof into portfolio, interview, and incident recovery retrieval cases with quality checks.
- Added configuration evidence reuse plan routes at `/practice/configuration-evidence-reuse-plan` and `/api/practice/configuration-evidence-reuse-plan`; the plan turns retrieved proof into portfolio notes, interview answers, review comments, and incident recovery notes.
- Added configuration portfolio brief routes at `/practice/configuration-portfolio-brief` and `/api/practice/configuration-portfolio-brief`; the brief packages reused configuration evidence into a portfolio-ready artifact with headline, proof table, talking points, and review checklist.
- Added configuration portfolio review routes at `/practice/configuration-portfolio-review` and `/api/practice/configuration-portfolio-review`; the review scores the portfolio brief for clarity, proof quality, interview readiness, review discipline, and quality status.
- Added configuration publication checklist routes at `/practice/configuration-publication-checklist` and `/api/practice/configuration-publication-checklist`; the checklist turns a scored portfolio review into publish/hold status, publication channels, pre-publish checks, and do-not-publish rules.
- Added configuration handoff packet routes at `/practice/configuration-handoff-packet` and `/api/practice/configuration-handoff-packet`; the packet turns approved configuration evidence into handoff summary, links, required evidence, rehearsal prompts, and next actions.
- Added configuration next-session plan routes at `/practice/configuration-next-session-plan` and `/api/practice/configuration-next-session-plan`; the plan turns the handoff packet into preflight checks, timed practice blocks, deliverables, stop criteria, and verification commands.
- Added configuration session debrief routes at `/practice/configuration-session-debrief` and `/api/practice/configuration-session-debrief`; the debrief turns the follow-up session into completed outputs, evidence review, blockers, next actions, and verification commands.
- Added configuration session archive routes at `/practice/configuration-session-archive` and `/api/practice/configuration-session-archive`; the archive stores the follow-up session debrief as evidence tags, archive entries, retrieval prompts, reuse paths, and commands.
- Added configuration archive refresh plan routes at `/practice/configuration-archive-refresh-plan` and `/api/practice/configuration-archive-refresh-plan`; the plan keeps archived evidence current with refresh triggers, refresh tasks, rerun commands, remediation triggers, and quality status.
- Added configuration maintenance roadmap routes at `/practice/configuration-maintenance-roadmap` and `/api/practice/configuration-maintenance-roadmap`; the roadmap turns refresh plans into cadence, owners, health signals, escalation paths, and commands.
- Expanded the configuration learning pipeline/dashboard so risk register, remediation plan, PR plan, and assessment are first-class stages before deployment and release evidence.

## 2026-05-25 Review Notes

### Findings

- Shared partials use `href="../index.html"` for `#backLink`. This breaks from nested pages because it can resolve to `sites/index.html`, which does not exist.
- `assets/site.js` uses many `innerHTML` render paths. Some values are escaped with `escapeHtml()` or `formatRichText()`, but several JSON-backed fields are rendered raw.
- `chirper` contains a `Chirp` model and `chirps` migration, but the feature appears incomplete. The migration has only `id` and timestamps, and no route/view/controller exposes chirp CRUD.

### Environment

The observed machine did not have:

- `node`
- `php`
- `composer`

Because of this, automated JS/PHP test commands were not run.

### Recommended Follow-Up

1. Fix `#backLink` by setting the URL from `SITE_ROOT` after the shell is loaded.
2. Add or standardize safe render helpers for text inserted through `innerHTML`.
3. Decide whether `chirper` should become a complete lab. If yes, add fields, relationships, routes, UI, validation, policies, and tests.

## Documentation Added

The repository now includes:

- `AGENTS.md`
- `docs/ai-context.md`
- `docs/content-map.md`
- `docs/ai-workflows.md`
- `docs/ai-review-checklist.md`
- `docs/ai-change-log.md`

Future AI assistants should read these files before reviewing or editing code.

## 2026-05-25 Documentation Expansion

Added a deeper repository map and AI maintenance workflows:

- `docs/content-map.md` now records the static portal runtime flow, shell map, page key to URL map, JSON content files, menus, section types, search indexing, progress ID format, and Laravel lab map.
- `docs/ai-workflows.md` now records repeatable steps for reviewing the static site, adding Laravel topics, adding Interview topics, adding Vibe Coding topics, adding PHP levels, updating content, fixing render helpers, working on Laravel labs, and documenting new findings.
- `AGENTS.md` and `docs/ai-context.md` now point future AI sessions to the new files.

## 2026-05-25 Detailed Documentation Expansion

Added more complete operational and architecture documentation:

- `docs/static-site-architecture.md` documents shell loading, the main render decision tree, data loading, navigation, breadcrumbs, theme, progress, search, PHP keyword directory, roadmap behavior, rendering safety, and known couplings.
- `docs/data-schema.md` documents the JSON content contracts for root content, landing cards, hub pages, section types, mindmap sections, topic files, PHP levels, inline formatting, and stability rules.
- `docs/local-setup-and-verification.md` documents expected tools, local static-site serving, smoke-test pages, JSON validation, link checks, Laravel lab commands, Sail notes, and how to report blocked verification.
- `docs/technical-debt.md` documents known issues and recommended fix order: broken back links, raw JSON render paths, incomplete `chirper`, large `assets/site.js`, page wrapper drift, search validation gaps, missing static tests, and PowerShell encoding caveats.
- `docs/ai-review-checklist.md` now references the architecture/schema/technical-debt docs as part of review.

## 2026-05-25 Laravel Lab Inventory

Added `docs/laravel-labs-inventory.md` with per-lab details:

- `breeze`: Laravel 10/PHP 8.1 Breeze-style auth scaffold, routes, migrations, frontend dependencies, and auth/profile tests.
- `chirper`: Laravel 10/PHP 8.1 Jetstream/Livewire scaffold, current incomplete `Chirp` feature, migrations, routes, app areas, and tests.
- `jetstream`: Laravel 10/PHP 8.1 Jetstream teams scaffold, team actions/models/policy, migrations, routes, and team/auth/API tests.
- `sail`: Laravel 11/PHP 8.2 Sail-oriented app, Laravel 11 structure notes, Docker services, migrations, and minimal tests.

Updated `AGENTS.md`, `docs/ai-context.md`, `docs/ai-workflows.md`, `docs/content-map.md`, `docs/local-setup-and-verification.md`, and `docs/ai-review-checklist.md` to point future AI sessions to the inventory before editing Laravel labs.

## 2026-05-25 Design And Content Taxonomy

Added two more documentation files:

- `docs/design-system.md` captures CSS module order, visual direction, theme variables, layout rules, navigation, core component classes, dark mode, responsive behavior, accessibility notes, rendering/CSS coupling, and design guardrails.
- `docs/content-taxonomy.md` captures the learning areas: Roadmap, PHP, Laravel, Interview, Vibe Coding, Glossary, and Practice. It also records content depth rules, bilingual rules, cross-linking rules, anti-patterns, and how to decide where new content belongs.

Updated `AGENTS.md`, `docs/ai-context.md`, `docs/ai-workflows.md`, `docs/ai-review-checklist.md`, and `docs/content-map.md` so future AI sessions read these files for UI and content work.

## 2026-05-25 Laravel Coding Standards

Added `docs/laravel-coding-standards.md` with technical code rules:

- Layering: routes, Form Requests, controllers, services/actions, repositories/query classes, models, policies, jobs, events, listeners, resources, and DTOs.
- Controllers stay thin; business logic moves to services/actions.
- SQL/database access should live in repositories or query classes when it is reusable or complex.
- Models stay focused on relationships, casts, scopes, fillable fields, and small helpers.
- Validation/authorization, transactions, exceptions, external API calls, raw SQL, DTOs, comments, docblocks, naming, tests, and Blade rules are documented.
- Includes a practical Chirp-style flow showing Controller -> Form Request -> Service -> Repository -> Model.

Updated `AGENTS.md`, `docs/ai-context.md`, `docs/ai-workflows.md`, and `docs/ai-review-checklist.md` so future AI sessions read these standards before writing Laravel code.

## 2026-05-25 Engineering Standards

Added `docs/engineering-standards.md` with cross-cutting technical rules:

- database and migration safety
- query/performance review
- transactions and consistency
- API response shape
- web controller responses
- security and secret handling
- file upload safety
- logging and exception handling
- queue/job and event/listener rules
- dependency and environment/config rules
- testing strategy and naming
- code style
- Git/change management
- documentation rules
- AI-specific engineering rules

Updated `AGENTS.md`, `docs/ai-context.md`, `docs/ai-workflows.md`, and `docs/ai-review-checklist.md` so future AI sessions read this file before changing cross-cutting technical behavior.

## 2026-05-25 Content Quality Standards

Added `docs/content-quality-standards.md` with professional writing rules:

- content mission and professional depth expectations
- voice and tone rules
- bilingual English/Vietnamese quality rules
- examples and code snippet rules
- Laravel, PHP, Interview, Glossary, Practice, Vibe Coding, and Roadmap content rules
- source/reference rules
- consistency rules
- content review checklist
- publish quality bar

Updated `AGENTS.md`, `docs/ai-context.md`, `docs/ai-workflows.md`, and `docs/ai-review-checklist.md` so future AI sessions read this file before writing, rewriting, translating, or reorganizing learning content.

## 2026-05-25 Principal Laravel Architect Rules

Added `docs/principal-laravel-architect-rules.md` as the highest-priority Laravel architecture/output rule file. It captures the requested Senior/Principal Laravel Architect behavior:

- enterprise-grade production code expectations
- Clean Architecture, SOLID, PSR, DRY, KISS, YAGNI, DDD, maintainability, scalability, security, performance, and testability
- strict Laravel layering rules for Controllers, Form Requests, Services, Repositories, DTOs, Resources, Events, Jobs, Policies, Enums, Exceptions, and Value Objects
- repository/service/controller responsibilities
- mandatory PHPDoc for generated classes and methods
- English WHY-focused comments only when needed
- naming conventions
- standard API response shape
- validation, database, security, performance, testing, style, output, refactor, review, and advanced engineering rules

Updated `AGENTS.md`, `docs/ai-context.md`, `docs/ai-workflows.md`, and `docs/ai-review-checklist.md` so future AI sessions read this file before generating, refactoring, reviewing, or architecting Laravel code.

## 2026-05-25 Documentation Optimization

Added:

- `docs/README.md` as a documentation index and task-based reading guide.
- `docs/rule-precedence.md` to resolve conflicts between Principal Laravel rules, engineering standards, static-site rules, content rules, and local app inventory.
- `docs/templates.md` with reusable templates for Laravel feature plans, API endpoint documentation, code reviews, technical docs, content topics, practice tasks, and commit messages.

Updated `AGENTS.md`, `docs/ai-context.md`, and `docs/ai-workflows.md` so future AI sessions start from the index, read precedence rules, and use templates for structured output.

## 2026-05-25 Operating Protocol

Added:

- `docs/definition-of-done.md` with completion criteria for static site code, JSON content, CSS/UI, Laravel code, documentation, reviews, and blocked tasks.
- `docs/adr-guide.md` with ADR purpose, when to write one, file naming, template, quality rules, and current suggested ADR candidates.

Updated `AGENTS.md`, `docs/README.md`, `docs/ai-context.md`, and `docs/ai-workflows.md` so future AI sessions use Definition of Done and ADR guidance.

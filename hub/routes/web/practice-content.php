<?php

declare(strict_types=1);

use App\Http\Controllers\Practice\ConfigurationArchiveRefreshPlanController;
use App\Http\Controllers\Practice\ConfigurationArchiveRetrievalController;
use App\Http\Controllers\Practice\ConfigurationAssessmentController;
use App\Http\Controllers\Practice\ConfigurationChangeChecklistController;
use App\Http\Controllers\Practice\ConfigurationDecisionRecordController;
use App\Http\Controllers\Practice\ConfigurationDeploymentPlanController;
use App\Http\Controllers\Practice\ConfigurationEvidenceArchiveController;
use App\Http\Controllers\Practice\ConfigurationEvidenceReusePlanController;
use App\Http\Controllers\Practice\ConfigurationHandoffPacketController;
use App\Http\Controllers\Practice\ConfigurationIncidentDrillController;
use App\Http\Controllers\Practice\ConfigurationIncidentPostmortemController;
use App\Http\Controllers\Practice\ConfigurationInterviewBriefController;
use App\Http\Controllers\Practice\ConfigurationLearningPipelineController;
use App\Http\Controllers\Practice\ConfigurationMaintenanceRoadmapController;
use App\Http\Controllers\Practice\ConfigurationMasteryCheckpointController;
use App\Http\Controllers\Practice\ConfigurationNextSessionPlanController;
use App\Http\Controllers\Practice\ConfigurationOperationsRunbookController;
use App\Http\Controllers\Practice\ConfigurationPortfolioBriefController;
use App\Http\Controllers\Practice\ConfigurationPortfolioReviewController;
use App\Http\Controllers\Practice\ConfigurationPracticeDashboardController;
use App\Http\Controllers\Practice\ConfigurationPublicationChecklistController;
use App\Http\Controllers\Practice\ConfigurationPullRequestPlanController;
use App\Http\Controllers\Practice\ConfigurationReadinessController;
use App\Http\Controllers\Practice\ConfigurationReleaseEvidenceController;
use App\Http\Controllers\Practice\ConfigurationRemediationPlanController;
use App\Http\Controllers\Practice\ConfigurationRiskRegisterController;
use App\Http\Controllers\Practice\ConfigurationSessionArchiveController;
use App\Http\Controllers\Practice\ConfigurationSessionDebriefController;
use App\Http\Controllers\Practice\ConfigurationSpacedReviewController;
use App\Http\Controllers\Practice\ConfigurationTestPlanController;
use App\Http\Controllers\Practice\ContentImplementationBlueprintController;
use App\Http\Controllers\Practice\ContentPracticeDrillController;
use App\Http\Controllers\Practice\ContentPracticeMapController;
use App\Http\Controllers\Practice\ContentPracticeSyllabusController;
use App\Http\Controllers\Practice\GuidedImplementationChecklistController;
use App\Http\Controllers\Practice\ImplementationStarterKitController;
use App\Http\Controllers\Practice\ImplementationVerificationPlanController;
use App\Http\Controllers\Practice\PracticeQueueController;
use App\Http\Controllers\Practice\PracticeSprintController;
use App\Http\Controllers\Practice\QuestionDrillSetController;
use App\Http\Controllers\Practice\RecordPracticeWorkspaceController;
use App\Http\Controllers\Practice\SourcePracticePackController;
use App\Http\Controllers\Practice\SourcePracticePackIndexController;
use App\Http\Controllers\Practice\TechnologyCodeExampleController;
use App\Http\Controllers\Practice\TechnologyCodeExampleDetailController;
use App\Http\Controllers\Practice\TechnologyCommitPlanController;
use App\Http\Controllers\Practice\TechnologyEvidenceArchiveController;
use App\Http\Controllers\Practice\TechnologyImplementationLabController;
use App\Http\Controllers\Practice\TechnologyInterviewPackController;
use App\Http\Controllers\Practice\TechnologyLearningPipelineController;
use App\Http\Controllers\Practice\TechnologyMasteryCheckpointController;
use App\Http\Controllers\Practice\TechnologyPipelineIndexController;
use App\Http\Controllers\Practice\TechnologyPortfolioArtifactController;
use App\Http\Controllers\Practice\TechnologyPracticeBoardController;
use App\Http\Controllers\Practice\TechnologyPracticeMatrixController;
use App\Http\Controllers\Practice\TechnologyQualityPlanController;
use App\Http\Controllers\Practice\TechnologyRemediationPlanController;
use App\Http\Controllers\Practice\TechnologySkillAssessmentController;
use App\Http\Controllers\Practice\TechnologySpacedReviewController;
use Illuminate\Support\Facades\Route;

// Source-to-code practice routes that turn content records into implementation work.
// Show the source-to-practice map across integrated content.
Route::get('/practice/content-map', ContentPracticeMapController::class)->name('practice.content-map');

// Show a phased syllabus built from content sources.
Route::get('/practice/syllabus', ContentPracticeSyllabusController::class)->name('practice.syllabus');

// Show one content-backed coding drill.
Route::get('/practice/content-drill', ContentPracticeDrillController::class)->name('practice.content-drill');

// Show generated route, class, file, and test names for one record.
Route::get('/practice/implementation-blueprint', ContentImplementationBlueprintController::class)->name('practice.implementation-blueprint');

// Show a guided TDD checklist for one implementation blueprint.
Route::get('/practice/guided-checklist', GuidedImplementationChecklistController::class)->name('practice.guided-checklist');

// Show starter snippets for a focused implementation slice.
Route::get('/practice/starter-kit', ImplementationStarterKitController::class)->name('practice.starter-kit');

// Show the full workspace for one source record.
Route::get('/practice/record-workspace', RecordPracticeWorkspaceController::class)->name('practice.record-workspace');

// Show focused test, route check, smoke request, and quality-gate commands.
Route::get('/practice/verification-plan', ImplementationVerificationPlanController::class)->name('practice.verification-plan');

// Show the dashboard for app and auth configuration practice.
Route::get('/practice/configuration-dashboard', ConfigurationPracticeDashboardController::class)->name('practice.configuration-dashboard');

// Show the risk register for app and auth configuration practice.
Route::get('/practice/configuration-risk-register', ConfigurationRiskRegisterController::class)->name('practice.configuration-risk-register');

// Show remediation tasks for app and auth configuration risks.
Route::get('/practice/configuration-remediation-plan', ConfigurationRemediationPlanController::class)->name('practice.configuration-remediation-plan');

// Show pull request artifacts for configuration remediation work.
Route::get('/practice/configuration-pull-request-plan', ConfigurationPullRequestPlanController::class)->name('practice.configuration-pull-request-plan');

// Show the scored assessment for configuration remediation PR work.
Route::get('/practice/configuration-assessment', ConfigurationAssessmentController::class)->name('practice.configuration-assessment');

// Show the ADR-style decision record for configuration practice.
Route::get('/practice/configuration-decision-record', ConfigurationDecisionRecordController::class)->name('practice.configuration-decision-record');

// Show the operations runbook for configuration practice.
Route::get('/practice/configuration-operations-runbook', ConfigurationOperationsRunbookController::class)->name('practice.configuration-operations-runbook');

// Show the incident drill for configuration practice.
Route::get('/practice/configuration-incident-drill', ConfigurationIncidentDrillController::class)->name('practice.configuration-incident-drill');

// Show the postmortem for configuration incident practice.
Route::get('/practice/configuration-incident-postmortem', ConfigurationIncidentPostmortemController::class)->name('practice.configuration-incident-postmortem');

// Show the full app and auth configuration learning pipeline.
Route::get('/practice/configuration-learning-pipeline', ConfigurationLearningPipelineController::class)->name('practice.configuration-learning-pipeline');

// Show app and auth configuration readiness checks.
Route::get('/practice/configuration-readiness', ConfigurationReadinessController::class)->name('practice.configuration-readiness');

// Show a PHPUnit test plan for app and auth configuration contracts.
Route::get('/practice/configuration-test-plan', ConfigurationTestPlanController::class)->name('practice.configuration-test-plan');

// Show a review checklist for app, auth, and quality-gate configuration changes.
Route::get('/practice/configuration-change-checklist', ConfigurationChangeChecklistController::class)->name('practice.configuration-change-checklist');

// Show a deployment plan for app, auth, and quality-gate configuration changes.
Route::get('/practice/configuration-deployment-plan', ConfigurationDeploymentPlanController::class)->name('practice.configuration-deployment-plan');

// Show release evidence for app, auth, and quality-gate configuration work.
Route::get('/practice/configuration-release-evidence', ConfigurationReleaseEvidenceController::class)->name('practice.configuration-release-evidence');

// Show interview questions for app, auth, and quality-gate configuration work.
Route::get('/practice/configuration-interview-brief', ConfigurationInterviewBriefController::class)->name('practice.configuration-interview-brief');

// Show the promote-or-repeat checkpoint for configuration practice.
Route::get('/practice/configuration-mastery-checkpoint', ConfigurationMasteryCheckpointController::class)->name('practice.configuration-mastery-checkpoint');

// Show day 1, day 3, and day 7 review cards for configuration practice.
Route::get('/practice/configuration-spaced-review', ConfigurationSpacedReviewController::class)->name('practice.configuration-spaced-review');

// Show archived evidence for configuration practice.
Route::get('/practice/configuration-evidence-archive', ConfigurationEvidenceArchiveController::class)->name('practice.configuration-evidence-archive');

// Show retrieval drills for archived configuration evidence.
Route::get('/practice/configuration-archive-retrieval', ConfigurationArchiveRetrievalController::class)->name('practice.configuration-archive-retrieval');

// Show reuse tasks for retrieved configuration evidence.
Route::get('/practice/configuration-evidence-reuse-plan', ConfigurationEvidenceReusePlanController::class)->name('practice.configuration-evidence-reuse-plan');

// Show the portfolio brief for configuration evidence.
Route::get('/practice/configuration-portfolio-brief', ConfigurationPortfolioBriefController::class)->name('practice.configuration-portfolio-brief');

// Show the scored review for the configuration portfolio brief.
Route::get('/practice/configuration-portfolio-review', ConfigurationPortfolioReviewController::class)->name('practice.configuration-portfolio-review');

// Show the publication checklist for configuration evidence.
Route::get('/practice/configuration-publication-checklist', ConfigurationPublicationChecklistController::class)->name('practice.configuration-publication-checklist');

// Show the final handoff packet for configuration practice.
Route::get('/practice/configuration-handoff-packet', ConfigurationHandoffPacketController::class)->name('practice.configuration-handoff-packet');

// Show the next-session plan for configuration practice.
Route::get('/practice/configuration-next-session-plan', ConfigurationNextSessionPlanController::class)->name('practice.configuration-next-session-plan');

// Show the debrief for a configuration follow-up session.
Route::get('/practice/configuration-session-debrief', ConfigurationSessionDebriefController::class)->name('practice.configuration-session-debrief');

// Show the archive entry for a configuration follow-up session.
Route::get('/practice/configuration-session-archive', ConfigurationSessionArchiveController::class)->name('practice.configuration-session-archive');

// Show the refresh plan for archived configuration evidence.
Route::get('/practice/configuration-archive-refresh-plan', ConfigurationArchiveRefreshPlanController::class)->name('practice.configuration-archive-refresh-plan');

// Show the maintenance roadmap for configuration evidence.
Route::get('/practice/configuration-maintenance-roadmap', ConfigurationMaintenanceRoadmapController::class)->name('practice.configuration-maintenance-roadmap');

// Show an ordered queue of record-level practice tasks.
Route::get('/practice/queue', PracticeQueueController::class)->name('practice.queue');

// Show a short multi-phase practice sprint.
Route::get('/practice/sprint', PracticeSprintController::class)->name('practice.sprint');

// Show question-backed drills from filtered source records.
Route::get('/practice/question-drills', QuestionDrillSetController::class)->name('practice.question-drills');

// Show one technology board grouped by source file.
Route::get('/practice/technology-board', TechnologyPracticeBoardController::class)->name('practice.technology-board');

// Show the technology matrix inferred from source records.
Route::get('/practice/technology-matrix', TechnologyPracticeMatrixController::class)->name('practice.technology-matrix');

// Show all available technology learning pipelines.
Route::get('/practice/technology-pipelines', TechnologyPipelineIndexController::class)->name('practice.technology-pipelines');

// Show quality gates for technology learning pipelines.
Route::get('/practice/technology-quality-plan', TechnologyQualityPlanController::class)->name('practice.technology-quality-plan');

// Show technology-specific code examples generated from content records.
Route::get('/practice/technology-code-examples', TechnologyCodeExampleController::class)->name('practice.technology-code-examples');

// Show the complete learning pipeline for one inferred technology.
Route::get('/practice/technology-learning-pipeline/{technology}', TechnologyLearningPipelineController::class)->name('practice.technology-learning-pipeline');

// Show record-level code examples for one inferred technology.
Route::get('/practice/technology-code-examples/{technology}', TechnologyCodeExampleDetailController::class)->name('practice.technology-code-examples.show');

// Show an implementation lab for one inferred technology.
Route::get('/practice/technology-implementation-lab/{technology}', TechnologyImplementationLabController::class)->name('practice.technology-implementation-lab');

// Show commit-ready artifacts for one technology implementation lab.
Route::get('/practice/technology-commit-plan/{technology}', TechnologyCommitPlanController::class)->name('practice.technology-commit-plan');

// Show a portfolio-ready artifact for one inferred technology.
Route::get('/practice/technology-portfolio-artifact/{technology}', TechnologyPortfolioArtifactController::class)->name('practice.technology-portfolio-artifact');

// Show interview defense questions for one inferred technology.
Route::get('/practice/technology-interview-pack/{technology}', TechnologyInterviewPackController::class)->name('practice.technology-interview-pack');

// Show a scored skill assessment for one inferred technology.
Route::get('/practice/technology-skill-assessment/{technology}', TechnologySkillAssessmentController::class)->name('practice.technology-skill-assessment');

// Show remediation tasks for one inferred technology assessment.
Route::get('/practice/technology-remediation-plan/{technology}', TechnologyRemediationPlanController::class)->name('practice.technology-remediation-plan');

// Show a promote-or-repeat checkpoint for one inferred technology.
Route::get('/practice/technology-mastery-checkpoint/{technology}', TechnologyMasteryCheckpointController::class)->name('practice.technology-mastery-checkpoint');

// Show spaced review cards for one inferred technology.
Route::get('/practice/technology-spaced-review/{technology}', TechnologySpacedReviewController::class)->name('practice.technology-spaced-review');

// Show an evidence archive entry for one inferred technology.
Route::get('/practice/technology-evidence-archive/{technology}', TechnologyEvidenceArchiveController::class)->name('practice.technology-evidence-archive');

// Show the index of available source practice packs.
Route::get('/practice/source-packs', SourcePracticePackIndexController::class)->name('practice.source-packs.index');

// Show one source practice pack by source key.
Route::get('/practice/source-packs/{sourceKey}', SourcePracticePackController::class)->name('practice.source-pack');

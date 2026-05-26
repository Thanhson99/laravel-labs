<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ConfigurationArchiveRefreshPlanController;
use App\Http\Controllers\Api\ConfigurationArchiveRetrievalController;
use App\Http\Controllers\Api\ConfigurationAssessmentController;
use App\Http\Controllers\Api\ConfigurationChangeChecklistController;
use App\Http\Controllers\Api\ConfigurationDecisionRecordController;
use App\Http\Controllers\Api\ConfigurationDeploymentPlanController;
use App\Http\Controllers\Api\ConfigurationEvidenceArchiveController;
use App\Http\Controllers\Api\ConfigurationEvidenceReusePlanController;
use App\Http\Controllers\Api\ConfigurationHandoffPacketController;
use App\Http\Controllers\Api\ConfigurationIncidentDrillController;
use App\Http\Controllers\Api\ConfigurationIncidentPostmortemController;
use App\Http\Controllers\Api\ConfigurationInterviewBriefController;
use App\Http\Controllers\Api\ConfigurationLearningPipelineController;
use App\Http\Controllers\Api\ConfigurationMaintenanceRoadmapController;
use App\Http\Controllers\Api\ConfigurationMasteryCheckpointController;
use App\Http\Controllers\Api\ConfigurationNextSessionPlanController;
use App\Http\Controllers\Api\ConfigurationOperationsRunbookController;
use App\Http\Controllers\Api\ConfigurationPortfolioBriefController;
use App\Http\Controllers\Api\ConfigurationPortfolioReviewController;
use App\Http\Controllers\Api\ConfigurationPracticeDashboardController;
use App\Http\Controllers\Api\ConfigurationPublicationChecklistController;
use App\Http\Controllers\Api\ConfigurationPullRequestPlanController;
use App\Http\Controllers\Api\ConfigurationReadinessController;
use App\Http\Controllers\Api\ConfigurationReleaseEvidenceController;
use App\Http\Controllers\Api\ConfigurationRemediationPlanController;
use App\Http\Controllers\Api\ConfigurationRiskRegisterController;
use App\Http\Controllers\Api\ConfigurationSessionArchiveController;
use App\Http\Controllers\Api\ConfigurationSessionDebriefController;
use App\Http\Controllers\Api\ConfigurationSpacedReviewController;
use App\Http\Controllers\Api\ConfigurationTestPlanController;
use App\Http\Controllers\Api\ContentImplementationBlueprintController;
use App\Http\Controllers\Api\ContentPracticeDrillController;
use App\Http\Controllers\Api\ContentPracticeMapController;
use App\Http\Controllers\Api\ContentPracticeSyllabusController;
use App\Http\Controllers\Api\GuidedImplementationChecklistController;
use App\Http\Controllers\Api\ImplementationStarterKitController;
use App\Http\Controllers\Api\ImplementationVerificationPlanController;
use App\Http\Controllers\Api\PracticeQueueController;
use App\Http\Controllers\Api\PracticeSprintController;
use App\Http\Controllers\Api\QuestionDrillSetController;
use App\Http\Controllers\Api\RecordPracticeWorkspaceController;
use App\Http\Controllers\Api\SourcePracticePackController;
use App\Http\Controllers\Api\SourcePracticePackIndexController;
use App\Http\Controllers\Api\TechnologyCodeExampleController;
use App\Http\Controllers\Api\TechnologyCodeExampleDetailController;
use App\Http\Controllers\Api\TechnologyCommitPlanController;
use App\Http\Controllers\Api\TechnologyEvidenceArchiveController;
use App\Http\Controllers\Api\TechnologyImplementationLabController;
use App\Http\Controllers\Api\TechnologyInterviewPackController;
use App\Http\Controllers\Api\TechnologyLearningPipelineController;
use App\Http\Controllers\Api\TechnologyMasteryCheckpointController;
use App\Http\Controllers\Api\TechnologyPipelineIndexController;
use App\Http\Controllers\Api\TechnologyPortfolioArtifactController;
use App\Http\Controllers\Api\TechnologyPracticeBoardController;
use App\Http\Controllers\Api\TechnologyPracticeMatrixController;
use App\Http\Controllers\Api\TechnologyQualityPlanController;
use App\Http\Controllers\Api\TechnologyRemediationPlanController;
use App\Http\Controllers\Api\TechnologySkillAssessmentController;
use App\Http\Controllers\Api\TechnologySpacedReviewController;
use Illuminate\Support\Facades\Route;

// Source-to-code API endpoints used by the practice workspace and drills.
// Return the source-to-practice map across integrated content.
Route::get('/content-map', ContentPracticeMapController::class)->name('content-map');

// Return a phased syllabus built from content sources.
Route::get('/syllabus', ContentPracticeSyllabusController::class)->name('syllabus');

// Return one content-backed coding drill.
Route::get('/content-drill', ContentPracticeDrillController::class)->name('content-drill');

// Return generated route, class, file, and test names for one record.
Route::get('/implementation-blueprint', ContentImplementationBlueprintController::class)->name('implementation-blueprint');

// Return a guided TDD checklist for one implementation blueprint.
Route::get('/guided-checklist', GuidedImplementationChecklistController::class)->name('guided-checklist');

// Return starter snippets for a focused implementation slice.
Route::get('/starter-kit', ImplementationStarterKitController::class)->name('starter-kit');

// Return the full workspace for one source record.
Route::get('/record-workspace', RecordPracticeWorkspaceController::class)->name('record-workspace');

// Return focused test, route check, smoke request, and quality-gate commands.
Route::get('/verification-plan', ImplementationVerificationPlanController::class)->name('verification-plan');

// Return the dashboard for app and auth configuration practice.
Route::get('/configuration-dashboard', ConfigurationPracticeDashboardController::class)->name('configuration-dashboard');

// Return the risk register for app and auth configuration practice.
Route::get('/configuration-risk-register', ConfigurationRiskRegisterController::class)->name('configuration-risk-register');

// Return remediation tasks for app and auth configuration risks.
Route::get('/configuration-remediation-plan', ConfigurationRemediationPlanController::class)->name('configuration-remediation-plan');

// Return pull request artifacts for configuration remediation work.
Route::get('/configuration-pull-request-plan', ConfigurationPullRequestPlanController::class)->name('configuration-pull-request-plan');

// Return the scored assessment for configuration remediation PR work.
Route::get('/configuration-assessment', ConfigurationAssessmentController::class)->name('configuration-assessment');

// Return the ADR-style decision record for configuration practice.
Route::get('/configuration-decision-record', ConfigurationDecisionRecordController::class)->name('configuration-decision-record');

// Return the operations runbook for configuration practice.
Route::get('/configuration-operations-runbook', ConfigurationOperationsRunbookController::class)->name('configuration-operations-runbook');

// Return the incident drill for configuration practice.
Route::get('/configuration-incident-drill', ConfigurationIncidentDrillController::class)->name('configuration-incident-drill');

// Return the postmortem for configuration incident practice.
Route::get('/configuration-incident-postmortem', ConfigurationIncidentPostmortemController::class)->name('configuration-incident-postmortem');

// Return the full app and auth configuration learning pipeline.
Route::get('/configuration-learning-pipeline', ConfigurationLearningPipelineController::class)->name('configuration-learning-pipeline');

// Return app and auth configuration readiness checks.
Route::get('/configuration-readiness', ConfigurationReadinessController::class)->name('configuration-readiness');

// Return a PHPUnit test plan for app and auth configuration contracts.
Route::get('/configuration-test-plan', ConfigurationTestPlanController::class)->name('configuration-test-plan');

// Return a review checklist for app, auth, and quality-gate configuration changes.
Route::get('/configuration-change-checklist', ConfigurationChangeChecklistController::class)->name('configuration-change-checklist');

// Return a deployment plan for app, auth, and quality-gate configuration changes.
Route::get('/configuration-deployment-plan', ConfigurationDeploymentPlanController::class)->name('configuration-deployment-plan');

// Return release evidence for app, auth, and quality-gate configuration work.
Route::get('/configuration-release-evidence', ConfigurationReleaseEvidenceController::class)->name('configuration-release-evidence');

// Return interview questions for app, auth, and quality-gate configuration work.
Route::get('/configuration-interview-brief', ConfigurationInterviewBriefController::class)->name('configuration-interview-brief');

// Return the promote-or-repeat checkpoint for configuration practice.
Route::get('/configuration-mastery-checkpoint', ConfigurationMasteryCheckpointController::class)->name('configuration-mastery-checkpoint');

// Return day 1, day 3, and day 7 review cards for configuration practice.
Route::get('/configuration-spaced-review', ConfigurationSpacedReviewController::class)->name('configuration-spaced-review');

// Return archived evidence for configuration practice.
Route::get('/configuration-evidence-archive', ConfigurationEvidenceArchiveController::class)->name('configuration-evidence-archive');

// Return retrieval drills for archived configuration evidence.
Route::get('/configuration-archive-retrieval', ConfigurationArchiveRetrievalController::class)->name('configuration-archive-retrieval');

// Return reuse tasks for retrieved configuration evidence.
Route::get('/configuration-evidence-reuse-plan', ConfigurationEvidenceReusePlanController::class)->name('configuration-evidence-reuse-plan');

// Return the portfolio brief for configuration evidence.
Route::get('/configuration-portfolio-brief', ConfigurationPortfolioBriefController::class)->name('configuration-portfolio-brief');

// Return the scored review for the configuration portfolio brief.
Route::get('/configuration-portfolio-review', ConfigurationPortfolioReviewController::class)->name('configuration-portfolio-review');

// Return the publication checklist for configuration evidence.
Route::get('/configuration-publication-checklist', ConfigurationPublicationChecklistController::class)->name('configuration-publication-checklist');

// Return the final handoff packet for configuration practice.
Route::get('/configuration-handoff-packet', ConfigurationHandoffPacketController::class)->name('configuration-handoff-packet');

// Return the next-session plan for configuration practice.
Route::get('/configuration-next-session-plan', ConfigurationNextSessionPlanController::class)->name('configuration-next-session-plan');

// Return the debrief for a configuration follow-up session.
Route::get('/configuration-session-debrief', ConfigurationSessionDebriefController::class)->name('configuration-session-debrief');

// Return the archive entry for a configuration follow-up session.
Route::get('/configuration-session-archive', ConfigurationSessionArchiveController::class)->name('configuration-session-archive');

// Return the refresh plan for archived configuration evidence.
Route::get('/configuration-archive-refresh-plan', ConfigurationArchiveRefreshPlanController::class)->name('configuration-archive-refresh-plan');

// Return the maintenance roadmap for configuration evidence.
Route::get('/configuration-maintenance-roadmap', ConfigurationMaintenanceRoadmapController::class)->name('configuration-maintenance-roadmap');

// Return an ordered queue of record-level practice tasks.
Route::get('/queue', PracticeQueueController::class)->name('queue');

// Return a short multi-phase practice sprint.
Route::get('/sprint', PracticeSprintController::class)->name('sprint');

// Return question-backed drills from filtered source records.
Route::get('/question-drills', QuestionDrillSetController::class)->name('question-drills');

// Return one technology board grouped by source file.
Route::get('/technology-board', TechnologyPracticeBoardController::class)->name('technology-board');

// Return the technology matrix inferred from source records.
Route::get('/technology-matrix', TechnologyPracticeMatrixController::class)->name('technology-matrix');

// Return all available technology learning pipelines.
Route::get('/technology-pipelines', TechnologyPipelineIndexController::class)->name('technology-pipelines');

// Return quality gates for technology learning pipelines.
Route::get('/technology-quality-plan', TechnologyQualityPlanController::class)->name('technology-quality-plan');

// Return technology-specific code examples generated from content records.
Route::get('/technology-code-examples', TechnologyCodeExampleController::class)->name('technology-code-examples');

// Return the complete learning pipeline for one inferred technology.
Route::get('/technology-learning-pipeline/{technology}', TechnologyLearningPipelineController::class)->name('technology-learning-pipeline');

// Return record-level code examples for one inferred technology.
Route::get('/technology-code-examples/{technology}', TechnologyCodeExampleDetailController::class)->name('technology-code-examples.show');

// Return an implementation lab for one inferred technology.
Route::get('/technology-implementation-lab/{technology}', TechnologyImplementationLabController::class)->name('technology-implementation-lab');

// Return commit-ready artifacts for one technology implementation lab.
Route::get('/technology-commit-plan/{technology}', TechnologyCommitPlanController::class)->name('technology-commit-plan');

// Return a portfolio-ready artifact for one inferred technology.
Route::get('/technology-portfolio-artifact/{technology}', TechnologyPortfolioArtifactController::class)->name('technology-portfolio-artifact');

// Return interview defense questions for one inferred technology.
Route::get('/technology-interview-pack/{technology}', TechnologyInterviewPackController::class)->name('technology-interview-pack');

// Return a scored skill assessment for one inferred technology.
Route::get('/technology-skill-assessment/{technology}', TechnologySkillAssessmentController::class)->name('technology-skill-assessment');

// Return remediation tasks for one inferred technology assessment.
Route::get('/technology-remediation-plan/{technology}', TechnologyRemediationPlanController::class)->name('technology-remediation-plan');

// Return a promote-or-repeat checkpoint for one inferred technology.
Route::get('/technology-mastery-checkpoint/{technology}', TechnologyMasteryCheckpointController::class)->name('technology-mastery-checkpoint');

// Return spaced review cards for one inferred technology.
Route::get('/technology-spaced-review/{technology}', TechnologySpacedReviewController::class)->name('technology-spaced-review');

// Return an evidence archive entry for one inferred technology.
Route::get('/technology-evidence-archive/{technology}', TechnologyEvidenceArchiveController::class)->name('technology-evidence-archive');

// Return the index of available source practice packs.
Route::get('/source-packs', SourcePracticePackIndexController::class)->name('source-packs.index');

// Return one source practice pack by source key.
Route::get('/source-packs/{sourceKey}', SourcePracticePackController::class)->name('source-pack');

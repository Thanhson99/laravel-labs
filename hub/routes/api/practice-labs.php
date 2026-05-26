<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PracticeArchiveRetrievalLabController;
use App\Http\Controllers\Api\PracticeAssessmentLabController;
use App\Http\Controllers\Api\PracticeBugFixLabController;
use App\Http\Controllers\Api\PracticeCapstoneLabController;
use App\Http\Controllers\Api\PracticeChallengeEvidenceReviewLabController;
use App\Http\Controllers\Api\PracticeChallengeExecutionLabController;
use App\Http\Controllers\Api\PracticeChallengePromotionLabController;
use App\Http\Controllers\Api\PracticeCheckpointExamLabController;
use App\Http\Controllers\Api\PracticeCompetencyMapLabController;
use App\Http\Controllers\Api\PracticeDemoScriptLabController;
use App\Http\Controllers\Api\PracticeEvidenceReusePlanLabController;
use App\Http\Controllers\Api\PracticeInterviewDefenseLabController;
use App\Http\Controllers\Api\PracticeKnowledgeGapLabController;
use App\Http\Controllers\Api\PracticeLiveCodingLabController;
use App\Http\Controllers\Api\PracticeMasteryEvidenceLabController;
use App\Http\Controllers\Api\PracticeMasteryPathLabController;
use App\Http\Controllers\Api\PracticeMentorFeedbackLabController;
use App\Http\Controllers\Api\PracticeNextChallengeLabController;
use App\Http\Controllers\Api\PracticeNextSessionHandoffLabController;
use App\Http\Controllers\Api\PracticePortfolioLabController;
use App\Http\Controllers\Api\PracticePullRequestLabController;
use App\Http\Controllers\Api\PracticeRefactorLabController;
use App\Http\Controllers\Api\PracticeReleaseReadinessLabController;
use App\Http\Controllers\Api\PracticeRemediationLabController;
use App\Http\Controllers\Api\PracticeRetrospectiveLabController;
use App\Http\Controllers\Api\PracticeReviewLabController;
use App\Http\Controllers\Api\PracticeRotationLabController;
use App\Http\Controllers\Api\PracticeSessionArchiveLabController;
use App\Http\Controllers\Api\PracticeSessionDebriefLabController;
use App\Http\Controllers\Api\PracticeSessionReplayLabController;
use App\Http\Controllers\Api\PracticeSpacedRepetitionLabController;
use App\Http\Controllers\Api\PracticeTddLabController;
use App\Http\Controllers\Api\PracticeWeeklyReportLabController;
use Illuminate\Support\Facades\Route;

// Guided lab-chain API endpoints for generated practice workflows.
// Return the scoring rubric for one implementation artifact.
Route::get('/assessment-lab', PracticeAssessmentLabController::class)->name('assessment-lab');

// Return bug-fix drills from live coding rounds.
Route::get('/bug-fix-lab', PracticeBugFixLabController::class)->name('bug-fix-lab');

// Return a technology-level capstone plan.
Route::get('/capstone-lab', PracticeCapstoneLabController::class)->name('capstone-lab');

// Return a timed checkpoint exam for one technology.
Route::get('/checkpoint-exam-lab', PracticeCheckpointExamLabController::class)->name('checkpoint-exam-lab');

// Return a demo script for presenting a practice week.
Route::get('/demo-script-lab', PracticeDemoScriptLabController::class)->name('demo-script-lab');

// Return timed live-coding rounds from a demo script.
Route::get('/live-coding-lab', PracticeLiveCodingLabController::class)->name('live-coding-lab');

// Return pull-request artifacts for a remediated task.
Route::get('/pull-request-lab', PracticePullRequestLabController::class)->name('pull-request-lab');

// Return safe refactor tasks after bug-fix verification.
Route::get('/refactor-lab', PracticeRefactorLabController::class)->name('refactor-lab');

// Return release notes, smoke checks, and rollback notes for refactor work.
Route::get('/release-readiness-lab', PracticeReleaseReadinessLabController::class)->name('release-readiness-lab');

// Return concrete fix tasks from a review checklist.
Route::get('/remediation-lab', PracticeRemediationLabController::class)->name('remediation-lab');

// Return review checklist items for a TDD implementation.
Route::get('/review-lab', PracticeReviewLabController::class)->name('review-lab');

// Return retrospective prompts and next actions after assessment.
Route::get('/retrospective-lab', PracticeRetrospectiveLabController::class)->name('retrospective-lab');

// Return a day-by-day practice rotation.
Route::get('/rotation-lab', PracticeRotationLabController::class)->name('rotation-lab');

// Return repeated review checkpoints for knowledge gaps.
Route::get('/spaced-repetition-lab', PracticeSpacedRepetitionLabController::class)->name('spaced-repetition-lab');

// Return the Red-Green-Refactor lab for one source record.
Route::get('/tdd-lab', PracticeTddLabController::class)->name('tdd-lab');

// Return a weekly report template from rotation output.
Route::get('/weekly-report-lab', PracticeWeeklyReportLabController::class)->name('weekly-report-lab');

// Advanced evidence API endpoints for mastery, interview readiness, and reuse.
// Return retrieval cards from archived session evidence.
Route::get('/archive-retrieval-lab', PracticeArchiveRetrievalLabController::class)->name('archive-retrieval-lab');

// Return evidence review cards after challenge execution.
Route::get('/challenge-evidence-review-lab', PracticeChallengeEvidenceReviewLabController::class)->name('challenge-evidence-review-lab');

// Return executable steps for accepted challenge cards.
Route::get('/challenge-execution-lab', PracticeChallengeExecutionLabController::class)->name('challenge-execution-lab');

// Return promote-or-repeat decisions from reviewed challenge evidence.
Route::get('/challenge-promotion-lab', PracticeChallengePromotionLabController::class)->name('challenge-promotion-lab');

// Return competency levels from mastery evidence.
Route::get('/competency-map-lab', PracticeCompetencyMapLabController::class)->name('competency-map-lab');

// Return reuse plans for portfolio, interview, and review artifacts.
Route::get('/evidence-reuse-plan-lab', PracticeEvidenceReusePlanLabController::class)->name('evidence-reuse-plan-lab');

// Return interview defense questions and answer outlines.
Route::get('/interview-defense-lab', PracticeInterviewDefenseLabController::class)->name('interview-defense-lab');

// Return coding actions for weak interview-defense answers.
Route::get('/knowledge-gap-lab', PracticeKnowledgeGapLabController::class)->name('knowledge-gap-lab');

// Return mastery evidence cards from repeated review checkpoints.
Route::get('/mastery-evidence-lab', PracticeMasteryEvidenceLabController::class)->name('mastery-evidence-lab');

// Return multi-technology mastery milestones.
Route::get('/mastery-path-lab', PracticeMasteryPathLabController::class)->name('mastery-path-lab');

// Return mentor feedback prompts for capstone tasks.
Route::get('/mentor-feedback-lab', PracticeMentorFeedbackLabController::class)->name('mentor-feedback-lab');

// Return recommended harder labs or reinforcement challenges.
Route::get('/next-challenge-lab', PracticeNextChallengeLabController::class)->name('next-challenge-lab');

// Return next-session handoff cards from promotion decisions.
Route::get('/next-session-handoff-lab', PracticeNextSessionHandoffLabController::class)->name('next-session-handoff-lab');

// Return a portfolio entry template from retrospective evidence.
Route::get('/portfolio-lab', PracticePortfolioLabController::class)->name('portfolio-lab');

// Return archive entries from session debrief cards.
Route::get('/session-archive-lab', PracticeSessionArchiveLabController::class)->name('session-archive-lab');

// Return debrief prompts after replaying a session.
Route::get('/session-debrief-lab', PracticeSessionDebriefLabController::class)->name('session-debrief-lab');

// Return replay rounds from next-session handoff cards.
Route::get('/session-replay-lab', PracticeSessionReplayLabController::class)->name('session-replay-lab');

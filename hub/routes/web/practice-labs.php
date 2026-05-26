<?php

declare(strict_types=1);

use App\Http\Controllers\Practice\PracticeArchiveRetrievalLabController;
use App\Http\Controllers\Practice\PracticeAssessmentLabController;
use App\Http\Controllers\Practice\PracticeBugFixLabController;
use App\Http\Controllers\Practice\PracticeCapstoneLabController;
use App\Http\Controllers\Practice\PracticeChallengeEvidenceReviewLabController;
use App\Http\Controllers\Practice\PracticeChallengeExecutionLabController;
use App\Http\Controllers\Practice\PracticeChallengePromotionLabController;
use App\Http\Controllers\Practice\PracticeCheckpointExamLabController;
use App\Http\Controllers\Practice\PracticeCompetencyMapLabController;
use App\Http\Controllers\Practice\PracticeDemoScriptLabController;
use App\Http\Controllers\Practice\PracticeEvidenceReusePlanLabController;
use App\Http\Controllers\Practice\PracticeInterviewDefenseLabController;
use App\Http\Controllers\Practice\PracticeKnowledgeGapLabController;
use App\Http\Controllers\Practice\PracticeLiveCodingLabController;
use App\Http\Controllers\Practice\PracticeMasteryEvidenceLabController;
use App\Http\Controllers\Practice\PracticeMasteryPathLabController;
use App\Http\Controllers\Practice\PracticeMentorFeedbackLabController;
use App\Http\Controllers\Practice\PracticeNextChallengeLabController;
use App\Http\Controllers\Practice\PracticeNextSessionHandoffLabController;
use App\Http\Controllers\Practice\PracticePortfolioLabController;
use App\Http\Controllers\Practice\PracticePullRequestLabController;
use App\Http\Controllers\Practice\PracticeRefactorLabController;
use App\Http\Controllers\Practice\PracticeReleaseReadinessLabController;
use App\Http\Controllers\Practice\PracticeRemediationLabController;
use App\Http\Controllers\Practice\PracticeRetrospectiveLabController;
use App\Http\Controllers\Practice\PracticeReviewLabController;
use App\Http\Controllers\Practice\PracticeRotationLabController;
use App\Http\Controllers\Practice\PracticeSessionArchiveLabController;
use App\Http\Controllers\Practice\PracticeSessionDebriefLabController;
use App\Http\Controllers\Practice\PracticeSessionReplayLabController;
use App\Http\Controllers\Practice\PracticeSpacedRepetitionLabController;
use App\Http\Controllers\Practice\PracticeTddLabController;
use App\Http\Controllers\Practice\PracticeWeeklyReportLabController;
use Illuminate\Support\Facades\Route;

// Guided lab chain from implementation to review, release, mastery, and reuse.
// Show the scoring rubric for one implementation artifact.
Route::get('/practice/assessment-lab', PracticeAssessmentLabController::class)->name('practice.assessment-lab');

// Show bug-fix drills from live coding rounds.
Route::get('/practice/bug-fix-lab', PracticeBugFixLabController::class)->name('practice.bug-fix-lab');

// Show a technology-level capstone plan.
Route::get('/practice/capstone-lab', PracticeCapstoneLabController::class)->name('practice.capstone-lab');

// Show a timed checkpoint exam for one technology.
Route::get('/practice/checkpoint-exam-lab', PracticeCheckpointExamLabController::class)->name('practice.checkpoint-exam-lab');

// Show a demo script for presenting a practice week.
Route::get('/practice/demo-script-lab', PracticeDemoScriptLabController::class)->name('practice.demo-script-lab');

// Show timed live-coding rounds from a demo script.
Route::get('/practice/live-coding-lab', PracticeLiveCodingLabController::class)->name('practice.live-coding-lab');

// Show pull-request artifacts for a remediated task.
Route::get('/practice/pull-request-lab', PracticePullRequestLabController::class)->name('practice.pull-request-lab');

// Show safe refactor tasks after bug-fix verification.
Route::get('/practice/refactor-lab', PracticeRefactorLabController::class)->name('practice.refactor-lab');

// Show release notes, smoke checks, and rollback notes for refactor work.
Route::get('/practice/release-readiness-lab', PracticeReleaseReadinessLabController::class)->name('practice.release-readiness-lab');

// Show concrete fix tasks from a review checklist.
Route::get('/practice/remediation-lab', PracticeRemediationLabController::class)->name('practice.remediation-lab');

// Show review checklist items for a TDD implementation.
Route::get('/practice/review-lab', PracticeReviewLabController::class)->name('practice.review-lab');

// Show retrospective prompts and next actions after assessment.
Route::get('/practice/retrospective-lab', PracticeRetrospectiveLabController::class)->name('practice.retrospective-lab');

// Show a day-by-day practice rotation.
Route::get('/practice/rotation-lab', PracticeRotationLabController::class)->name('practice.rotation-lab');

// Show repeated review checkpoints for knowledge gaps.
Route::get('/practice/spaced-repetition-lab', PracticeSpacedRepetitionLabController::class)->name('practice.spaced-repetition-lab');

// Show the Red-Green-Refactor lab for one source record.
Route::get('/practice/tdd-lab', PracticeTddLabController::class)->name('practice.tdd-lab');

// Show a weekly report template from rotation output.
Route::get('/practice/weekly-report-lab', PracticeWeeklyReportLabController::class)->name('practice.weekly-report-lab');

// Advanced evidence pipeline routes for interview readiness and long-running progress.
// Show retrieval cards from archived session evidence.
Route::get('/practice/archive-retrieval-lab', PracticeArchiveRetrievalLabController::class)->name('practice.archive-retrieval-lab');

// Show evidence review cards after challenge execution.
Route::get('/practice/challenge-evidence-review-lab', PracticeChallengeEvidenceReviewLabController::class)->name('practice.challenge-evidence-review-lab');

// Show executable steps for accepted challenge cards.
Route::get('/practice/challenge-execution-lab', PracticeChallengeExecutionLabController::class)->name('practice.challenge-execution-lab');

// Show promote-or-repeat decisions from reviewed challenge evidence.
Route::get('/practice/challenge-promotion-lab', PracticeChallengePromotionLabController::class)->name('practice.challenge-promotion-lab');

// Show competency levels from mastery evidence.
Route::get('/practice/competency-map-lab', PracticeCompetencyMapLabController::class)->name('practice.competency-map-lab');

// Show reuse plans for portfolio, interview, and review artifacts.
Route::get('/practice/evidence-reuse-plan-lab', PracticeEvidenceReusePlanLabController::class)->name('practice.evidence-reuse-plan-lab');

// Show interview defense questions and answer outlines.
Route::get('/practice/interview-defense-lab', PracticeInterviewDefenseLabController::class)->name('practice.interview-defense-lab');

// Show coding actions for weak interview-defense answers.
Route::get('/practice/knowledge-gap-lab', PracticeKnowledgeGapLabController::class)->name('practice.knowledge-gap-lab');

// Show mastery evidence cards from repeated review checkpoints.
Route::get('/practice/mastery-evidence-lab', PracticeMasteryEvidenceLabController::class)->name('practice.mastery-evidence-lab');

// Show multi-technology mastery milestones.
Route::get('/practice/mastery-path-lab', PracticeMasteryPathLabController::class)->name('practice.mastery-path-lab');

// Show mentor feedback prompts for capstone tasks.
Route::get('/practice/mentor-feedback-lab', PracticeMentorFeedbackLabController::class)->name('practice.mentor-feedback-lab');

// Show recommended harder labs or reinforcement challenges.
Route::get('/practice/next-challenge-lab', PracticeNextChallengeLabController::class)->name('practice.next-challenge-lab');

// Show next-session handoff cards from promotion decisions.
Route::get('/practice/next-session-handoff-lab', PracticeNextSessionHandoffLabController::class)->name('practice.next-session-handoff-lab');

// Show a portfolio entry template from retrospective evidence.
Route::get('/practice/portfolio-lab', PracticePortfolioLabController::class)->name('practice.portfolio-lab');

// Show archive entries from session debrief cards.
Route::get('/practice/session-archive-lab', PracticeSessionArchiveLabController::class)->name('practice.session-archive-lab');

// Show debrief prompts after replaying a session.
Route::get('/practice/session-debrief-lab', PracticeSessionDebriefLabController::class)->name('practice.session-debrief-lab');

// Show replay rounds from next-session handoff cards.
Route::get('/practice/session-replay-lab', PracticeSessionReplayLabController::class)->name('practice.session-replay-lab');

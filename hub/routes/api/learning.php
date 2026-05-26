<?php

declare(strict_types=1);

use App\Http\Controllers\Api\LearningContentApiController;
use Illuminate\Support\Facades\Route;

// Learning-content API endpoints for source records, generated plans, quizzes, and analytics.
// Return the learning dashboard payload.
Route::get('/', [LearningContentApiController::class, 'index'])->name('index');

// Return all integrated learning content sources.
Route::get('/sources', [LearningContentApiController::class, 'sources'])->name('sources');

// Return one source file and its records.
Route::get('/sources/{sourceKey}', [LearningContentApiController::class, 'source'])->name('sources.show');

// Return the searchable question bank.
Route::get('/questions', [LearningContentApiController::class, 'questions'])->name('questions');

// Return generated quiz cards from source records.
Route::get('/quiz', [LearningContentApiController::class, 'quiz'])->name('quiz');

// Return a generated study plan from source content.
Route::get('/study-plan', [LearningContentApiController::class, 'studyPlan'])->name('study-plan');

// Return analytics for content coverage and source distribution.
Route::get('/analytics', [LearningContentApiController::class, 'analytics'])->name('analytics');

// Return generated hands-on lab suggestions.
Route::get('/labs', [LearningContentApiController::class, 'labs'])->name('labs');

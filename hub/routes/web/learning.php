<?php

declare(strict_types=1);

use App\Http\Controllers\Learning\AnalyticsController;
use App\Http\Controllers\Learning\DashboardController;
use App\Http\Controllers\Learning\LabController;
use App\Http\Controllers\Learning\QuestionBankController;
use App\Http\Controllers\Learning\QuizController;
use App\Http\Controllers\Learning\SourceController;
use App\Http\Controllers\Learning\StudyPlanController;
use Illuminate\Support\Facades\Route;

// Learning reference pages backed by integrated source content.
// Show the learning dashboard.
Route::get('/', DashboardController::class)->name('learning.dashboard');

// Show source coverage and content analytics.
Route::get('/analytics', AnalyticsController::class)->name('learning.analytics');

// Show generated hands-on learning labs.
Route::get('/labs', LabController::class)->name('learning.labs');

// Show the searchable question bank.
Route::get('/questions', QuestionBankController::class)->name('learning.questions');

// Show quiz practice generated from source records.
Route::get('/quiz', QuizController::class)->name('learning.quiz');

// Show a generated study plan.
Route::get('/study-plan', StudyPlanController::class)->name('learning.study-plan');

// Show one integrated source file.
Route::get('/sources/{sourceKey}', SourceController::class)->name('learning.sources.show');

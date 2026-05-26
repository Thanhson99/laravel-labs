<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AsyncJobPlanController;
use App\Http\Controllers\Api\AuthorizationPolicyPlanController;
use App\Http\Controllers\Api\CacheStrategyPlanController;
use App\Http\Controllers\Api\CollectionFilterPreviewController;
use App\Http\Controllers\Api\ContainerBindingPlanController;
use App\Http\Controllers\Api\EventListenerPlanController;
use App\Http\Controllers\Api\FileStoragePlanController;
use App\Http\Controllers\Api\HttpRequestFlowTraceController;
use App\Http\Controllers\Api\PracticeProgressChecklistController;
use App\Http\Controllers\Api\PracticeQualityGateController;
use App\Http\Controllers\Api\PracticeSessionPlanController;
use App\Http\Controllers\Api\PracticeTopicController;
use App\Http\Controllers\Api\RateLimitPlanController;
use App\Http\Controllers\Api\RuntimeSmokeCheckController;
use App\Http\Controllers\Api\SecurityEscapePreviewController;
use App\Http\Controllers\Practice\Workbench\NameNormalizerController;
use Illuminate\Support\Facades\Route;

// Practice action API endpoints for learner submissions, session planning, and runtime checks.
// Normalize a submitted name through the runnable workbench exercise.
Route::post('/name-normalizer', [NameNormalizerController::class, 'store'])->name('name-normalizer.store');

// Store a practice topic through a validated API slice.
Route::post('/topics', [PracticeTopicController::class, 'store'])->name('topics.store');

// Return a Laravel HTTP request trace through route, validation, controller, service, and response layers.
Route::post('/http-request-flow', HttpRequestFlowTraceController::class)->name('http-request-flow.store');

// Evaluate a learner's verification checklist against the quality gate.
Route::post('/quality-gate', [PracticeQualityGateController::class, 'store'])->name('quality-gate.store');

// Return escaped preview data for Blade security practice.
Route::post('/security-escape-preview', SecurityEscapePreviewController::class)->name('security-escape-preview.store');

// Return filtered and paginated records for list-page practice.
Route::post('/collection-filter-preview', CollectionFilterPreviewController::class)->name('collection-filter-preview.store');

// Return an async job plan for queue, retry, and idempotency practice.
Route::post('/async-job-plan', AsyncJobPlanController::class)->name('async-job-plan.store');

// Return an event/listener plan for decoupled side-effect practice.
Route::post('/event-listener-plan', EventListenerPlanController::class)->name('event-listener-plan.store');

// Return a service-container binding plan for dependency injection practice.
Route::post('/container-binding-plan', ContainerBindingPlanController::class)->name('container-binding-plan.store');

// Return an authorization policy plan for access-control practice.
Route::post('/authorization-policy-plan', AuthorizationPolicyPlanController::class)->name('authorization-policy-plan.store');

// Return a cache strategy plan for performance practice.
Route::post('/cache-strategy-plan', CacheStrategyPlanController::class)->name('cache-strategy-plan.store');

// Return a file storage plan for upload and media practice.
Route::post('/file-storage-plan', FileStoragePlanController::class)->name('file-storage-plan.store');

// Return a rate-limit plan for API throttling practice.
Route::post('/rate-limit-plan', RateLimitPlanController::class)->name('rate-limit-plan.store');

// Summarize progress checklist items submitted by a learner.
Route::post('/progress-checklist', [PracticeProgressChecklistController::class, 'store'])->name('progress-checklist.store');

// Return today's practice session plan.
Route::get('/session-plan', PracticeSessionPlanController::class)->name('session-plan');

// Return runtime smoke-check status for the Docker/Laravel environment.
Route::get('/runtime-smoke-check', RuntimeSmokeCheckController::class)->name('runtime-smoke-check');

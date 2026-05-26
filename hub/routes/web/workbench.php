<?php

declare(strict_types=1);

use App\Http\Controllers\Practice\Workbench\AsyncJobPlanController;
use App\Http\Controllers\Practice\Workbench\AuthorizationPolicyPlanController;
use App\Http\Controllers\Practice\Workbench\CacheStrategyPlanController;
use App\Http\Controllers\Practice\Workbench\CollectionFilterPreviewController;
use App\Http\Controllers\Practice\Workbench\ContainerBindingPlanController;
use App\Http\Controllers\Practice\Workbench\EventListenerPlanController;
use App\Http\Controllers\Practice\Workbench\FileStoragePlanController;
use App\Http\Controllers\Practice\Workbench\HttpRequestFlowController;
use App\Http\Controllers\Practice\Workbench\NameNormalizerController;
use App\Http\Controllers\Practice\Workbench\PracticeTopicIntakeController;
use App\Http\Controllers\Practice\Workbench\QualityGateController;
use App\Http\Controllers\Practice\Workbench\RateLimitPlanController;
use App\Http\Controllers\Practice\Workbench\SecurityEscapePreviewController;
use Illuminate\Support\Facades\Route;

// Runnable workbench pages backed by native Laravel code.
// Open the name-normalizer practice workbench.
Route::get('/workbench/name-normalizer', [NameNormalizerController::class, 'index'])->name('practice.workbench.name-normalizer');

// Open the API validation topic-intake workbench.
Route::get('/workbench/topic-intake', PracticeTopicIntakeController::class)->name('practice.workbench.topic-intake');

// Open the Laravel HTTP request-flow workbench.
Route::get('/workbench/http-request-flow', HttpRequestFlowController::class)->name('practice.workbench.http-request-flow');

// Open the testing and quality-gate workbench.
Route::get('/workbench/quality-gate', QualityGateController::class)->name('practice.workbench.quality-gate');

// Open the Blade escaping and XSS safety workbench.
Route::get('/workbench/security-escape-preview', SecurityEscapePreviewController::class)->name('practice.workbench.security-escape-preview');

// Open the collection filtering and pagination workbench.
Route::get('/workbench/collection-filter-preview', CollectionFilterPreviewController::class)->name('practice.workbench.collection-filter-preview');

// Open the async job planning workbench.
Route::get('/workbench/async-job-plan', AsyncJobPlanController::class)->name('practice.workbench.async-job-plan');

// Open the event/listener planning workbench.
Route::get('/workbench/event-listener-plan', EventListenerPlanController::class)->name('practice.workbench.event-listener-plan');

// Open the service-container binding workbench.
Route::get('/workbench/container-binding-plan', ContainerBindingPlanController::class)->name('practice.workbench.container-binding-plan');

// Open the authorization policy planning workbench.
Route::get('/workbench/authorization-policy-plan', AuthorizationPolicyPlanController::class)->name('practice.workbench.authorization-policy-plan');

// Open the cache strategy planning workbench.
Route::get('/workbench/cache-strategy-plan', CacheStrategyPlanController::class)->name('practice.workbench.cache-strategy-plan');

// Open the file storage planning workbench.
Route::get('/workbench/file-storage-plan', FileStoragePlanController::class)->name('practice.workbench.file-storage-plan');

// Open the API rate-limit planning workbench.
Route::get('/workbench/rate-limit-plan', RateLimitPlanController::class)->name('practice.workbench.rate-limit-plan');

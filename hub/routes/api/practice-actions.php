<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AiAgentMemoryPlanController;
use App\Http\Controllers\Api\AiCloudInterviewRubricController;
use App\Http\Controllers\Api\AiHallucinationGuardPlanController;
use App\Http\Controllers\Api\AsyncJobPlanController;
use App\Http\Controllers\Api\AuthorizationPolicyPlanController;
use App\Http\Controllers\Api\CacheStrategyPlanController;
use App\Http\Controllers\Api\CollectionFilterPreviewController;
use App\Http\Controllers\Api\ContainerBindingPlanController;
use App\Http\Controllers\Api\CsrfProtectionPlanController;
use App\Http\Controllers\Api\DatabaseLockingPlanController;
use App\Http\Controllers\Api\DependencyInjectionRefactorController;
use App\Http\Controllers\Api\EventListenerPlanController;
use App\Http\Controllers\Api\FileStoragePlanController;
use App\Http\Controllers\Api\GraphqlRestDecisionController;
use App\Http\Controllers\Api\GraphTraversalPlanController;
use App\Http\Controllers\Api\HttpRequestFlowTraceController;
use App\Http\Controllers\Api\IdorAccessReviewController;
use App\Http\Controllers\Api\JavascriptArrowThisLabController;
use App\Http\Controllers\Api\JavascriptHoistingLabController;
use App\Http\Controllers\Api\JwtRevocationPlanController;
use App\Http\Controllers\Api\JwtTokenStoragePlanController;
use App\Http\Controllers\Api\KubernetesAnalogyPlanController;
use App\Http\Controllers\Api\LayeredArchitectureDecisionController;
use App\Http\Controllers\Api\LlmDecisionLoopPlanController;
use App\Http\Controllers\Api\LoadBalancerPlanController;
use App\Http\Controllers\Api\LsmTreePlanController;
use App\Http\Controllers\Api\OauthFlowPlanController;
use App\Http\Controllers\Api\OopAbstractionDecisionController;
use App\Http\Controllers\Api\PracticeProgressChecklistController;
use App\Http\Controllers\Api\PracticeQualityGateController;
use App\Http\Controllers\Api\PracticeSessionPlanController;
use App\Http\Controllers\Api\PracticeTopicController;
use App\Http\Controllers\Api\RagStrategyPlanController;
use App\Http\Controllers\Api\RateLimitPlanController;
use App\Http\Controllers\Api\ReactRenderOptimizationPlanController;
use App\Http\Controllers\Api\RestfulApiNamingPlanController;
use App\Http\Controllers\Api\ReverseProxyFailurePlanController;
use App\Http\Controllers\Api\RuntimeSmokeCheckController;
use App\Http\Controllers\Api\SecurityEscapePreviewController;
use App\Http\Controllers\Api\SiemElkPlanController;
use App\Http\Controllers\Api\SqlInjectionDefensePlanController;
use App\Http\Controllers\Api\SystemDesignTradeoffPlanController;
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

// Return a SQL Injection defense plan for parameterized-query practice.
Route::post('/sql-injection-defense-plan', SqlInjectionDefensePlanController::class)->name('sql-injection-defense-plan.store');

// Return a CSRF protection plan for cookie-authenticated browser flows.
Route::post('/csrf-protection-plan', CsrfProtectionPlanController::class)->name('csrf-protection-plan.store');

// Return an IDOR access review for object-level authorization practice.
Route::post('/idor-access-review', IdorAccessReviewController::class)->name('idor-access-review.store');

// Return filtered and paginated records for list-page practice.
Route::post('/collection-filter-preview', CollectionFilterPreviewController::class)->name('collection-filter-preview.store');

// Return a database-locking plan for transaction-bound concurrency practice.
Route::post('/database-locking-plan', DatabaseLockingPlanController::class)->name('database-locking-plan.store');

// Return an async job plan for queue, retry, and idempotency practice.
Route::post('/async-job-plan', AsyncJobPlanController::class)->name('async-job-plan.store');

// Return an event/listener plan for decoupled side-effect practice.
Route::post('/event-listener-plan', EventListenerPlanController::class)->name('event-listener-plan.store');

// Return a service-container binding plan for dependency injection practice.
Route::post('/container-binding-plan', ContainerBindingPlanController::class)->name('container-binding-plan.store');

// Return a Dependency Injection refactor plan for replacing manual dependencies.
Route::post('/dependency-injection-refactor', DependencyInjectionRefactorController::class)->name('dependency-injection-refactor.store');

// Return a PHP OOP abstraction decision plan for abstract class versus interface practice.
Route::post('/oop-abstraction-decision', OopAbstractionDecisionController::class)->name('oop-abstraction-decision.store');

// Return a Clean Architecture layering decision plan for Laravel feature boundaries.
Route::post('/layered-architecture-decision', LayeredArchitectureDecisionController::class)->name('layered-architecture-decision.store');

// Return a System Design tradeoff plan for interview architecture choices.
Route::post('/system-design-tradeoff-plan', SystemDesignTradeoffPlanController::class)->name('system-design-tradeoff-plan.store');

// Return a load-balancer algorithm plan for system-design interview practice.
Route::post('/load-balancer-plan', LoadBalancerPlanController::class)->name('load-balancer-plan.store');

// Return a reverse-proxy failure-mode plan for edge availability practice.
Route::post('/reverse-proxy-failure-plan', ReverseProxyFailurePlanController::class)->name('reverse-proxy-failure-plan.store');

// Return a SIEM and ELK implementation plan for security log analysis practice.
Route::post('/siem-elk-plan', SiemElkPlanController::class)->name('siem-elk-plan.store');

// Return a Kubernetes ship-analogy plan for DevOps beginner practice.
Route::post('/kubernetes-analogy-plan', KubernetesAnalogyPlanController::class)->name('kubernetes-analogy-plan.store');

// Return an authorization policy plan for access-control practice.
Route::post('/authorization-policy-plan', AuthorizationPolicyPlanController::class)->name('authorization-policy-plan.store');

// Return a cache strategy plan for performance practice.
Route::post('/cache-strategy-plan', CacheStrategyPlanController::class)->name('cache-strategy-plan.store');

// Return an LSM Tree plan for NoSQL storage-engine performance practice.
Route::post('/lsm-tree-plan', LsmTreePlanController::class)->name('lsm-tree-plan.store');

// Return a file storage plan for upload and media practice.
Route::post('/file-storage-plan', FileStoragePlanController::class)->name('file-storage-plan.store');

// Return a rate-limit plan for API throttling practice.
Route::post('/rate-limit-plan', RateLimitPlanController::class)->name('rate-limit-plan.store');

// Return a JWT token-storage plan for browser and API auth practice.
Route::post('/jwt-token-storage-plan', JwtTokenStoragePlanController::class)->name('jwt-token-storage-plan.store');

// Return a JWT revocation plan for API and database auth practice.
Route::post('/jwt-revocation-plan', JwtRevocationPlanController::class)->name('jwt-revocation-plan.store');

// Return an OAuth flow plan for replacing Implicit Flow with Authorization Code and PKCE.
Route::post('/oauth-flow-plan', OauthFlowPlanController::class)->name('oauth-flow-plan.store');

// Return a REST versus GraphQL API contract decision plan.
Route::post('/graphql-rest-decision', GraphqlRestDecisionController::class)->name('graphql-rest-decision.store');

// Return a RESTful endpoint naming plan for Laravel API contracts.
Route::post('/restful-api-naming-plan', RestfulApiNamingPlanController::class)->name('restful-api-naming-plan.store');

// Return a BFS versus DFS traversal decision plan for API and database practice.
Route::post('/graph-traversal-plan', GraphTraversalPlanController::class)->name('graph-traversal-plan.store');

// Return a React re-render optimization plan for memo, useMemo, and useCallback practice.
Route::post('/react-render-optimization-plan', ReactRenderOptimizationPlanController::class)->name('react-render-optimization-plan.store');

// Return a JavaScript hoisting analysis for frontend interview practice.
Route::post('/javascript-hoisting-lab', JavascriptHoistingLabController::class)->name('javascript-hoisting-lab.store');

// Return a JavaScript arrow-function this analysis for frontend interview practice.
Route::post('/javascript-arrow-this-lab', JavascriptArrowThisLabController::class)->name('javascript-arrow-this-lab.store');

// Return an AI hallucination guard plan for evidence-first code review practice.
Route::post('/ai-hallucination-guard-plan', AiHallucinationGuardPlanController::class)->name('ai-hallucination-guard-plan.store');

// Return an AI Cloud interview rubric for evaluating practical AI usage.
Route::post('/ai-cloud-interview-rubric', AiCloudInterviewRubricController::class)->name('ai-cloud-interview-rubric.store');

// Return an AI agent memory plan for developer-agent memory governance practice.
Route::post('/ai-agent-memory-plan', AiAgentMemoryPlanController::class)->name('ai-agent-memory-plan.store');

// Return a RAG strategy plan for classic, graph, and agentic retrieval patterns.
Route::post('/rag-strategy-plan', RagStrategyPlanController::class)->name('rag-strategy-plan.store');

// Return an LLM decision-loop plan for AI foundations practice.
Route::post('/llm-decision-loop-plan', LlmDecisionLoopPlanController::class)->name('llm-decision-loop-plan.store');

// Summarize progress checklist items submitted by a learner.
Route::post('/progress-checklist', [PracticeProgressChecklistController::class, 'store'])->name('progress-checklist.store');

// Return today's practice session plan.
Route::get('/session-plan', PracticeSessionPlanController::class)->name('session-plan');

// Return runtime smoke-check status for the Docker/Laravel environment.
Route::get('/runtime-smoke-check', RuntimeSmokeCheckController::class)->name('runtime-smoke-check');

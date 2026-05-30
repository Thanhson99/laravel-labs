<?php

declare(strict_types=1);

use App\Http\Controllers\Practice\Workbench\AiAgentMemoryPlanController;
use App\Http\Controllers\Practice\Workbench\AiCloudInterviewRubricController;
use App\Http\Controllers\Practice\Workbench\AiHallucinationGuardPlanController;
use App\Http\Controllers\Practice\Workbench\AsyncJobPlanController;
use App\Http\Controllers\Practice\Workbench\AuthorizationPolicyPlanController;
use App\Http\Controllers\Practice\Workbench\CacheStrategyPlanController;
use App\Http\Controllers\Practice\Workbench\CollectionFilterPreviewController;
use App\Http\Controllers\Practice\Workbench\ContainerBindingPlanController;
use App\Http\Controllers\Practice\Workbench\CsrfProtectionPlanController;
use App\Http\Controllers\Practice\Workbench\DatabaseLockingPlanController;
use App\Http\Controllers\Practice\Workbench\DependencyInjectionRefactorController;
use App\Http\Controllers\Practice\Workbench\EventListenerPlanController;
use App\Http\Controllers\Practice\Workbench\FileStoragePlanController;
use App\Http\Controllers\Practice\Workbench\GraphqlRestDecisionController;
use App\Http\Controllers\Practice\Workbench\GraphTraversalPlanController;
use App\Http\Controllers\Practice\Workbench\HttpRequestFlowController;
use App\Http\Controllers\Practice\Workbench\IdorAccessReviewController;
use App\Http\Controllers\Practice\Workbench\JavascriptArrowThisLabController;
use App\Http\Controllers\Practice\Workbench\JavascriptHoistingLabController;
use App\Http\Controllers\Practice\Workbench\JwtRevocationPlanController;
use App\Http\Controllers\Practice\Workbench\JwtTokenStoragePlanController;
use App\Http\Controllers\Practice\Workbench\KubernetesAnalogyPlanController;
use App\Http\Controllers\Practice\Workbench\LayeredArchitectureDecisionController;
use App\Http\Controllers\Practice\Workbench\LlmDecisionLoopPlanController;
use App\Http\Controllers\Practice\Workbench\LoadBalancerPlanController;
use App\Http\Controllers\Practice\Workbench\LsmTreePlanController;
use App\Http\Controllers\Practice\Workbench\NameNormalizerController;
use App\Http\Controllers\Practice\Workbench\OauthFlowPlanController;
use App\Http\Controllers\Practice\Workbench\OopAbstractionDecisionController;
use App\Http\Controllers\Practice\Workbench\PracticeTopicIntakeController;
use App\Http\Controllers\Practice\Workbench\QualityGateController;
use App\Http\Controllers\Practice\Workbench\RagStrategyPlanController;
use App\Http\Controllers\Practice\Workbench\RateLimitPlanController;
use App\Http\Controllers\Practice\Workbench\ReactRenderOptimizationPlanController;
use App\Http\Controllers\Practice\Workbench\RestfulApiNamingPlanController;
use App\Http\Controllers\Practice\Workbench\ReverseProxyFailurePlanController;
use App\Http\Controllers\Practice\Workbench\SecurityEscapePreviewController;
use App\Http\Controllers\Practice\Workbench\SiemElkPlanController;
use App\Http\Controllers\Practice\Workbench\SqlInjectionDefensePlanController;
use App\Http\Controllers\Practice\Workbench\SystemDesignTradeoffPlanController;
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

// Open the SQL Injection defense planning workbench.
Route::get('/workbench/sql-injection-defense-plan', SqlInjectionDefensePlanController::class)->name('practice.workbench.sql-injection-defense-plan');

// Open the CSRF protection planning workbench.
Route::get('/workbench/csrf-protection-plan', CsrfProtectionPlanController::class)->name('practice.workbench.csrf-protection-plan');

// Open the IDOR object-level authorization workbench.
Route::get('/workbench/idor-access-review', IdorAccessReviewController::class)->name('practice.workbench.idor-access-review');

// Open the collection filtering and pagination workbench.
Route::get('/workbench/collection-filter-preview', CollectionFilterPreviewController::class)->name('practice.workbench.collection-filter-preview');

// Open the database-locking planning workbench.
Route::get('/workbench/database-locking-plan', DatabaseLockingPlanController::class)->name('practice.workbench.database-locking-plan');

// Open the async job planning workbench.
Route::get('/workbench/async-job-plan', AsyncJobPlanController::class)->name('practice.workbench.async-job-plan');

// Open the event/listener planning workbench.
Route::get('/workbench/event-listener-plan', EventListenerPlanController::class)->name('practice.workbench.event-listener-plan');

// Open the service-container binding workbench.
Route::get('/workbench/container-binding-plan', ContainerBindingPlanController::class)->name('practice.workbench.container-binding-plan');

// Open the Dependency Injection refactor workbench.
Route::get('/workbench/dependency-injection-refactor', DependencyInjectionRefactorController::class)->name('practice.workbench.dependency-injection-refactor');

// Open the PHP OOP abstraction decision workbench.
Route::get('/workbench/oop-abstraction-decision', OopAbstractionDecisionController::class)->name('practice.workbench.oop-abstraction-decision');

// Open the Clean Architecture layering decision workbench.
Route::get('/workbench/layered-architecture-decision', LayeredArchitectureDecisionController::class)->name('practice.workbench.layered-architecture-decision');

// Open the System Design tradeoff planning workbench.
Route::get('/workbench/system-design-tradeoff-plan', SystemDesignTradeoffPlanController::class)->name('practice.workbench.system-design-tradeoff-plan');

// Open the load-balancer algorithm planning workbench.
Route::get('/workbench/load-balancer-plan', LoadBalancerPlanController::class)->name('practice.workbench.load-balancer-plan');

// Open the reverse-proxy failure-mode planning workbench.
Route::get('/workbench/reverse-proxy-failure-plan', ReverseProxyFailurePlanController::class)->name('practice.workbench.reverse-proxy-failure-plan');

// Open the SIEM and ELK planning workbench.
Route::get('/workbench/siem-elk-plan', SiemElkPlanController::class)->name('practice.workbench.siem-elk-plan');

// Open the Kubernetes analogy planning workbench.
Route::get('/workbench/kubernetes-analogy-plan', KubernetesAnalogyPlanController::class)->name('practice.workbench.kubernetes-analogy-plan');

// Open the authorization policy planning workbench.
Route::get('/workbench/authorization-policy-plan', AuthorizationPolicyPlanController::class)->name('practice.workbench.authorization-policy-plan');

// Open the cache strategy planning workbench.
Route::get('/workbench/cache-strategy-plan', CacheStrategyPlanController::class)->name('practice.workbench.cache-strategy-plan');

// Open the LSM Tree storage-engine planning workbench.
Route::get('/workbench/lsm-tree-plan', LsmTreePlanController::class)->name('practice.workbench.lsm-tree-plan');

// Open the file storage planning workbench.
Route::get('/workbench/file-storage-plan', FileStoragePlanController::class)->name('practice.workbench.file-storage-plan');

// Open the API rate-limit planning workbench.
Route::get('/workbench/rate-limit-plan', RateLimitPlanController::class)->name('practice.workbench.rate-limit-plan');

// Open the JWT token-storage planning workbench.
Route::get('/workbench/jwt-token-storage-plan', JwtTokenStoragePlanController::class)->name('practice.workbench.jwt-token-storage-plan');

// Open the JWT revocation planning workbench.
Route::get('/workbench/jwt-revocation-plan', JwtRevocationPlanController::class)->name('practice.workbench.jwt-revocation-plan');

// Open the OAuth flow planning workbench.
Route::get('/workbench/oauth-flow-plan', OauthFlowPlanController::class)->name('practice.workbench.oauth-flow-plan');

// Open the REST versus GraphQL decision workbench.
Route::get('/workbench/graphql-rest-decision', GraphqlRestDecisionController::class)->name('practice.workbench.graphql-rest-decision');

// Open the RESTful API naming workbench.
Route::get('/workbench/restful-api-naming-plan', RestfulApiNamingPlanController::class)->name('practice.workbench.restful-api-naming-plan');

// Open the BFS versus DFS graph traversal planning workbench.
Route::get('/workbench/graph-traversal-plan', GraphTraversalPlanController::class)->name('practice.workbench.graph-traversal-plan');

// Open the React render optimization workbench.
Route::get('/workbench/react-render-optimization-plan', ReactRenderOptimizationPlanController::class)->name('practice.workbench.react-render-optimization-plan');

// Open the JavaScript hoisting interview lab.
Route::get('/workbench/javascript-hoisting-lab', JavascriptHoistingLabController::class)->name('practice.workbench.javascript-hoisting-lab');

// Open the JavaScript arrow-function this interview lab.
Route::get('/workbench/javascript-arrow-this-lab', JavascriptArrowThisLabController::class)->name('practice.workbench.javascript-arrow-this-lab');

// Open the AI hallucination guard planning workbench.
Route::get('/workbench/ai-hallucination-guard-plan', AiHallucinationGuardPlanController::class)->name('practice.workbench.ai-hallucination-guard-plan');

// Open the AI Cloud interview rubric workbench.
Route::get('/workbench/ai-cloud-interview-rubric', AiCloudInterviewRubricController::class)->name('practice.workbench.ai-cloud-interview-rubric');

// Open the AI agent memory planning workbench.
Route::get('/workbench/ai-agent-memory-plan', AiAgentMemoryPlanController::class)->name('practice.workbench.ai-agent-memory-plan');

// Open the RAG strategy planning workbench.
Route::get('/workbench/rag-strategy-plan', RagStrategyPlanController::class)->name('practice.workbench.rag-strategy-plan');

// Open the LLM decision-loop planning workbench.
Route::get('/workbench/llm-decision-loop-plan', LlmDecisionLoopPlanController::class)->name('practice.workbench.llm-decision-loop-plan');

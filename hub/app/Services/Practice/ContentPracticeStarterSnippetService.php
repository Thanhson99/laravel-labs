<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentPracticeStarterSnippetService
{
    /**
     * Return readable starter code snippets for the selected source record.
     *
     * @param  array<string, mixed>  $item
     * @return array<int, array{label: string, file: string, code: string}>
     */
    public function snippetsFor(array $item): array
    {
        if (($item['technology'] ?? null) === 'llm-foundations' && AiAgentMemoryTopicService::matchesRecord($item)) {
            return $this->aiAgentMemorySnippets();
        }

        if (($item['technology'] ?? null) === 'llm-foundations' && AiTypeComparisonTopicService::matchesRecord($item)) {
            return $this->aiTypeComparisonSnippets();
        }

        if (($item['technology'] ?? null) === 'database-eloquent' && CoveringIndexTopicService::matchesRecord($item)) {
            return $this->coveringIndexSnippets();
        }

        if (($item['technology'] ?? null) === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord($item)) {
            return $this->databaseLockingSnippets();
        }

        return match ($item['technology']) {
            'restful-api-naming' => $this->restfulApiNamingSnippets(),
            'api-validation' => $this->apiValidationSnippets(),
            'auth-security' => $this->authSecuritySnippets(),
            'database-eloquent' => $this->databaseSnippets(),
            'files-media' => $this->fileStorageSnippets(),
            'async-workflow' => $this->asyncWorkflowSnippets(),
            'performance-cache' => $this->performanceCacheSnippets(),
            'container-architecture' => $this->containerArchitectureSnippets(),
            'realtime-events' => $this->realtimeEventSnippets(),
            'system-design-tradeoffs',
            'reverse-proxy-edge',
            'siem-elk-observability',
            'load-balancing',
            'kubernetes-orchestration',
            'jwt-token-storage',
            'jwt-revocation',
            'oauth-flow',
            'lsm-tree-storage',
            'llm-foundations',
            'rag-systems',
            'graphql-api',
            'graph-traversal',
            'react-render-performance',
            'sql-injection-defense',
            'csrf-protection',
            'idor-access-control',
            'security-misconfiguration',
            'ai-cloud-interview' => $this->workbenchPlannerSnippets($item['technology']),
            'javascript-closures' => $this->javascriptClosureSnippets($item),
            'xss-defense' => $this->xssDefenseSnippets(),
            'blade-ui' => $this->bladeUiSnippets(),
            'php' => $this->phpSnippets($item),
            default => $this->webSnippets(),
        };
    }

    /**
     * Return starter code for RESTful API endpoint naming practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function restfulApiNamingSnippets(): array
    {
        return [
            [
                'label' => 'API route',
                'file' => 'routes/api/practice-actions.php',
                'code' => <<<'PHP'
// Return a RESTful endpoint naming plan for Laravel API contracts.
Route::post('/restful-api-naming-plan', RestfulApiNamingPlanController::class)
    ->name('restful-api-naming-plan.store');
PHP,
            ],
            [
                'label' => 'Form request',
                'file' => 'app/Http/Requests/Api/PlanRestfulApiNamingRequest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanRestfulApiNamingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource_name' => ['required', 'string', 'min:2', 'max:60'],
            'current_endpoint' => ['nullable', 'string', 'max:120'],
            'operation_type' => ['required', 'string', Rule::in(['read', 'create', 'update', 'delete', 'action'])],
            'needs_filtering' => ['required', 'boolean'],
            'needs_business_action' => ['required', 'boolean'],
            'version' => ['required', 'string', Rule::in(['v1', 'v2'])],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Service',
                'file' => 'app/Services/Practice/RestfulApiNamingPlanService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class RestfulApiNamingPlanService
{
    /**
     * @param  array{resource_name: string, current_endpoint?: string|null, operation_type: string, needs_filtering: bool, needs_business_action: bool, version: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        return [
            'base_path' => "/api/{$input['version']}/orders",
            'quality_review' => [
                'current_endpoint' => $input['current_endpoint'] ?? null,
                'score' => 85,
                'smells' => [],
            ],
            'contract_artifacts' => [
                'openapi_path' => "/api/{$input['version']}/orders",
                'openapi_yaml' => "paths:\n  /api/{$input['version']}/orders:\n    get:\n      operationId: getOrdersIndex",
            ],
            'implementation_blueprint' => [
                'controller' => 'OrderController',
                'form_requests' => ['IndexOrderRequest'],
                'policy_abilities' => ['index', 'show'],
            ],
            'migration_plan' => [
                'requires_deprecation' => true,
                'legacy_endpoint' => $input['current_endpoint'] ?? null,
                'target_endpoint' => "/api/{$input['version']}/orders",
            ],
            'response_contract' => [
                'success_responses' => [
                    ['status' => 200, 'envelope' => ['data' => ['array<orders>']]],
                ],
                'error_responses' => [
                    ['status' => 422, 'shape' => ['message' => 'The given data was invalid.']],
                ],
            ],
            'operational_readiness' => [
                'logs' => ['request_id', 'route_name', 'actor_id'],
                'metrics' => ['api_requests_total{route_name,status}'],
                'acceptance_checks' => ['Route names are present in route:list.'],
            ],
            'naming_rubric' => [
                'total_score' => 85,
                'grade' => 'review',
                'criteria' => ['Resource noun path', 'HTTP method semantics'],
            ],
            'client_examples' => [
                [
                    'route_name' => 'orders.index',
                    'curl' => "curl -X GET 'https://api.example.test/api/v1/orders' -H 'Accept: application/json'",
                    'fetch' => "await fetch('https://api.example.test/api/v1/orders');",
                ],
            ],
            'rules' => [
                'Use plural resource nouns.',
                'Let HTTP verbs carry CRUD intent.',
                'Keep filters in query parameters.',
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/RestfulApiNamingPlanWorkbenchTest.php',
                'code' => <<<'PHP'
public function test_restful_api_naming_api_returns_a_plan(): void
{
    $response = $this->postJson('/api/practice/restful-api-naming-plan', [
        'resource_name' => 'Order',
        'current_endpoint' => '/api/v1/getOrders',
        'operation_type' => 'read',
        'needs_filtering' => true,
        'needs_business_action' => true,
        'version' => 'v1',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.resource.name', 'orders')
        ->assertJsonPath('data.recommendation.base_path', '/api/v1/orders')
        ->assertJsonPath('data.quality_review.current_endpoint', '/api/v1/getOrders')
        ->assertJsonPath('data.contract_artifacts.openapi_path', '/api/v1/orders')
        ->assertJsonPath('data.implementation_blueprint.controller', 'OrderController')
        ->assertJsonPath('data.migration_plan.target_endpoint', '/api/v1/orders')
        ->assertJsonPath('data.response_contract.error_responses.0.status', 422)
        ->assertJsonPath('data.operational_readiness.metrics.0', 'api_requests_total{route_name,status}')
        ->assertJsonPath('data.naming_rubric.grade', 'review')
        ->assertJsonPath('data.client_examples.0.route_name', 'orders.index');
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for an API validation slice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function apiValidationSnippets(): array
    {
        return [
            [
                'label' => 'API route',
                'file' => 'routes/api/practice-actions.php',
                'code' => <<<'PHP'
// Store a content-backed drill through a validated API slice.
Route::post('/content-backed-drill', ContentBackedApiDrillController::class)
    ->name('content-backed-drill.store');
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/ContentBackedApiDrillTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentBackedApiDrillTest extends TestCase
{
    public function test_endpoint_accepts_valid_payload(): void
    {
        $response = $this->postJson('/api/practice/content-backed-drill', [
            'title' => 'Validated practice payload',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Validated practice payload');
    }

    public function test_endpoint_rejects_invalid_payload(): void
    {
        $response = $this->postJson('/api/practice/content-backed-drill', [
            'title' => 'x',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }
}
PHP,
            ],
            [
                'label' => 'Form request',
                'file' => 'app/Http/Requests/Api/StoreContentBackedDrillRequest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class StoreContentBackedDrillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'API controller',
                'file' => 'app/Http/Controllers/Api/ContentBackedApiDrillController.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreContentBackedDrillRequest;
use App\Services\Practice\ContentBackedApiDrillService;
use Illuminate\Http\JsonResponse;

final class ContentBackedApiDrillController extends Controller
{
    public function __invoke(StoreContentBackedDrillRequest $request, ContentBackedApiDrillService $service): JsonResponse
    {
        return response()->json([
            'data' => $service->store($request->validated()),
        ], 201);
    }
}
PHP,
            ],
            [
                'label' => 'Service',
                'file' => 'app/Services/Practice/ContentBackedApiDrillService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentBackedApiDrillService
{
    /**
     * @param  array{title: string}  $payload
     * @return array{title: string, status: string}
     */
    public function store(array $payload): array
    {
        return [
            'title' => trim($payload['title']),
            'status' => 'validated',
        ];
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for a pure PHP practice slice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function phpSnippets(array $item = []): array
    {
        if (PhpRuntimeMemoryTopicService::matchesRecord($item)) {
            return $this->phpRuntimeMemorySnippets();
        }

        return [
            [
                'label' => 'Pure PHP service',
                'file' => 'app/Practice/Php/ContentBackedNormalizer.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Practice\Php;

final class ContentBackedNormalizer
{
    public function normalize(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
PHP,
            ],
            [
                'label' => 'Unit test',
                'file' => 'tests/Unit/Practice/ContentBackedNormalizerTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Practice\Php\ContentBackedNormalizer;
use PHPUnit\Framework\TestCase;

final class ContentBackedNormalizerTest extends TestCase
{
    public function test_it_normalizes_spacing(): void
    {
        $normalizer = new ContentBackedNormalizer();

        $this->assertSame('Laravel practice', $normalizer->normalize(' Laravel   practice '));
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for a PHP runtime-memory stack versus heap practice slice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function phpRuntimeMemorySnippets(): array
    {
        return [
            [
                'label' => 'Runtime memory note',
                'file' => 'app/Practice/Php/RuntimeMemoryNote.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Practice\Php;

final class RuntimeMemoryNote
{
    /**
     * @return array{stack_frame: array<string>, heap_backed: array<string>, cleanup: array<string>, risks: array<string>}
     */
    public function explain(array $payload): array
    {
        $localCount = count($payload);
        $heapPressure = array_values($payload);

        return [
            'stack_frame' => [
                'The active call frame owns $payload, $localCount, and $heapPressure references while explain() is running.',
                'When explain() returns, the frame unwinds and local variable names disappear.',
            ],
            'heap_backed' => [
                'The array values and any nested objects or strings are heap-backed data.',
                'Heap-backed data stays alive while another reference can still reach it.',
            ],
            'cleanup' => [
                'Scope exit releases local references.',
                'unset($heapPressure) would remove that local reference earlier.',
                'PHP reference counting and garbage collection decide when unreachable data is reclaimed.',
            ],
            'risks' => [
                'Deep recursion can exhaust call frames.',
                'Large arrays, retained references, circular references, or long-running workers can create memory pressure.',
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Runtime memory unit test',
                'file' => 'tests/Unit/Practice/RuntimeMemoryNoteTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Practice\Php\RuntimeMemoryNote;
use PHPUnit\Framework\TestCase;

final class RuntimeMemoryNoteTest extends TestCase
{
    public function test_it_separates_call_frame_heap_cleanup_and_risks(): void
    {
        $note = (new RuntimeMemoryNote)->explain(range(1, 1000));

        $this->assertStringContainsString('call frame', $note['stack_frame'][0]);
        $this->assertStringContainsString('heap-backed data', $note['heap_backed'][0]);
        $this->assertStringContainsString('reference counting', $note['cleanup'][2]);
        $this->assertStringContainsString('Long-running workers', ucfirst($note['risks'][1]));
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for authorization and security practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function authSecuritySnippets(): array
    {
        return [
            [
                'label' => 'Policy',
                'file' => 'app/Policies/PracticeTaskPolicy.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class PracticeTaskPolicy
{
    public function update(User $user, array $task): bool
    {
        return (int) $task['owner_id'] === (int) $user->id
            && ($task['status'] ?? 'draft') !== 'locked';
    }
}
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/PracticeTaskAuthorizationTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeTaskAuthorizationTest extends TestCase
{
    public function test_non_owner_cannot_update_practice_task(): void
    {
        $response = $this->patchJson('/api/practice/tasks/1', [
            'title' => 'Updated title',
        ]);

        $response->assertForbidden();
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for migration, model, and query practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function databaseSnippets(): array
    {
        return [
            [
                'label' => 'Migration',
                'file' => 'database/migrations/xxxx_xx_xx_create_practice_tasks_table.php',
                'code' => <<<'PHP'
Schema::create('practice_tasks', function (Blueprint $table): void {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title', 160);
    $table->string('status', 40)->default('draft');
    $table->timestamps();

    $table->index(['user_id', 'status']);
});
PHP,
            ],
            [
                'label' => 'Eloquent model',
                'file' => 'app/Models/PracticeTask.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PracticeTask extends Model
{
    protected $fillable = ['user_id', 'title', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for PostgreSQL covering-index verification practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function coveringIndexSnippets(): array
    {
        return [
            [
                'label' => 'Covering index migration',
                'file' => 'database/migrations/xxxx_xx_xx_add_orders_covering_index.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
CREATE INDEX idx_orders_user_created_covering
    ON orders (user_id, created_at DESC)
    INCLUDE (status, total_amount)
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_orders_user_created_covering');
    }
};
PHP,
            ],
            [
                'label' => 'Query-plan review service',
                'file' => 'app/Services/Practice/CoveringIndexReviewService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class CoveringIndexReviewService
{
    /**
     * @return array{required_plan_signals: array<int, string>, risk_checks: array<int, string>, interview_answer: string}
     */
    public function review(): array
    {
        return [
            'required_plan_signals' => [
                'EXPLAIN (ANALYZE, BUFFERS) shows Index Only Scan.',
                'Heap Fetches is small or zero for the hot query.',
                'Selected columns are only key columns or included columns.',
                'Buffers show fewer heap reads than the previous plan.',
            ],
            'risk_checks' => [
                'Confirm autovacuum keeps the visibility map healthy.',
                'Avoid wide or high-churn included columns.',
                'Measure index size, write overhead, and rollback path before release.',
                'Recheck the plan after realistic inserts, updates, deletes, and VACUUM.',
            ],
            'interview_answer' => 'A covering index helps when the query can be answered from the index. In PostgreSQL, Index Only Scan is only truly useful when visibility-map state lets the engine avoid heap fetches.',
        ];
    }

}
PHP,
            ],
            [
                'label' => 'Focused unit test',
                'file' => 'tests/Unit/Practice/CoveringIndexReviewServiceTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\CoveringIndexReviewService;
use PHPUnit\Framework\TestCase;

final class CoveringIndexReviewServiceTest extends TestCase
{
    public function test_it_names_plan_signals_and_bloat_risks(): void
    {
        $review = (new CoveringIndexReviewService)->review();

        $this->assertContains('EXPLAIN (ANALYZE, BUFFERS) shows Index Only Scan.', $review['required_plan_signals']);
        $this->assertContains('Heap Fetches is small or zero for the hot query.', $review['required_plan_signals']);
        $this->assertContains('Confirm autovacuum keeps the visibility map healthy.', $review['risk_checks']);
        $this->assertContains('Avoid wide or high-churn included columns.', $review['risk_checks']);
        $this->assertStringContainsString('avoid heap fetches', $review['interview_answer']);
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for database locking and transaction-bound concurrency practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function databaseLockingSnippets(): array
    {
        return [
            [
                'label' => 'Inventory lock service',
                'file' => 'app/Services/Practice/InventoryReservationService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

final class InventoryReservationService
{
    /**
     * Reserve one product unit inside a row-level database lock.
     *
     * @return array{product_id: int, remaining_stock: int}
     */
    public function reserve(int $productId): array
    {
        return DB::transaction(function () use ($productId): array {
            $product = Product::query()
                ->whereKey($productId)
                ->lockForUpdate()
                ->firstOrFail();

            $remainingStock = $product->stock - 1;

            if ($product->stock < 1) {
                throw new InsufficientStock($product->id);
            }

            $product->forceFill([
                'stock' => $remainingStock,
            ])->save();

            Order::query()->create([
                'product_id' => $product->id,
                'status' => 'reserved',
            ]);

            return [
                'product_id' => $product->id,
                'remaining_stock' => $remainingStock,
            ];
        });
    }
}

final class InsufficientStock extends \RuntimeException
{
    public function __construct(int $productId)
    {
        parent::__construct("Product {$productId} is out of stock.");
    }
}
PHP,
            ],
            [
                'label' => 'Concurrency test checklist',
                'file' => 'tests/Feature/InventoryReservationConcurrencyTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

use App\Services\Practice\InventoryReservationService;

it('protects stock updates with a transaction and row lock', function (): void {
    // Arrange one product with stock = 1.
    // Run two reservation attempts that target the same product.
    // Assert only one order is created and stock never becomes negative.
    // In a real integration test, run this against the same DB engine used in production.
});

it('keeps the locked section short', function (): void {
    // Assert external API calls, mail, file IO, or queue dispatch are not done while holding lockForUpdate().
});
PHP,
            ],
        ];
    }

    /**
     * Return starter code for file storage practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function fileStorageSnippets(): array
    {
        return [
            [
                'label' => 'Form request',
                'file' => 'app/Http/Requests/Api/StorePracticeUploadRequest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class StorePracticeUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'artifact' => ['required', 'file', 'mimes:pdf,png,jpg', 'max:5120'],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Storage service',
                'file' => 'app/Services/Practice/PracticeUploadService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Http\UploadedFile;

final class PracticeUploadService
{
    public function store(UploadedFile $file, int $userId): array
    {
        $path = $file->store("practice-artifacts/{$userId}", 'public');

        return [
            'disk' => 'public',
            'path' => $path,
        ];
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for queued jobs and event side effects.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function asyncWorkflowSnippets(): array
    {
        return [
            [
                'label' => 'Event',
                'file' => 'app/Events/PracticeTaskCompleted.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Events;

final readonly class PracticeTaskCompleted
{
    public function __construct(public int $taskId) {}
}
PHP,
            ],
            [
                'label' => 'Queued listener',
                'file' => 'app/Listeners/SendPracticeTaskSummary.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PracticeTaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendPracticeTaskSummary implements ShouldQueue
{
    public function handle(PracticeTaskCompleted $event): void
    {
        // Load by ID here, then send one side effect.
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for cache and performance practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function performanceCacheSnippets(): array
    {
        return [
            [
                'label' => 'Cache service',
                'file' => 'app/Services/Practice/PracticeDashboardSummaryService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Facades\Cache;

final class PracticeDashboardSummaryService
{
    public function summaryFor(int $userId): array
    {
        return Cache::remember("practice:dashboard:{$userId}", now()->addMinutes(15), function (): array {
            return [
                'open_tasks' => 0,
                'completed_tasks' => 0,
            ];
        });
    }
}
PHP,
            ],
            [
                'label' => 'Cache test',
                'file' => 'tests/Unit/Practice/PracticeDashboardSummaryServiceTest.php',
                'code' => <<<'PHP'
public function test_summary_is_cached_by_user_scope(): void
{
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(fn (string $key): bool => $key === 'practice:dashboard:42')
        ->andReturn(['open_tasks' => 1, 'completed_tasks' => 2]);
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for service-container practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function containerArchitectureSnippets(): array
    {
        return [
            [
                'label' => 'Contract',
                'file' => 'app/Contracts/PracticeTaskReporter.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Contracts;

interface PracticeTaskReporter
{
    public function reportFor(int $userId): array;
}
PHP,
            ],
            [
                'label' => 'Container binding',
                'file' => 'app/Providers/AppServiceProvider.php',
                'code' => <<<'PHP'
public function register(): void
{
    $this->app->bind(
        PracticeTaskReporter::class,
        DatabasePracticeTaskReporter::class,
    );
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for realtime event practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function realtimeEventSnippets(): array
    {
        return [
            [
                'label' => 'Broadcast event',
                'file' => 'app/Events/PracticeProgressUpdated.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

final readonly class PracticeProgressUpdated implements ShouldBroadcast
{
    public function __construct(public int $userId, public int $completed) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("practice.{$this->userId}");
    }
}
PHP,
            ],
            [
                'label' => 'Channel authorization',
                'file' => 'routes/channels.php',
                'code' => <<<'PHP'
Broadcast::channel('practice.{userId}', function (User $user, int $userId): bool {
    return (int) $user->id === $userId;
});
PHP,
            ],
        ];
    }

    /**
     * Return starter code for Blade UI practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function bladeUiSnippets(): array
    {
        return [
            [
                'label' => 'Controller data',
                'file' => 'app/Http/Controllers/Practice/PracticeCardController.php',
                'code' => <<<'PHP'
public function __invoke(PracticeCardService $cards): View
{
    return view('practice.card', [
        'card' => $cards->first(),
    ]);
}
PHP,
            ],
            [
                'label' => 'Blade view',
                'file' => 'resources/views/practice/card.blade.php',
                'code' => <<<'BLADE'
@extends('learning.layout', ['title' => $card['title']])

@section('content')
    <section class="hero">
        <span class="badge">{{ $card['status'] }}</span>
        <h1>{{ $card['title'] }}</h1>
        <p>{{ $card['summary'] }}</p>
    </section>
@endsection
BLADE,
            ],
        ];
    }

    /**
     * Return starter code for XSS-safe rendering practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function xssDefenseSnippets(): array
    {
        return [
            [
                'label' => 'Escape preview service',
                'file' => 'app/Services/Practice/SecurityEscapePreviewService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class SecurityEscapePreviewService
{
    public function preview(array $content): array
    {
        $body = trim($content['body']);
        $context = $content['rendering_context'] ?? 'html_text';
        $lowerBody = Str::lower($body);
        $riskFlags = collect([
            '<script' => 'script-tag',
            'onerror=' => 'inline-event-handler',
            'javascript:' => 'javascript-url',
            'innerhtml' => 'unsafe-dom-sink',
        ])
            ->filter(fn (string $flag, string $needle): bool => str_contains($lowerBody, $needle))
            ->values()
            ->all();

        return [
            'body' => $body,
            'rendering_context' => $context,
            'escaped' => ['body' => e($body)],
            'risk_flags' => $riskFlags,
            'severity_summary' => [
                'high' => count($riskFlags),
                'medium' => 0,
                'low' => 0,
            ],
            'release_decision' => [
                'status' => $riskFlags === [] ? 'ready' : 'block',
                'reason' => $riskFlags === []
                    ? 'No known executable browser payload pattern was detected.'
                    : 'Fix reported XSS risk flags before release.',
            ],
            'secure_code_snippets' => [
                [
                    'label' => 'Escaped Blade text',
                    'code' => '{{ $comment->body }}',
                    'note' => 'Render user text as text, not HTML.',
                ],
                [
                    'label' => 'Safe script data handoff',
                    'code' => '<script>window.viewModel = @json($viewModel);</script>',
                    'note' => 'Serialize server data instead of concatenating strings.',
                ],
            ],
            'context_rule' => match ($context) {
                'javascript_data' => 'Serialize data with @json or Js::from.',
                'url' => 'Allowlist URL schemes before rendering href or src.',
                'rich_text' => 'Sanitize rich text before raw HTML rendering.',
                default => 'Render user-provided text with escaped Blade output.',
            },
            'review_checklist' => [
                'Check escaped output or sanitizer boundary.',
                'Add payload tests for reported risk flags.',
                'Document CSP as defense-in-depth.',
            ],
            'remediation_steps' => [
                'Fix escaped output or sanitizer boundary first.',
                'Add payload tests before marking the issue resolved.',
                'Document CSP as defense-in-depth.',
            ],
            'test_plan' => [
                'Assert the detected risk flag.',
                'Assert escaped output renders the payload as text.',
                'Assert review checklist and variant prompts are returned.',
            ],
            'variant_review' => [
                'Check reflected XSS through request input and flash messages.',
                'Check stored XSS through database-backed profile, comment, or admin content.',
                'Check DOM XSS through innerHTML, document.write, or unsafe template strings.',
            ],
            'interview_answer_outline' => [
                'XSS means untrusted data reaches an executable browser context.',
                'Use context-aware escaping or sanitization at the rendering boundary.',
                'Prove the fix with reflected, stored, and DOM-style payload tests.',
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Blade output',
                'file' => 'resources/views/practice/workbench/security-escape-preview.blade.php',
                'code' => <<<'BLADE'
<p>{{ $preview['body'] }}</p>

{{-- Render sanitized or trusted HTML only after a clear review boundary. --}}
<pre><code>{{ $preview['escaped']['body'] }}</code></pre>
<p>{{ $preview['context_rule'] }}</p>
BLADE,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/SecurityEscapePreviewWorkbenchTest.php',
                'code' => <<<'PHP'
public function test_script_payload_is_escaped(): void
{
    $response = $this->postJson('/api/practice/security-escape-preview', [
        'title' => 'Payload preview',
        'body' => '<script>alert(1)</script>',
        'rendering_context' => 'html_text',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.risk_flags.0', 'script-tag')
        ->assertJsonPath('data.severity_summary.high', 1)
        ->assertJsonPath('data.release_decision.status', 'block')
        ->assertJsonPath('data.secure_code_snippets.0.label', 'Escaped Blade text')
        ->assertJsonPath('data.review_checklist.1', 'Add payload tests for reported risk flags.')
        ->assertJsonPath('data.remediation_steps.0', 'Fix escaped output or sanitizer boundary first.')
        ->assertJsonPath('data.test_plan.0', 'Assert the detected risk flag.')
        ->assertJsonPath('data.variant_review.0', 'Check reflected XSS through request input and flash messages.')
        ->assertJsonPath('data.escaped.body', '&lt;script&gt;alert(1)&lt;/script&gt;');
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for technology-specific planning workbenches.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function workbenchPlannerSnippets(string $technology): array
    {
        if ($technology === 'oauth-flow') {
            return $this->oauthFlowSnippets();
        }

        if ($technology === 'graph-traversal') {
            return $this->graphTraversalSnippets();
        }

        if ($technology === 'idor-access-control') {
            return $this->idorAccessControlSnippets();
        }

        if ($technology === 'rag-systems') {
            return $this->ragStrategySnippets();
        }

        $name = match ($technology) {
            'reverse-proxy-edge' => 'ReverseProxyFailurePlan',
            'siem-elk-observability' => 'SiemElkPlan',
            'load-balancing' => 'LoadBalancerPlan',
            'kubernetes-orchestration' => 'KubernetesAnalogyPlan',
            'jwt-token-storage' => 'JwtTokenStoragePlan',
            'jwt-revocation' => 'JwtRevocationPlan',
            'oauth-flow' => 'OauthFlowPlan',
            'system-design-tradeoffs' => 'SystemDesignTradeoffPlan',
            'graphql-api' => 'GraphqlRestDecision',
            'graph-traversal' => 'GraphTraversalPlan',
            'react-render-performance' => 'ReactRenderOptimizationPlan',
            'sql-injection-defense' => 'SqlInjectionDefensePlan',
            'csrf-protection' => 'CsrfProtectionPlan',
            'idor-access-control' => 'IdorAccessReview',
            'security-misconfiguration' => 'ConfigurationReadiness',
            'xss-defense' => 'XssDefensePreview',
            'lsm-tree-storage' => 'LsmTreePlan',
            'llm-foundations' => 'LlmDecisionLoopPlan',
            'rag-systems' => 'RagStrategyPlan',
            'ai-cloud-interview' => 'AiCloudInterviewRubric',
            default => 'TechnologyPlan',
        };

        return [
            [
                'label' => 'Planning service',
                'file' => "app/Services/Practice/{$name}Service.php",
                'code' => <<<PHP
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class {$name}Service
{
    public function plan(array \$input): array
    {
        return [
            'technology' => '{$technology}',
            'recommendation' => 'Choose the option that matches the actual constraints.',
            'verification' => ['Add one focused feature test and one failure-mode assertion.'],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => "tests/Feature/{$name}WorkbenchTest.php",
                'code' => <<<PHP
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class {$name}WorkbenchTest extends TestCase
{
    public function test_workbench_returns_a_plan(): void
    {
        \$response = \$this->postJson('/api/practice/example-plan', [
            'technology' => '{$technology}',
        ]);

        \$response
            ->assertOk()
            ->assertJsonPath('data.technology', '{$technology}');
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for RAG, Long Context, CAG, and hybrid chatbot strategy practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function ragStrategySnippets(): array
    {
        return [
            [
                'label' => 'Strategy service',
                'file' => 'app/Services/Practice/RagStrategyPlanService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class RagStrategyPlanService
{
    public function plan(array $input): array
    {
        $ragPattern = $this->ragPatternFor($input);
        $contextStrategy = $this->contextStrategyFor($input, $ragPattern);

        return [
            'summary' => [
                'recommendation' => $ragPattern,
                'reason' => 'Pick the smallest retrieval pattern that can cite evidence and fail safely.',
            ],
            'context_strategy_plan' => $contextStrategy,
            'answer_contract' => [
                'answer' => 'Generated only from selected context.',
                'citations' => 'Source IDs or an explicit missing-evidence reason.',
                'confidence' => 'low, medium, or high from source agreement and freshness.',
            ],
        ];
    }

    private function ragPatternFor(array $input): string
    {
        if (($input['tool_use'] ?? null) === 'multi-step-agent') {
            return 'agentic-rag';
        }

        if (($input['relationship_need'] ?? null) === 'high') {
            return 'graph-rag';
        }

        return 'classic-rag';
    }

    private function contextStrategyFor(array $input, string $ragPattern): array
    {
        $strategy = $input['context_strategy'] ?? 'auto';

        if ($strategy === 'auto') {
            $strategy = (($input['freshness'] ?? null) === 'static' && ($input['risk_level'] ?? null) === 'low')
                ? 'cag'
                : 'rag';
        }

        return [
            'recommendation' => $strategy,
            'rag_pattern' => $ragPattern,
            'routing_sequence' => match ($strategy) {
                'long-context' => ['classify question', 'pack context', 'answer and audit'],
                'cag' => ['classify question', 'read cache', 'answer and audit'],
                'hybrid' => ['classify question', 'route context path', 'answer and audit'],
                default => ['classify question', 'retrieve evidence', 'answer and audit'],
            },
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Form request',
                'file' => 'app/Http/Requests/Api/PlanRagStrategyRequest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanRagStrategyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'knowledge_shape' => ['required', 'string', Rule::in(['documents', 'entities', 'workflows'])],
            'relationship_need' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'tool_use' => ['required', 'string', Rule::in(['none', 'retrieval-tools', 'multi-step-agent'])],
            'freshness' => ['required', 'string', Rule::in(['static', 'periodic', 'real-time'])],
            'risk_level' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'answer_style' => ['required', 'string', Rule::in(['summary', 'citations', 'actions'])],
            'context_strategy' => ['sometimes', 'string', Rule::in(['auto', 'rag', 'long-context', 'cag', 'hybrid'])],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Context router',
                'file' => 'app/Services/Rag/ChatbotContextRouter.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Rag;

final class ChatbotContextRouter
{
    public function route(string $strategy): array
    {
        return match ($strategy) {
            'cag' => [
                'path' => 'cache',
                'rule' => 'Read permission-aware cached context for stable FAQ or policy answers.',
            ],
            'long-context' => [
                'path' => 'pack',
                'rule' => 'Pack a bounded document set with source markers and token budgets.',
            ],
            'hybrid' => [
                'path' => 'route context path',
                'rule' => 'Send stable FAQ to CAG, fresh private docs to RAG, and bounded analysis to Long Context.',
            ],
            default => [
                'path' => 'retrieve',
                'rule' => 'Retrieve fresh evidence with permission filters and cite source IDs.',
            ],
        };
    }
}
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/RagStrategyPlanWorkbenchTest.php',
                'code' => <<<'PHP'
public function test_rag_strategy_plan_api_returns_cag_strategy_plan(): void
{
    $response = $this->postJson('/api/practice/rag-strategy-plan', [
        'knowledge_shape' => 'documents',
        'relationship_need' => 'low',
        'tool_use' => 'none',
        'freshness' => 'static',
        'risk_level' => 'low',
        'answer_style' => 'summary',
        'context_strategy' => 'cag',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.context_strategy_plan.recommendation', 'cag')
        ->assertJsonPath('data.context_strategy_plan.routing_sequence.1.step', 'read cache');
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for IDOR object-level authorization review practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function idorAccessControlSnippets(): array
    {
        return [
            [
                'label' => 'IDOR review service',
                'file' => 'app/Services/Practice/IdorAccessReviewService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class IdorAccessReviewService
{
    /**
     * @param  array{route: string, object_type: string, actor_scope: string, lookup_style: string, policy_check: string, covered_actions: array<int, string>}  $input
     * @return array<string, mixed>
     */
    public function review(array $input): array
    {
        $directLookup = str_contains(strtolower($input['lookup_style']), 'find');
        $hasObjectPolicy = $input['policy_check'] !== 'none';

        return [
            'technology' => 'idor-access-control',
            'object_surface' => [
                'route' => $input['route'],
                'object_type' => $input['object_type'],
                'actor_scope' => $input['actor_scope'],
                'covered_actions' => $input['covered_actions'],
            ],
            'risk_drivers' => array_values(array_filter([
                $directLookup ? 'Direct lookup can expose another user object before authorization.' : null,
                $hasObjectPolicy ? null : 'Missing object policy means login is treated as object-level authorization.',
                in_array('download', $input['covered_actions'], true) ? 'Download routes need the same object-level check as JSON routes.' : null,
                in_array('export', $input['covered_actions'], true) ? 'Export routes need cross-tenant denial tests.' : null,
            ])),
            'secure_pattern' => [
                'lookup' => '$invoice = $request->user()->invoices()->whereKey($invoiceId)->firstOrFail();',
                'authorization' => '$this->authorize("view", $invoice);',
                'test' => 'actingAs($attacker)->getJson("/api/invoices/{$victimInvoice->id}")->assertForbidden();',
            ],
            'review_checklist' => [
                'Inventory every route parameter that represents an object id.',
                'Scope lookup by current user, tenant, organization, team, or parent resource before returning data.',
                'Authorize the exact object and action with a Policy, Gate, FormRequest, or domain service.',
                'Add two-user or two-tenant ID-swap tests for read, update, delete, download, export, and nested resources.',
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Policy and lookup example',
                'file' => 'app/Policies/InvoicePolicy.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

final class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $invoice->tenant_id === $user->tenant_id
            && $invoice->user_id === $user->id;
    }
}

// Controller sketch:
// $invoice = $request->user()
//     ->invoices()
//     ->whereKey($invoiceId)
//     ->firstOrFail();
// Equivalent one-line pattern: $request->user()->invoices()->whereKey($invoiceId)->firstOrFail();
//
// $this->authorize('view', $invoice);
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/IdorAccessReviewWorkbenchTest.php',
                'code' => <<<'PHP'
public function test_attacker_cannot_replay_victim_object_id(): void
{
    $response = $this->postJson('/api/practice/idor-access-review', [
        'route' => 'GET /api/invoices/{invoice}',
        'object_type' => 'Invoice',
        'actor_scope' => 'tenant + owner',
        'lookup_style' => 'Invoice::findOrFail($id)',
        'policy_check' => 'none',
        'covered_actions' => ['read', 'download', 'export'],
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.technology', 'idor-access-control');

    $this->assertStringContainsString('Direct lookup', $response->json('data.risk_drivers.0'));
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for BFS and DFS traversal planning practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function graphTraversalSnippets(): array
    {
        return [
            [
                'label' => 'Traversal decision service',
                'file' => 'app/Services/Practice/GraphTraversalPlanService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class GraphTraversalPlanService
{
    public function plan(array $input): array
    {
        $goal = strtolower((string) ($input['goal'] ?? 'nearest-match'));
        $weightedEdges = (bool) ($input['weighted_edges'] ?? false);

        if ($weightedEdges && $goal === 'shortest-path') {
            $strategy = 'weighted-shortest-path-warning';
        } elseif (str_contains($goal, 'nearest') || str_contains($goal, 'shortest')) {
            $strategy = 'bfs';
        } else {
            $strategy = 'dfs';
        }

        return [
            'strategy' => $strategy,
            'state' => $strategy === 'bfs' ? 'queue frontier' : 'stack, recursion path, or priority queue',
            'guards' => ['visited set', 'max depth', 'max nodes', 'pagination or batch size'],
            'api_example' => 'Crawl related resources by hop count and rate-limit each level.',
            'database_example' => 'Read category trees in levels for expansion or depth-first for subtree validation.',
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Unit test',
                'file' => 'tests/Unit/Practice/GraphTraversalPlanServiceTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\GraphTraversalPlanService;
use PHPUnit\Framework\TestCase;

final class GraphTraversalPlanServiceTest extends TestCase
{
    public function test_nearest_or_shortest_goals_choose_bfs(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'goal' => 'nearest-match',
            'weighted_edges' => false,
        ]);

        $this->assertSame('bfs', $plan['strategy']);
        $this->assertContains('visited set', $plan['guards']);
        $this->assertContains('max depth', $plan['guards']);
    }

    public function test_branch_exploration_defaults_to_dfs(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'goal' => 'subtree-validation',
            'weighted_edges' => false,
        ]);

        $this->assertSame('dfs', $plan['strategy']);
        $this->assertSame('stack, recursion path, or priority queue', $plan['state']);
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for JavaScript closure interview practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function javascriptClosureSnippets(array $item = []): array
    {
        $haystack = strtolower(implode(' ', [
            $item['content']['title'] ?? '',
            $item['content']['body'] ?? '',
            $item['task'] ?? '',
        ]));

        if (str_contains($haystack, 'arrow') || str_contains($haystack, 'lexical this') || str_contains($haystack, 'call-site this')) {
            return $this->javascriptArrowThisSnippets();
        }

        if (str_contains($haystack, 'hoisting')) {
            return $this->javascriptHoistingSnippets();
        }

        return [
            [
                'label' => 'Closure counter example',
                'file' => 'resources/js/closure-counter.js',
                'code' => <<<'JS'
export function createCounter(initialValue = 0) {
  let count = initialValue;

  return function increment(step = 1) {
    count += step;

    return count;
  };
}

const counter = createCounter();

console.log(counter()); // 1
console.log(counter()); // 2
JS,
            ],
            [
                'label' => 'Interview checklist',
                'file' => 'resources/js/closure-interview-checklist.js',
                'code' => <<<'JS'
export const closureInterviewChecklist = [
  'A closure is a function plus access to variables from its lexical scope.',
  'The inner function keeps access to the variable binding, not a one-time copied value.',
  'Closures are useful for private state, callbacks, debounce, throttle, memoization, and event handlers.',
  'Common traps include var versus let in loops, stale closures, and incorrect hook dependencies.',
];
JS,
            ],
        ];
    }

    /**
     * Return starter code for JavaScript hoisting interview practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function javascriptHoistingSnippets(): array
    {
        return [
            [
                'label' => 'Hoisting trace',
                'file' => 'resources/js/hoisting-trace.js',
                'code' => <<<'JS'
sayHi(); // Works: function declarations are initialized during setup.

function sayHi() {
  return 'Hello from a hoisted function declaration';
}

console.log(userName); // undefined: var is hoisted and initialized.
var userName = 'Son';

// console.log(score); // ReferenceError: temporal dead zone.
let score = 10;

// run(); // TypeError: run is not a function.
var run = function () {
  return 'Function expression is assigned during execution';
};
JS,
            ],
            [
                'label' => 'Interview checklist',
                'file' => 'resources/js/hoisting-interview-checklist.js',
                'code' => <<<'JS'
export const hoistingInterviewChecklist = [
  'Hoisting means declarations are registered before code execution starts.',
  'Function declarations are callable before their source line.',
  'var is hoisted and initialized as undefined.',
  'let and const are hoisted but blocked by the temporal dead zone before declaration.',
  'Function expressions assigned to var are not callable until the assignment runs.',
];
JS,
            ],
        ];
    }

    /**
     * Return starter code for arrow-function `this` interview practice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function javascriptArrowThisSnippets(): array
    {
        return [
            [
                'label' => 'Arrow this comparison',
                'file' => 'resources/js/arrow-this-comparison.js',
                'code' => <<<'JS'
export const user = {
  name: 'Son',
  normalMethod() {
    return this.name;
  },
  arrowMethod: () => this?.name,
};

export function createTimer() {
  return {
    seconds: 0,
    start() {
      setTimeout(() => {
        this.seconds += 1;
      }, 1000);
    },
  };
}

console.log(user.normalMethod()); // 'Son'
console.log(user.arrowMethod()); // usually undefined in modules/strict mode
JS,
            ],
            [
                'label' => 'Interview checklist',
                'file' => 'resources/js/arrow-this-interview-checklist.js',
                'code' => <<<'JS'
export const arrowThisInterviewChecklist = [
  'Arrow functions do not have their own this.',
  'Arrow this is read from the lexical scope where the arrow was created.',
  'Normal function this is dynamic and depends on the call site.',
  'Arrow callbacks are useful when a method needs to keep outer this.',
  'Avoid arrow functions as object methods when the method needs this to be that object.',
  'call, apply, and bind cannot rebind this for an arrow function.',
];
JS,
            ],
        ];
    }

    /**
     * Return starter code for Predictive AI versus Generative AI comparison work.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function aiTypeComparisonSnippets(): array
    {
        return [
            [
                'label' => 'AI type comparison service',
                'file' => 'app/Services/Practice/AiTypeComparisonPlanService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class AiTypeComparisonPlanService
{
    public function compare(): array
    {
        return [
            'predictive_ai' => [
                'output_contract' => ['score', 'label', 'forecast', 'ranking', 'risk decision'],
                'input_evidence' => ['historical data', 'features', 'labels'],
                'quality_checks' => ['precision', 'recall', 'calibration', 'AUC', 'business lift'],
                'failure_modes' => ['overfitting', 'drift', 'biased labels'],
            ],
            'generative_ai' => [
                'output_contract' => ['text', 'image', 'code', 'summary', 'answer'],
                'input_evidence' => ['prompt', 'context', 'retrieved evidence', 'constraints'],
                'quality_checks' => ['groundedness', 'safety', 'citation quality', 'tests', 'human review'],
                'failure_modes' => ['hallucination', 'fabricated citations', 'prompt injection', 'unsafe code'],
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/AiTypeComparisonPlanTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Practice\AiTypeComparisonPlanService;
use Tests\TestCase;

final class AiTypeComparisonPlanTest extends TestCase
{
    public function test_plan_separates_prediction_and_generation_contracts(): void
    {
        $plan = (new AiTypeComparisonPlanService)->compare();

        $this->assertContains('forecast', $plan['predictive_ai']['output_contract']);
        $this->assertContains('code', $plan['generative_ai']['output_contract']);
        $this->assertContains('calibration', $plan['predictive_ai']['quality_checks']);
        $this->assertContains('groundedness', $plan['generative_ai']['quality_checks']);
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for AI agent memory classification and governance.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function aiAgentMemorySnippets(): array
    {
        return [
            [
                'label' => 'Agent memory contract',
                'file' => 'app/Services/Practice/AiAgentMemoryPlanService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class AiAgentMemoryPlanService
{
    public function plan(): array
    {
        return [
            'working_memory' => 'Current task goal, open files, failing test, constraints, and next action.',
            'episodic_memory' => 'Prior decisions, commands run, failed attempts, review notes, and session summaries.',
            'semantic_memory' => 'Durable repo rules, domain vocabulary, API contracts, data model, and team policy.',
            'procedural_memory' => 'Repeatable playbooks such as debug loop, test command, PR checklist, release steps, and incident flow.',
            'governance' => [
                'scope every memory by user, repo, project, and permission',
                'store source, freshness, confidence, and correction path',
                'keep private or stale memory from silently changing the next action',
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Agent memory test',
                'file' => 'tests/Feature/AiAgentMemoryPlanTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Practice\AiAgentMemoryPlanService;
use Tests\TestCase;

final class AiAgentMemoryPlanTest extends TestCase
{
    public function test_agent_memory_plan_separates_memory_types(): void
    {
        $plan = (new AiAgentMemoryPlanService)->plan();

        $this->assertArrayHasKey('working_memory', $plan);
        $this->assertArrayHasKey('episodic_memory', $plan);
        $this->assertArrayHasKey('semantic_memory', $plan);
        $this->assertArrayHasKey('procedural_memory', $plan);
        $this->assertContains('store source, freshness, confidence, and correction path', $plan['governance']);
    }
}
PHP,
            ],
        ];
    }

    /**
     * Return OAuth-specific starter snippets with Client Credentials coverage.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function oauthFlowSnippets(): array
    {
        return [
            [
                'label' => 'OAuth planning service',
                'file' => 'app/Services/Practice/OauthFlowPlanService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class OauthFlowPlanService
{
    public function plan(array $input): array
    {
        $flow = $input['client_type'] === 'service-to-service'
            ? 'client-credentials'
            : 'authorization-code-pkce';

        return [
            'recommendation' => ['flow' => $flow],
            'client_credentials_checklist' => [
                'Token endpoint requires confidential client authentication.',
                'Resource server validates audience and service scope.',
                'Client secret has owner, rotation, and leak response.',
            ],
            'pkce_sequence_simulation' => [
                'applies' => $flow === 'authorization-code-pkce',
                'title' => 'Authorization Code with PKCE sequence',
                'steps' => [
                    [
                        'stage' => '1. Create verifier',
                        'actor' => 'public client',
                        'sends' => 'Nothing leaves the client yet.',
                        'security_point' => 'code_verifier stays private to this login attempt.',
                    ],
                    [
                        'stage' => '2. Authorize request',
                        'actor' => 'public client',
                        'sends' => 'response_type=code, code_challenge, code_challenge_method=S256, state, redirect_uri.',
                        'security_point' => 'Only the derived challenge travels through the browser redirect.',
                    ],
                    [
                        'stage' => '3. Token exchange',
                        'actor' => 'token endpoint client',
                        'sends' => 'authorization code plus original code_verifier.',
                        'security_point' => 'A stolen authorization code fails without the verifier.',
                    ],
                ],
            ],
            'pkce_failure_drill' => [
                [
                    'case' => 'missing verifier',
                    'test' => 'Token endpoint rejects an authorization_code grant without code_verifier.',
                ],
                [
                    'case' => 'wrong verifier',
                    'test' => 'Token endpoint rejects a code_verifier that does not match the S256 challenge.',
                ],
                [
                    'case' => 'reused code',
                    'test' => 'Token endpoint rejects a second exchange attempt for the same authorization code.',
                ],
            ],
            'client_credentials_access_contract' => [
                [
                    'resource' => 'Orders API',
                    'audience' => 'api://orders',
                    'allowed_scopes' => ['orders.read'],
                    'denied_by_default' => ['users.impersonate'],
                ],
            ],
            'client_credentials_token_validation_policy' => [
                [
                    'claim' => 'aud',
                    'rule' => 'Audience must match the receiving API.',
                    'reject_when' => 'Token audience is missing or points to another API.',
                ],
                [
                    'claim' => 'scope',
                    'rule' => 'Scope must include the exact service permission required by the route.',
                    'reject_when' => 'Required route scope is missing or too broad.',
                ],
            ],
            'client_credentials_operational_plan' => [
                'applies' => $flow === 'client-credentials',
                'audit_events' => [
                    'oauth_client_credentials_token_issued',
                    'oauth_client_secret_rotated',
                ],
            ],
            'client_credentials_rotation_drill' => [
                [
                    'phase' => 'prepare',
                    'action' => 'Create a second active credential before revoking the old one.',
                    'verification' => 'New secret gets a scoped token and old secret still works during rollout.',
                ],
                [
                    'phase' => 'revoke old secret',
                    'action' => 'Disable the previous credential after all instances use the new secret.',
                    'verification' => 'Old secret fails authentication and oauth_client_secret_rotated is logged.',
                ],
            ],
            'client_credentials_monitoring_signals' => [
                'metrics' => [
                    'oauth_client_auth_failed_total by client_id and failure_reason',
                    'oauth_service_audience_denied_total by client_id and resource_audience',
                ],
                'alerts' => [
                    'Alert when client-auth failures spike for one client_id.',
                    'Alert when a client secret exceeds the maximum rotation age.',
                ],
            ],
            'client_credentials_failure_playbook' => [
                [
                    'failure' => 'client authentication failure',
                    'action' => 'Check recent secret rotation, deployment config, and secret-manager version.',
                ],
                [
                    'failure' => 'wrong audience',
                    'action' => 'Block cross-API token reuse and fix the client access contract.',
                ],
            ],
            'client_credentials_security_review_rubric' => [
                [
                    'criterion' => 'confidential credential boundary',
                    'weight' => 25,
                ],
                [
                    'criterion' => 'audience and scope enforcement',
                    'weight' => 25,
                ],
            ],
            'client_credentials_interview_checklist' => [
                'Use only for machine-to-machine access.',
                'Keep client credentials server-side in confidential clients.',
                'Validate audience and exact service scopes at the resource server.',
                'Mention secret rotation, audit logs, monitoring, and incident response.',
            ],
            'implementation_snippets' => [
                'client_credentials_token_request' => "POST /oauth/token\ngrant_type=client_credentials&scope=orders.read",
            ],
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/OauthFlowPlanWorkbenchTest.php',
                'code' => <<<'PHP'
public function test_service_client_uses_client_credentials(): void
{
    $response = $this->postJson('/api/practice/oauth-flow-plan', [
        'client_type' => 'service-to-service',
        'current_flow' => 'client-credentials',
        'can_keep_secret' => 'yes',
        'pkce_supported' => 'no',
        'refresh_token_needed' => 'no',
        'browser_history_risk' => 'low',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.recommendation.flow', 'client-credentials')
        ->assertJsonPath('data.client_credentials_checklist.0', 'Token endpoint requires confidential client authentication.');
}
PHP,
            ],
        ];
    }

    /**
     * Return starter code for a web route, controller, service, and Blade slice.
     *
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function webSnippets(): array
    {
        return [
            [
                'label' => 'Web route',
                'file' => 'routes/web/practice-content.php',
                'code' => <<<'PHP'
// Show the content-backed web drill.
Route::get('/practice/content-backed-drill', ContentBackedWebDrillController::class)
    ->name('practice.content-backed-drill');
PHP,
            ],
            [
                'label' => 'Feature test',
                'file' => 'tests/Feature/ContentBackedWebDrillTest.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentBackedWebDrillTest extends TestCase
{
    public function test_page_renders_practice_content(): void
    {
        $response = $this->get('/practice/content-backed-drill');

        $response
            ->assertOk()
            ->assertSee('Content-backed drill');
    }
}
PHP,
            ],
            [
                'label' => 'Controller',
                'file' => 'app/Http/Controllers/Practice/ContentBackedWebDrillController.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ContentBackedDrillService;
use Illuminate\Contracts\View\View;

final class ContentBackedWebDrillController extends Controller
{
    public function __invoke(ContentBackedDrillService $service): View
    {
        return view('practice.content-backed-drill', [
            'drill' => $service->build(),
        ]);
    }
}
PHP,
            ],
            [
                'label' => 'Service',
                'file' => 'app/Services/Practice/ContentBackedDrillService.php',
                'code' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentBackedDrillService
{
    public function build(): array
    {
        return [
            'title' => 'Content-backed drill',
            'status' => 'ready',
        ];
    }
}
PHP,
            ],
            [
                'label' => 'Blade view',
                'file' => 'resources/views/practice/content-backed-drill.blade.php',
                'code' => <<<'BLADE'
@extends('learning.layout', ['title' => $drill['title']])

@section('content')
    <section class="hero">
        <span class="badge">{{ $drill['status'] }}</span>
        <h1>{{ $drill['title'] }}</h1>
    </section>
@endsection
BLADE,
            ],
        ];
    }
}

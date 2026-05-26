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
        return match ($item['technology']) {
            'api-validation' => $this->apiValidationSnippets(),
            'auth-security' => $this->authSecuritySnippets(),
            'database-eloquent' => $this->databaseSnippets(),
            'files-media' => $this->fileStorageSnippets(),
            'async-workflow' => $this->asyncWorkflowSnippets(),
            'performance-cache' => $this->performanceCacheSnippets(),
            'container-architecture' => $this->containerArchitectureSnippets(),
            'realtime-events' => $this->realtimeEventSnippets(),
            'blade-ui' => $this->bladeUiSnippets(),
            'php' => $this->phpSnippets(),
            default => $this->webSnippets(),
        };
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
    private function phpSnippets(): array
    {
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

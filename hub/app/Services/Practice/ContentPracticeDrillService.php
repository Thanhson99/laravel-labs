<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentPracticeDrillService
{
    /**
     * Create a drill service from mapped content practice items.
     */
    public function __construct(
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeStarterSnippetService $snippets,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
    ) {}

    /**
     * Build one focused practice drill from JSON content.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $result = $this->mapper->map($filters + ['limit' => 1]);
        $item = $result['items'][0] ?? null;

        if ($item === null) {
            return null;
        }

        return [
            'title' => sprintf('Content Drill: %s', $item['content']['title']),
            'technology' => $item['technology'],
            'source' => $item['source'],
            'content' => $item['content'],
            'practice' => $item['practice'],
            'goal' => $item['task'],
            'implementation_steps' => $this->stepsFor($item['technology']),
            'files' => $this->filesFor($item['technology']),
            'related_workbench' => $this->workbenches->linkFor($item['technology']),
            'starter_snippets' => $this->snippets->snippetsFor($item),
            'commands' => [
                'php artisan test --filter ContentBackedDrill',
                'php artisan route:list --path=content-backed-drill',
                'php artisan test',
                'vendor\\bin\\pint --test',
            ],
            'acceptance' => [
                'The drill references the exact JSON source file.',
                'The implementation changes Laravel code, not the content file.',
                'A focused test proves the behavior.',
                'The route, request, controller, service, and view responsibilities stay separated.',
                'Full test suite and Pint pass.',
            ],
        ];
    }

    /**
     * Return concrete coding steps for one technology.
     *
     * @return array<int, string>
     */
    private function stepsFor(string $technology): array
    {
        return match ($technology) {
            'api-validation' => [
                'Create or update a Form Request that validates the concept from the selected content record.',
                'Add a thin API controller method that returns a stable JSON response.',
                'Write one success API test and one 422 validation test.',
            ],
            'auth-security' => [
                'Turn the selected security rule into a policy method or gate.',
                'Call authorization before the controller mutates or reveals protected data.',
                'Write one allowed test and one forbidden 403 test.',
            ],
            'database-eloquent' => [
                'Model the selected data concept with a migration or Eloquent relationship.',
                'Move reusable reads into a query or service method.',
                'Write a test that proves the stored shape or query result.',
            ],
            'files-media' => [
                'Validate file type, size, and visibility before storage.',
                'Store through a named disk and return only safe path metadata.',
                'Test success, validation failure, and cleanup expectations.',
            ],
            'async-workflow' => [
                'Name the event or job after a completed business action.',
                'Keep controller work synchronous and dispatch side effects after validation.',
                'Use Event::fake or Queue::fake in the focused test.',
            ],
            'performance-cache' => [
                'Create a scoped cache key for the selected read path.',
                'Keep the source query correct when the cache is empty.',
                'Test cache miss, cache hit, and invalidation ownership.',
            ],
            'container-architecture' => [
                'Extract a small contract for the selected dependency boundary.',
                'Bind the contract to an implementation in a service provider.',
                'Inject the contract into the controller or service that needs it.',
            ],
            'realtime-events' => [
                'Represent the selected realtime action as an event payload.',
                'Keep broadcast payloads small and safe for clients.',
                'Test dispatch behavior before wiring browser subscriptions.',
            ],
            'blade-ui' => [
                'Prepare display data in a service or controller before rendering.',
                'Render escaped values in Blade and avoid business logic in the view.',
                'Write a feature test that checks the visible UI state.',
            ],
            'testing-quality' => [
                'Write a failing-first test that describes the selected content concept.',
                'Implement the smallest service or controller change that makes the test pass.',
                'Run Pint and refactor only after the behavior is protected.',
            ],
            'docker-runtime' => [
                'Turn the selected runtime concept into a smoke-check item.',
                'Expose the check through a service-backed API response.',
                'Document the command that proves the runtime path works.',
            ],
            'php' => [
                'Write a typed PHP service method for the selected concept.',
                'Cover empty or invalid input with a guard clause.',
                'Add a unit test for normal and edge-case input.',
            ],
            default => [
                'Create a named route for the selected concept.',
                'Keep controller logic thin and move workflow logic into a service.',
                'Render escaped output in Blade and cover the page with a feature test.',
            ],
        };
    }

    /**
     * Return suggested files for one technology.
     *
     * @return array<int, string>
     */
    private function filesFor(string $technology): array
    {
        return match ($technology) {
            'api-validation' => [
                'routes/api.php',
                'app/Http/Requests/Api',
                'app/Http/Controllers/Api',
                'tests/Feature',
            ],
            'auth-security' => [
                'app/Policies',
                'app/Providers/AuthServiceProvider.php',
                'app/Http/Controllers',
                'tests/Feature',
            ],
            'database-eloquent' => [
                'database/migrations',
                'app/Models',
                'app/Services/Practice',
                'tests/Feature',
            ],
            'files-media' => [
                'app/Http/Requests/Api',
                'app/Services/Practice',
                'config/filesystems.php',
                'tests/Feature',
            ],
            'async-workflow' => [
                'app/Events',
                'app/Listeners',
                'app/Jobs',
                'tests/Feature',
            ],
            'performance-cache' => [
                'app/Services/Practice',
                'config/cache.php',
                'tests/Unit/Practice',
                'tests/Feature',
            ],
            'container-architecture' => [
                'app/Contracts',
                'app/Services',
                'app/Providers/AppServiceProvider.php',
                'tests/Unit/Practice',
            ],
            'realtime-events' => [
                'app/Events',
                'routes/channels.php',
                'config/broadcasting.php',
                'tests/Feature',
            ],
            'blade-ui' => [
                'app/Http/Controllers/Practice',
                'app/Services/Practice',
                'resources/views/practice',
                'tests/Feature',
            ],
            'testing-quality' => [
                'app/Services/Practice',
                'tests/Unit/Practice',
                'tests/Feature',
            ],
            'docker-runtime' => [
                'Dockerfile',
                'docker-compose.yml',
                '.env.example',
                'app/Services/Practice/RuntimeSmokeCheckService.php',
            ],
            'php' => [
                'app/Practice/Php',
                'tests/Unit/Practice',
            ],
            default => [
                'routes/web.php',
                'app/Http/Controllers/Practice',
                'app/Services/Practice',
                'resources/views/practice',
                'tests/Feature',
            ],
        };
    }
}

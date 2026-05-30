<?php

declare(strict_types=1);

$runtimeBaseUrl = rtrim((string) env('HUB_RUNTIME_BASE_URL', 'http://localhost:8088'), '/');

return [
    'tracks' => [
        [
            'slug' => 'php-foundation',
            'name' => 'PHP Foundation',
            'summary' => 'Practice typed PHP functions, guard clauses, arrays, and CLI output before touching Laravel magic.',
        ],
        [
            'slug' => 'laravel-http',
            'name' => 'Laravel HTTP',
            'summary' => 'Practice routes, controllers, requests, Blade rendering, and thin controller boundaries.',
        ],
        [
            'slug' => 'api-validation',
            'name' => 'API + Validation',
            'summary' => 'Practice JSON endpoints, Form Requests, response shape, and validation failure handling.',
        ],
        [
            'slug' => 'testing-quality',
            'name' => 'Testing + Quality',
            'summary' => 'Practice feature tests, service tests, assertions, Pint, and behavior-focused verification.',
        ],
        [
            'slug' => 'docker-runtime',
            'name' => 'Docker + Runtime',
            'summary' => 'Practice reproducible local runtime, environment values, compose config, and smoke checks.',
        ],
        [
            'slug' => 'frontend-performance',
            'name' => 'Frontend Performance',
            'summary' => 'Practice React render measurement, memoization decisions, profiler evidence, state locality, and large-list tradeoffs.',
        ],
        [
            'slug' => 'security-auth',
            'name' => 'Security + Auth',
            'summary' => 'Practice SQL Injection prevention, authorization, JWT storage and revocation, OAuth flow choices, and security-focused verification.',
        ],
        [
            'slug' => 'ai-review',
            'name' => 'AI Review',
            'summary' => 'Practice evidence-first AI coding workflows, hallucination controls, review lenses, and verification commands.',
        ],
    ],

    'exercises' => [
        [
            'slug' => 'php-cli-input-normalizer',
            'track' => 'php-foundation',
            'title' => 'Build a PHP CLI input normalizer',
            'objective' => 'Create a small PHP script that receives raw names, trims invalid input, normalizes casing, and prints a clean result.',
            'why' => 'This practices plain PHP before framework code: functions, arrays, return types, guard clauses, and predictable output.',
            'steps' => [
                'Create `practice/php/name_normalizer.php`.',
                'Write `normalizeName(string $value): string` with trimming and whitespace cleanup.',
                'Write `normalizeMany(array $names): array` and skip empty values.',
                'Print the normalized list line by line.',
                'Add one invalid input case and make the script handle it cleanly.',
            ],
            'files' => [
                'practice/php/name_normalizer.php',
            ],
            'commands' => [
                'php practice/php/name_normalizer.php',
                'php -l practice/php/name_normalizer.php',
                'php artisan test --filter NameNormalizer',
            ],
            'acceptance' => [
                'The script runs from CLI without warnings.',
                'Empty input is skipped.',
                'Functions use parameter and return types.',
                'Output can be checked by reading the terminal.',
            ],
            'workbench' => [
                'label' => 'Run in Laravel workbench',
                'route' => 'practice.workbench.name-normalizer',
            ],
            'starter_code' => <<<'PHP'
<?php

declare(strict_types=1);

function normalizeName(string $value): string
{
    $value = trim($value);
    $value = preg_replace('/\s+/', ' ', $value) ?? '';

    return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
}

$names = ['  jane   doe ', '', 'LARAVEL labs'];

foreach ($names as $name) {
    $normalized = normalizeName($name);

    if ($normalized === '') {
        continue;
    }

    echo $normalized.PHP_EOL;
}
PHP,
        ],
        [
            'slug' => 'laravel-thin-controller',
            'track' => 'laravel-http',
            'title' => 'Build a thin-controller learning note page',
            'objective' => 'Create a Laravel route and controller that renders a practice note while keeping transformation logic out of the controller.',
            'why' => 'This practices routing, controller boundaries, service extraction, Blade escaping, and feature testing.',
            'steps' => [
                'Create a controller under `app/Http/Controllers/Practice`.',
                'Create a service that returns the note data.',
                'Render the data in a Blade view with escaped output.',
                'Add a route named `practice.notes.show`.',
                'Write a feature test that checks status and visible note text.',
            ],
            'files' => [
                'routes/web.php',
                'app/Http/Controllers/Practice/NoteController.php',
                'app/Services/Practice/PracticeNoteService.php',
                'resources/views/practice/note.blade.php',
                'app/Services/Practice/HttpRequestFlowTracerService.php',
                'resources/views/practice/workbench/http-request-flow.blade.php',
                'app/Services/Practice/SecurityEscapePreviewService.php',
                'resources/views/practice/workbench/security-escape-preview.blade.php',
                'app/Services/Practice/CollectionFilterPreviewService.php',
                'resources/views/practice/workbench/collection-filter-preview.blade.php',
                'tests/Feature/PracticeNoteTest.php',
            ],
            'commands' => [
                'php artisan route:list',
                'php artisan test --filter PracticeNoteTest',
                'php artisan test --filter HttpRequestFlowWorkbenchTest',
                'php artisan test --filter SecurityEscapePreviewWorkbenchTest',
                'php artisan test --filter CollectionFilterPreviewWorkbenchTest',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the HTTP request-flow workbench',
                'route' => 'practice.workbench.http-request-flow',
            ],
            'page' => [
                'label' => 'Open the thin-controller note page',
                'route' => 'practice.notes.show',
            ],
            'acceptance' => [
                'Controller only calls the service and returns a view.',
                'Blade uses escaped output.',
                'Route is named and visible in route:list.',
                'Feature test passes.',
            ],
            'starter_code' => <<<'PHP'
final class NoteController
{
    public function __invoke(PracticeNoteService $notes): View
    {
        return view('practice.note', [
            'note' => $notes->first(),
        ]);
    }
}
PHP,
        ],
        [
            'slug' => 'blade-escaping-xss-preview',
            'track' => 'security-auth',
            'title' => 'Preview XSS-safe Blade escaping',
            'objective' => 'Practice identifying reflected, stored, and DOM XSS risks and proving unsafe input renders as escaped text.',
            'why' => 'This practices browser-output security: Blade escaping, raw HTML boundaries, safe JSON handoff, rich-text sanitization, CSP as defense in depth, and payload tests.',
            'steps' => [
                'Open the security escape preview workbench with a script-like payload.',
                'Compare escaped Blade output with risky raw HTML rendering.',
                'List which output context is involved: HTML text, attribute, JavaScript, URL, or rich text.',
                'Write one feature test that proves the payload is visible as text and does not execute.',
                'Document when `{!! !!}` is acceptable and which sanitizer or trust boundary owns it.',
            ],
            'files' => [
                'app/Services/Practice/SecurityEscapePreviewService.php',
                'app/Http/Requests/Api/PreviewEscapedContentRequest.php',
                'resources/views/practice/workbench/security-escape-preview.blade.php',
                'tests/Feature/SecurityEscapePreviewWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter SecurityEscapePreviewWorkbenchTest',
                'php artisan route:list --path=security-escape-preview',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the XSS escape preview workbench',
                'route' => 'practice.workbench.security-escape-preview',
            ],
            'acceptance' => [
                'Unsafe payloads render as escaped text in normal Blade output.',
                'The plan names the output context and why that context matters.',
                'Raw HTML rendering is allowed only with a sanitizer or trusted boundary.',
                'Feature tests cover a script-like payload and a harmless rich-text case.',
            ],
            'starter_code' => <<<'BLADE'
<p>{{ $comment->body }}</p>

{{-- Only render sanitized or trusted HTML here. --}}
{!! $trustedHtml !!}

<script>
    window.pageData = @json($viewModel);
</script>
BLADE,
        ],
        [
            'slug' => 'api-form-request-slice',
            'track' => 'api-validation',
            'title' => 'Build a validated practice API endpoint',
            'objective' => 'Create a JSON endpoint that accepts a topic title and returns a stable response after Form Request validation.',
            'why' => 'This practices API routes, Form Requests, JSON response shape, validation errors, and API feature tests.',
            'steps' => [
                'Create `StorePracticeTopicRequest` with `title` and `track` rules.',
                'Create an API controller method that accepts the request.',
                'Return `{ data, message }` JSON on success.',
                'Write one success test and one validation failure test.',
            ],
            'files' => [
                'routes/api.php',
                'app/Http/Requests/Api/StorePracticeTopicRequest.php',
                'app/Http/Controllers/Api/PracticeTopicController.php',
                'app/Services/Practice/PracticeTopicIntakeService.php',
                'resources/views/practice/workbench/topic-intake.blade.php',
                'tests/Feature/PracticeTopicApiTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeTopicApiTest',
                'php artisan test --filter PracticeTopicWorkbenchTest',
                'php artisan route:list',
            ],
            'workbench' => [
                'label' => 'Run the API validation workbench',
                'route' => 'practice.workbench.topic-intake',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/topics',
                'payload' => [
                    'title' => 'Build a validated API slice',
                    'track' => 'api-validation',
                    'difficulty' => 'beginner',
                ],
            ],
            'acceptance' => [
                'Invalid payload returns 422.',
                'Valid payload returns JSON with `data.title`.',
                'No business logic is placed in the route file.',
            ],
            'starter_code' => <<<'PHP'
return response()->json([
    'data' => [
        'title' => $request->validated('title'),
        'track' => $request->validated('track'),
    ],
    'message' => 'Practice topic accepted.',
], 201);
PHP,
        ],
        [
            'slug' => 'restful-api-naming-review',
            'track' => 'api-validation',
            'title' => 'Review and name RESTful API endpoints',
            'objective' => 'Turn a feature brief into predictable RESTful routes with resource nouns, HTTP verbs, query parameters, route names, and action endpoints only where the domain needs them.',
            'why' => 'This practices API contract design before code is written, so controllers, tests, OpenAPI docs, and clients all use the same language.',
            'steps' => [
                'List the resources, collections, single-resource lookups, and real domain actions.',
                'Name collection routes with plural nouns and choose the HTTP verb for each operation.',
                'Move filtering, searching, sorting, and pagination into query parameters.',
                'Keep nested resources shallow and explain the authorization context for each nested path.',
                'Write Laravel route names and one feature-test expectation for the most important endpoint.',
            ],
            'files' => [
                'routes/api.php',
                'routes/api/practice-actions.php',
                'routes/web/workbench.php',
                'app/Http/Requests/Api/PlanRestfulApiNamingRequest.php',
                'app/Http/Controllers/Api/RestfulApiNamingPlanController.php',
                'app/Services/Practice/RestfulApiNamingPlanService.php',
                'resources/views/practice/workbench/restful-api-naming-plan.blade.php',
                'tests/Feature/RestfulApiNamingPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan route:list --path=restful-api-naming-plan',
                'php artisan test --filter RestfulApiNamingPlanWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the RESTful API naming workbench',
                'route' => 'practice.workbench.restful-api-naming-plan',
            ],
            'acceptance' => [
                'Every endpoint path uses a resource noun unless it is a deliberate business action.',
                'HTTP verbs communicate read, create, update, delete, or command intent.',
                'Query parameters carry filter, search, pagination, and sort behavior.',
                'Laravel route names are stable and match the resource vocabulary.',
                'At least one feature test proves the chosen route and response shape.',
            ],
            'starter_code' => <<<'PHP'
Route::prefix('v1')->group(function () {
    Route::apiResource('orders', OrderController::class);

    Route::post('/orders/{order}/cancel', CancelOrderController::class)
        ->name('orders.cancel');

    Route::get('/users/{user}/orders', UserOrderController::class)
        ->name('users.orders.index');
});
PHP,
        ],
        [
            'slug' => 'practice-session-planner',
            'track' => 'laravel-http',
            'title' => 'Build a daily practice session planner',
            'objective' => 'Create a Laravel page and API that compose native practice exercises into a small daily plan.',
            'why' => 'This practices service composition, controller dependency injection, query filters, Blade rendering, JSON responses, and feature tests.',
            'steps' => [
                'Create a service that builds a session from configured native exercises.',
                'Create a web controller and Blade view for the session page.',
                'Create an API controller for the same session data.',
                'Add route names for the page and API.',
                'Write feature tests for the page and filtered API response.',
            ],
            'files' => [
                'app/Services/Practice/PracticeSessionPlannerService.php',
                'app/Http/Controllers/Practice/PracticeSessionController.php',
                'app/Http/Controllers/Api/PracticeSessionPlanController.php',
                'resources/views/practice/session.blade.php',
                'tests/Feature/PracticeSessionTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeSessionTest',
                'php artisan route:list --path=practice',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open today practice session',
                'route' => 'practice.sessions.today',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/session-plan?track=testing-quality',
                'payload' => [
                    'note' => 'No request body. Use the track query string to filter the plan.',
                ],
            ],
            'acceptance' => [
                'The session page shows native practice exercises.',
                'The API returns the same plan shape under `data`.',
                'A track query filters the selected exercises.',
                'Tests describe the page and API behavior.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeSessionPlannerService
{
    public function today(?string $track = null): array
    {
        return [
            'title' => 'Today Practice Session',
            'focus' => $track ?: 'all-tracks',
            'exercises' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'content-to-practice-map',
            'track' => 'laravel-http',
            'title' => 'Map learning content to native practice tasks',
            'objective' => 'Read JSON content and question records, infer the related technology, and point learners to a real Laravel practice exercise.',
            'why' => 'This keeps practice grounded in the existing content files while still making Laravel code the place where learning happens.',
            'steps' => [
                'Read question-like records through the content repository.',
                'Infer a technology from the source path, topic, title, body, and answer.',
                'Map that technology to a native practice exercise.',
                'Render the map in a Blade page and expose the same data through an API.',
                'Write tests that prove JSON content becomes a code-first task.',
            ],
            'files' => [
                'app/Services/Practice/ContentPracticeMapperService.php',
                'app/Http/Controllers/Practice/ContentPracticeMapController.php',
                'app/Http/Controllers/Api/ContentPracticeMapController.php',
                'resources/views/practice/content-map.blade.php',
                'tests/Feature/ContentPracticeMapTest.php',
            ],
            'commands' => [
                'php artisan test --filter ContentPracticeMapTest',
                'php artisan route:list --path=practice',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open content to practice map',
                'route' => 'practice.content-map',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/content-map?family=laravel&language=en&search=api&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use query filters for source family, language, technology, and search.',
                ],
            ],
            'acceptance' => [
                'JSON records are not just displayed; they become concrete coding tasks.',
                'Each mapped item includes source, content, technology, practice, and task fields.',
                'The page links back to the source JSON and the native practice exercise.',
                'API and page tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class ContentPracticeMapperService
{
    public function map(array $filters = []): array
    {
        return [
            'items' => [],
            'meta' => ['filters' => $filters],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'content-practice-drill',
            'track' => 'laravel-http',
            'title' => 'Build a content-backed coding drill',
            'objective' => 'Select one JSON content record and turn it into a focused Laravel coding drill with files, steps, commands, and acceptance criteria.',
            'why' => 'This makes the content files actionable: a learner can move from one question or topic directly into a small implementation task.',
            'steps' => [
                'Filter mapped content by source key and technology.',
                'Build one drill from the first matching content record.',
                'Generate technology-specific implementation steps.',
                'Render a Blade page and expose the same drill through an API.',
                'Write tests for page rendering, API shape, and not-found behavior.',
            ],
            'files' => [
                'app/Services/Practice/ContentPracticeDrillService.php',
                'app/Http/Controllers/Practice/ContentPracticeDrillController.php',
                'app/Http/Controllers/Api/ContentPracticeDrillController.php',
                'resources/views/practice/content-drill.blade.php',
                'tests/Feature/ContentPracticeDrillTest.php',
            ],
            'commands' => [
                'php artisan test --filter ContentPracticeDrillTest',
                'php artisan route:list --path=practice',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open content drill builder',
                'route' => 'practice.content-drill',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/content-drill?source_key=laravel-api-integration-en-json&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use source_key and technology query filters.',
                ],
            ],
            'acceptance' => [
                'The drill names the exact source JSON path.',
                'The drill points to a native practice exercise.',
                'The drill lists concrete files and commands.',
                'Unknown source filters return 404 from the API.',
            ],
            'starter_code' => <<<'PHP'
final class ContentPracticeDrillService
{
    public function build(array $filters): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'question-drill-set',
            'track' => 'testing-quality',
            'title' => 'Build question-backed coding drill cards',
            'objective' => 'Turn filtered JSON question records into drill cards that link to exact content-backed coding tasks.',
            'why' => 'This connects review mode with implementation mode: each question becomes a small Laravel task tied to its source file and technology.',
            'steps' => [
                'Map question records through the content-to-practice mapper.',
                'Keep the original record id and source path for traceability.',
                'Generate a drill query for each card.',
                'Render the cards in Blade and expose them through an API.',
                'Write tests for page output and API shape.',
            ],
            'files' => [
                'app/Services/Practice/QuestionDrillSetService.php',
                'app/Http/Controllers/Practice/QuestionDrillSetController.php',
                'app/Http/Controllers/Api/QuestionDrillSetController.php',
                'resources/views/practice/question-drills.blade.php',
                'tests/Feature/QuestionDrillSetTest.php',
            ],
            'commands' => [
                'php artisan test --filter QuestionDrillSetTest',
                'php artisan test --filter ContentPracticeDrillTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open question drill set',
                'route' => 'practice.question-drills',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/question-drills?family=laravel&language=en&search=api&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use filters to choose question records.',
                ],
            ],
            'acceptance' => [
                'Each card includes a record id and source JSON path.',
                'Each card links to a content drill for that exact record.',
                'Each card links to a native practice exercise.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class QuestionDrillSetService
{
    public function build(array $filters = []): array
    {
        return [
            'items' => [],
            'meta' => ['filters' => $filters],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'technology-practice-matrix',
            'track' => 'laravel-http',
            'title' => 'Build a technology practice matrix',
            'objective' => 'Group JSON content and question records by inferred technology, then link each group to native exercises and sample drills.',
            'why' => 'This gives the learner a high-level map from content coverage to practical Laravel work by technology.',
            'steps' => [
                'Read question-like records from the content repository.',
                'Infer a technology for each record.',
                'Group records by technology and count sources.',
                'Attach a native practice exercise and sample content drill to each technology.',
                'Render the matrix in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/TechnologyPracticeMatrixService.php',
                'app/Http/Controllers/Practice/TechnologyPracticeMatrixController.php',
                'app/Http/Controllers/Api/TechnologyPracticeMatrixController.php',
                'resources/views/practice/technology-matrix.blade.php',
                'tests/Feature/TechnologyPracticeMatrixTest.php',
            ],
            'commands' => [
                'php artisan test --filter TechnologyPracticeMatrixTest',
                'php artisan route:list --path=practice',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open technology practice matrix',
                'route' => 'practice.technology-matrix',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/technology-matrix?family=laravel&language=en&search=api',
                'payload' => [
                    'note' => 'No request body. Use filters to focus the matrix.',
                ],
            ],
            'acceptance' => [
                'Records are grouped by inferred technology.',
                'Each technology includes record and source counts.',
                'Each technology links to a native exercise and a sample drill.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class TechnologyPracticeMatrixService
{
    public function build(array $filters = []): array
    {
        return [
            'items' => [],
            'meta' => ['filters' => $filters],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'technology-practice-board',
            'track' => 'laravel-http',
            'title' => 'Build a technology practice board',
            'objective' => 'Group filtered JSON records by source file for one technology and link each source to packs, queues, and record workspaces.',
            'why' => 'This gives learners a technology-first entry point while keeping every task tied to real content files and records.',
            'steps' => [
                'Filter content records by inferred technology.',
                'Group matching records by source file.',
                'Show source pack links and sample record workspace links.',
                'Expose a queue query for the selected technology.',
                'Render the board in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/TechnologyPracticeBoardService.php',
                'app/Http/Controllers/Practice/TechnologyPracticeBoardController.php',
                'app/Http/Controllers/Api/TechnologyPracticeBoardController.php',
                'resources/views/practice/technology-board.blade.php',
                'tests/Feature/TechnologyPracticeBoardTest.php',
            ],
            'commands' => [
                'php artisan test --filter TechnologyPracticeBoardTest',
                'php artisan test --filter PracticeQueueTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open technology practice board',
                'route' => 'practice.technology-board',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/technology-board?technology=api-validation&family=laravel&language=en&search=api',
                'payload' => [
                    'note' => 'No request body. Use filters to focus the board.',
                ],
            ],
            'acceptance' => [
                'The board groups matching records by source file.',
                'Each source group links to a source pack.',
                'Each source group includes sample record workspace links.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class TechnologyPracticeBoardService
{
    public function build(array $filters = []): array
    {
        return [
            'technology' => 'api-validation',
            'sources' => [],
            'queue_query' => [],
            'meta' => ['filters' => $filters],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'source-practice-pack',
            'track' => 'laravel-http',
            'title' => 'Build a source-file practice pack',
            'objective' => 'Turn one JSON content file into a practice pack with technology paths, sample drills, workflow, and verification commands.',
            'why' => 'This lets a learner pick one content file, such as an API integration question file, and immediately see how to practice it in Laravel code.',
            'steps' => [
                'Find one decoded source by source key.',
                'Map all records in that source to technologies.',
                'Group records into technology paths.',
                'Attach sample drills and native exercises to each path.',
                'Render the pack in Blade and expose the same pack through an API.',
            ],
            'files' => [
                'app/Services/Practice/SourcePracticePackService.php',
                'app/Http/Controllers/Practice/SourcePracticePackController.php',
                'app/Http/Controllers/Api/SourcePracticePackController.php',
                'resources/views/practice/source-pack.blade.php',
                'tests/Feature/SourcePracticePackTest.php',
            ],
            'commands' => [
                'php artisan test --filter SourcePracticePackTest',
                'php artisan route:list --path=practice',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open API integration source pack',
                'route' => 'practice.source-pack',
                'parameters' => ['sourceKey' => 'laravel-api-integration-en-json'],
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/source-packs/laravel-api-integration-en-json',
                'payload' => [
                    'note' => 'No request body. The source key selects one JSON file.',
                ],
            ],
            'acceptance' => [
                'The pack references the exact JSON source path.',
                'The pack groups records by inferred technology.',
                'Each technology path links to a drill and native exercise.',
                'Unknown source keys return 404.',
            ],
            'starter_code' => <<<'PHP'
final class SourcePracticePackService
{
    public function build(string $sourceKey): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'source-practice-pack-index',
            'track' => 'laravel-http',
            'title' => 'Build a source practice pack index',
            'objective' => 'List JSON content files with record counts, inferred technologies, and links to source packs and sample workspaces.',
            'why' => 'This gives learners a file-first entry point: choose a content file, then practice its records in Laravel.',
            'steps' => [
                'Read all decoded JSON sources.',
                'Filter records by family, language, and search term.',
                'Count records and inferred technologies for each source.',
                'Link each source to its practice pack and sample record workspace.',
                'Render the index in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/SourcePracticePackIndexService.php',
                'app/Http/Controllers/Practice/SourcePracticePackIndexController.php',
                'app/Http/Controllers/Api/SourcePracticePackIndexController.php',
                'resources/views/practice/source-pack-index.blade.php',
                'tests/Feature/SourcePracticePackIndexTest.php',
            ],
            'commands' => [
                'php artisan test --filter SourcePracticePackIndexTest',
                'php artisan test --filter SourcePracticePackTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open source pack index',
                'route' => 'practice.source-packs.index',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/source-packs?family=laravel&language=en&search=api&limit=5',
                'payload' => [
                    'note' => 'No request body. Use filters to choose source files.',
                ],
            ],
            'acceptance' => [
                'Each item references a real JSON source path.',
                'Each item includes record counts and technology counts.',
                'Each item links to a source pack and sample workspace.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class SourcePracticePackIndexService
{
    public function build(array $filters = []): array
    {
        return [
            'items' => [],
            'meta' => ['filters' => $filters],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'content-implementation-blueprint',
            'track' => 'api-validation',
            'title' => 'Build an implementation blueprint from one content record',
            'objective' => 'Generate route, class, file, and test names from a JSON question record and its inferred technology.',
            'why' => 'This closes the gap between reading a question and starting implementation with concrete Laravel file names.',
            'steps' => [
                'Build a content drill from one record id.',
                'Normalize the content title into stable class and route names.',
                'Generate technology-specific request, controller, service, and test names.',
                'Render the blueprint in Blade and expose it through an API.',
                'Write tests for page output, API shape, and not-found behavior.',
            ],
            'files' => [
                'app/Services/Practice/ContentImplementationBlueprintService.php',
                'app/Http/Controllers/Practice/ContentImplementationBlueprintController.php',
                'app/Http/Controllers/Api/ContentImplementationBlueprintController.php',
                'resources/views/practice/implementation-blueprint.blade.php',
                'tests/Feature/ContentImplementationBlueprintTest.php',
            ],
            'commands' => [
                'php artisan test --filter ContentImplementationBlueprintTest',
                'php artisan route:list --path=practice',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open API implementation blueprint',
                'route' => 'practice.implementation-blueprint',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/implementation-blueprint?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology query filters.',
                ],
            ],
            'acceptance' => [
                'The blueprint references the exact source JSON record.',
                'The blueprint creates technology-specific route, class, file, and test names.',
                'Unknown record ids return 404.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class ContentImplementationBlueprintService
{
    public function build(array $filters): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'guided-implementation-checklist',
            'track' => 'testing-quality',
            'title' => 'Build a guided implementation checklist',
            'objective' => 'Turn a content implementation blueprint into a TDD checklist with progress payload for the practice progress API.',
            'why' => 'This makes the learning loop explicit: read the content record, write the test, implement the Laravel slice, and verify progress.',
            'steps' => [
                'Build an implementation blueprint from a JSON record.',
                'Create checklist steps for reading, testing, validation, routing, service logic, and verification.',
                'Generate a payload compatible with the progress-checklist API.',
                'Render the checklist in Blade and expose it through an API.',
                'Write tests for page output, API shape, and not-found behavior.',
            ],
            'files' => [
                'app/Services/Practice/GuidedImplementationChecklistService.php',
                'app/Http/Controllers/Practice/GuidedImplementationChecklistController.php',
                'app/Http/Controllers/Api/GuidedImplementationChecklistController.php',
                'resources/views/practice/guided-checklist.blade.php',
                'tests/Feature/GuidedImplementationChecklistTest.php',
            ],
            'commands' => [
                'php artisan test --filter GuidedImplementationChecklistTest',
                'php artisan test --filter PracticeProgressChecklistApiTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open guided checklist',
                'route' => 'practice.guided-checklist',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/guided-checklist?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology query filters.',
                ],
            ],
            'acceptance' => [
                'Checklist items come from a real content-backed blueprint.',
                'The payload can be posted to `/api/practice/progress-checklist`.',
                'The checklist names concrete files and commands.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class GuidedImplementationChecklistService
{
    public function build(array $filters): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'implementation-starter-kit',
            'track' => 'api-validation',
            'title' => 'Build an implementation starter kit',
            'objective' => 'Generate starter code snippets for the files named by a content-backed implementation blueprint.',
            'why' => 'This moves the learner from planning into coding: tests, requests, controllers, and services all start from the selected JSON record.',
            'steps' => [
                'Build a guided checklist from a content record.',
                'Read the generated route, class, file, and test names.',
                'Generate starter snippets for the focused test, Form Request, controller, and service.',
                'Render snippets in Blade and expose them through an API.',
                'Write tests for page output, API shape, and not-found behavior.',
            ],
            'files' => [
                'app/Services/Practice/ImplementationStarterKitService.php',
                'app/Http/Controllers/Practice/ImplementationStarterKitController.php',
                'app/Http/Controllers/Api/ImplementationStarterKitController.php',
                'resources/views/practice/starter-kit.blade.php',
                'tests/Feature/ImplementationStarterKitTest.php',
            ],
            'commands' => [
                'php artisan test --filter ImplementationStarterKitTest',
                'php artisan test --filter GuidedImplementationChecklistTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open implementation starter kit',
                'route' => 'practice.starter-kit',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/starter-kit?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology query filters.',
                ],
            ],
            'acceptance' => [
                'Starter snippets use the class and file names from the blueprint.',
                'The first snippet is a focused feature test.',
                'API validation snippets include Form Request, controller, and service skeletons.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class ImplementationStarterKitService
{
    public function build(array $filters): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'record-practice-workspace',
            'track' => 'api-validation',
            'title' => 'Build a record practice workspace',
            'objective' => 'Compose source, drill, blueprint, checklist, and starter snippets into one workspace for a single JSON record.',
            'why' => 'This is the full learning loop in one Laravel screen: content context, implementation names, steps, and starter code.',
            'steps' => [
                'Build a starter kit from a JSON record.',
                'Extract source, content, technology, practice exercise, blueprint, checklist, and snippets.',
                'Render the complete workspace in Blade.',
                'Expose the same workspace through an API.',
                'Write tests for page output, API shape, and not-found behavior.',
            ],
            'files' => [
                'app/Services/Practice/RecordPracticeWorkspaceService.php',
                'app/Http/Controllers/Practice/RecordPracticeWorkspaceController.php',
                'app/Http/Controllers/Api/RecordPracticeWorkspaceController.php',
                'resources/views/practice/record-workspace.blade.php',
                'tests/Feature/RecordPracticeWorkspaceTest.php',
            ],
            'commands' => [
                'php artisan test --filter RecordPracticeWorkspaceTest',
                'php artisan test --filter ImplementationStarterKitTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open record workspace',
                'route' => 'practice.record-workspace',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/record-workspace?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology query filters.',
                ],
            ],
            'acceptance' => [
                'The workspace references one exact JSON record.',
                'The workspace includes blueprint names, checklist items, and starter snippets.',
                'Unknown record ids return 404.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class RecordPracticeWorkspaceService
{
    public function build(array $filters): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'practice-queue',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed practice queue',
            'objective' => 'Turn filtered JSON question records into an ordered queue of record workspaces with progress payload.',
            'why' => 'This lets the learner practice several content-backed tasks in sequence while keeping each task tied to its source file and technology.',
            'steps' => [
                'Build question drill cards from content filters.',
                'Convert each card into a queued record workspace task.',
                'Estimate time by technology.',
                'Generate a progress payload for the whole queue.',
                'Render the queue in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeQueueService.php',
                'app/Http/Controllers/Practice/PracticeQueueController.php',
                'app/Http/Controllers/Api/PracticeQueueController.php',
                'resources/views/practice/queue.blade.php',
                'tests/Feature/PracticeQueueTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeQueueTest',
                'php artisan test --filter RecordPracticeWorkspaceTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open practice queue',
                'route' => 'practice.queue',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/queue?family=laravel&language=en&search=api&technology=api-validation&limit=2',
                'payload' => [
                    'note' => 'No request body. Use filters to build a queue.',
                ],
            ],
            'acceptance' => [
                'Each queue item links to one record workspace.',
                'The queue includes estimated minutes by technology.',
                'The queue exposes a progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeQueueService
{
    public function build(array $filters = []): array
    {
        return [
            'items' => [],
            'meta' => ['filters' => $filters],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'implementation-verification-plan',
            'track' => 'testing-quality',
            'title' => 'Build an implementation verification plan',
            'objective' => 'Generate focused tests, route checks, smoke request data, and quality-gate payload for one content-backed implementation.',
            'why' => 'This completes the practice loop: every JSON record can become code and a repeatable verification routine.',
            'steps' => [
                'Build a record workspace from one JSON record.',
                'Read the generated test class and route path.',
                'Generate focused test, route-list, full-suite, and Pint commands.',
                'Generate a smoke request and quality-gate payload.',
                'Render the plan in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/ImplementationVerificationPlanService.php',
                'app/Http/Controllers/Practice/ImplementationVerificationPlanController.php',
                'app/Http/Controllers/Api/ImplementationVerificationPlanController.php',
                'resources/views/practice/verification-plan.blade.php',
                'tests/Feature/ImplementationVerificationPlanTest.php',
            ],
            'commands' => [
                'php artisan test --filter ImplementationVerificationPlanTest',
                'php artisan test --filter RecordPracticeWorkspaceTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open verification plan',
                'route' => 'practice.verification-plan',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/verification-plan?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology query filters.',
                ],
            ],
            'acceptance' => [
                'The plan includes focused test, route-list, full-suite, and Pint commands.',
                'The plan includes an API smoke request.',
                'The plan includes a quality-gate payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class ImplementationVerificationPlanService
{
    public function build(array $filters): ?array
    {
        return null;
    }
}
PHP,
        ],
        [
            'slug' => 'content-practice-syllabus',
            'track' => 'laravel-http',
            'title' => 'Build a content-backed practice syllabus',
            'objective' => 'Generate a practice syllabus from JSON content coverage, technology phases, source packs, queues, and sample workspaces.',
            'why' => 'This creates a higher-level learning plan where every phase still links back to real content files and Laravel implementation work.',
            'steps' => [
                'Build a technology matrix from content records.',
                'Build a source pack index from the same filters.',
                'Create a phase for each inferred technology.',
                'Attach queue queries, exercises, and source packs to the syllabus.',
                'Render the syllabus in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/ContentPracticeSyllabusService.php',
                'app/Http/Controllers/Practice/ContentPracticeSyllabusController.php',
                'app/Http/Controllers/Api/ContentPracticeSyllabusController.php',
                'resources/views/practice/syllabus.blade.php',
                'tests/Feature/ContentPracticeSyllabusTest.php',
            ],
            'commands' => [
                'php artisan test --filter ContentPracticeSyllabusTest',
                'php artisan test --filter TechnologyPracticeMatrixTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open practice syllabus',
                'route' => 'practice.syllabus',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/syllabus?family=laravel&language=en&search=api&limit=5',
                'payload' => [
                    'note' => 'No request body. Use filters to build a syllabus.',
                ],
            ],
            'acceptance' => [
                'The syllabus includes technology phases.',
                'Each phase links to a queue and native exercise.',
                'The syllabus includes source packs from real JSON files.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class ContentPracticeSyllabusService
{
    public function build(array $filters = []): array
    {
        return [
            'phases' => [],
            'source_packs' => [],
            'meta' => ['filters' => $filters],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-sprint',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed practice sprint',
            'objective' => 'Turn a content-backed syllabus into a short sprint of concrete Laravel workspace tasks with verification links.',
            'why' => 'This keeps the learner in implementation mode: a syllabus becomes a small set of tasks that each open a record workspace and verification plan.',
            'steps' => [
                'Build a syllabus from content records and technologies.',
                'Take a limited number of technology phases.',
                'Build a small queue for each phase.',
                'Attach workspace and verification-plan queries to each task.',
                'Render the sprint in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeSprintService.php',
                'app/Http/Controllers/Practice/PracticeSprintController.php',
                'app/Http/Controllers/Api/PracticeSprintController.php',
                'resources/views/practice/sprint.blade.php',
                'tests/Feature/PracticeSprintTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeSprintTest',
                'php artisan test --filter ContentPracticeSyllabusTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open practice sprint',
                'route' => 'practice.sprint',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/sprint?family=laravel&language=en&search=api&phase_limit=1&tasks_per_phase=2',
                'payload' => [
                    'note' => 'No request body. Use filters to choose phases and tasks per phase.',
                ],
            ],
            'acceptance' => [
                'The sprint includes technology phases from the syllabus.',
                'Each phase includes concrete queue tasks.',
                'Each task links to a record workspace and verification plan.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeSprintService
{
    public function build(array $filters = []): array
    {
        return [
            'phases' => [],
            'meta' => ['filters' => $filters],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-tdd-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed TDD lab',
            'objective' => 'Turn one JSON content or question record into a Red-Green-Refactor lab with starter snippets and verification commands.',
            'why' => 'This makes a record actionable from first failing test to final quality gate while keeping Laravel as the place where practice happens.',
            'steps' => [
                'Build a starter kit from one content-backed record.',
                'Build a verification plan from the same record.',
                'Create Red, Green, and Refactor stages.',
                'Attach snippets, smoke request data, commands, and progress payload.',
                'Render the lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeTddLabService.php',
                'app/Http/Controllers/Practice/PracticeTddLabController.php',
                'app/Http/Controllers/Api/PracticeTddLabController.php',
                'resources/views/practice/tdd-lab.blade.php',
                'tests/Feature/PracticeTddLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeTddLabTest',
                'php artisan test --filter ImplementationStarterKitTest',
                'php artisan test --filter ImplementationVerificationPlanTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open TDD lab',
                'route' => 'practice.tdd-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/tdd-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to build a lab from one content record.',
                ],
            ],
            'acceptance' => [
                'The lab includes Red, Green, and Refactor stages.',
                'The Red stage includes the feature test snippet.',
                'The Green stage includes implementation snippets and smoke request data.',
                'The Refactor stage includes verification commands and quality-gate payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeTddLabService
{
    public function build(array $filters): ?array
    {
        return [
            'cycle' => [],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-review-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed review lab',
            'objective' => 'Turn one content-backed TDD lab into a review checklist for route, validation, controller, service, tests, and verification evidence.',
            'why' => 'This teaches learners to review their Laravel code after implementation, while keeping the review tied to the source content record and technology.',
            'steps' => [
                'Build a TDD lab for one content record.',
                'Extract generated files, route data, commands, and quality gate payload.',
                'Create layer-specific review items for Laravel code.',
                'Generate a progress payload for review completion.',
                'Render the review lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeReviewLabService.php',
                'app/Http/Controllers/Practice/PracticeReviewLabController.php',
                'app/Http/Controllers/Api/PracticeReviewLabController.php',
                'resources/views/practice/review-lab.blade.php',
                'tests/Feature/PracticeReviewLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeReviewLabTest',
                'php artisan test --filter PracticeTddLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open review lab',
                'route' => 'practice.review-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/review-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to review one implementation lab.',
                ],
            ],
            'acceptance' => [
                'The lab includes review items for route, validation, controller, service, tests, and verification.',
                'Each review item names the file or evidence to inspect.',
                'The lab includes verification commands and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeReviewLabService
{
    public function build(array $filters): ?array
    {
        return [
            'review_items' => [],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-remediation-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed remediation lab',
            'objective' => 'Turn one review lab into concrete fix tasks with files, actions, verification commands, and progress payload.',
            'why' => 'This completes the implementation loop: after reviewing a content-backed Laravel slice, the learner gets exact follow-up work to improve the code.',
            'steps' => [
                'Build a review lab for one content-backed implementation.',
                'Convert each review item into a remediation task.',
                'Attach the file to inspect, fix action, and verification command.',
                'Generate a progress payload and quality gate payload.',
                'Render the remediation lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeRemediationLabService.php',
                'app/Http/Controllers/Practice/PracticeRemediationLabController.php',
                'app/Http/Controllers/Api/PracticeRemediationLabController.php',
                'resources/views/practice/remediation-lab.blade.php',
                'tests/Feature/PracticeRemediationLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeRemediationLabTest',
                'php artisan test --filter PracticeReviewLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open remediation lab',
                'route' => 'practice.remediation-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/remediation-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to build fix tasks for one reviewed implementation.',
                ],
            ],
            'acceptance' => [
                'Each review item becomes a concrete fix task.',
                'Each task includes a file, fix action, and verification command.',
                'The lab includes progress payload and quality-gate payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeRemediationLabService
{
    public function build(array $filters): ?array
    {
        return [
            'tasks' => [],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-pull-request-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed pull request lab',
            'objective' => 'Turn a remediation lab into branch, commit, pull-request summary, changed files, verification evidence, and review checklist artifacts.',
            'why' => 'This teaches learners to finish a Laravel practice slice like real engineering work: scoped branch, clear commit, reviewable PR, and evidence from tests.',
            'steps' => [
                'Build a remediation lab for one content-backed implementation.',
                'Generate a practice branch name and commit message.',
                'Extract changed files and verification commands.',
                'Draft a pull request summary tied to the source record.',
                'Render the PR lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticePullRequestLabService.php',
                'app/Http/Controllers/Practice/PracticePullRequestLabController.php',
                'app/Http/Controllers/Api/PracticePullRequestLabController.php',
                'resources/views/practice/pull-request-lab.blade.php',
                'tests/Feature/PracticePullRequestLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticePullRequestLabTest',
                'php artisan test --filter PracticeRemediationLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open PR lab',
                'route' => 'practice.pull-request-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/pull-request-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to build PR artifacts for one implementation.',
                ],
            ],
            'acceptance' => [
                'The lab includes a branch name and commit message.',
                'The pull request draft includes summary, changed files, verification commands, and review checklist.',
                'The lab includes progress payload and quality-gate payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticePullRequestLabService
{
    public function build(array $filters): ?array
    {
        return [
            'branch' => 'practice/example',
            'pull_request' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-assessment-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed assessment lab',
            'objective' => 'Turn a pull request lab into a 100-point self-assessment rubric with evidence and progress payload.',
            'why' => 'This helps learners evaluate whether their Laravel implementation is traceable to the source content, layered correctly, tested, verified, and review-ready.',
            'steps' => [
                'Build a pull request lab for one content-backed implementation.',
                'Create rubric criteria for traceability, layers, tests, verification, and review readiness.',
                'Attach changed files, verification commands, review checklist, and quality gate payload as evidence.',
                'Generate a progress payload for assessment completion.',
                'Render the assessment lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeAssessmentLabService.php',
                'app/Http/Controllers/Practice/PracticeAssessmentLabController.php',
                'app/Http/Controllers/Api/PracticeAssessmentLabController.php',
                'resources/views/practice/assessment-lab.blade.php',
                'tests/Feature/PracticeAssessmentLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeAssessmentLabTest',
                'php artisan test --filter PracticePullRequestLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open assessment lab',
                'route' => 'practice.assessment-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/assessment-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to build a self-assessment rubric.',
                ],
            ],
            'acceptance' => [
                'The lab includes a 100-point rubric.',
                'The rubric is tied to source record, technology, changed files, verification, and review checklist evidence.',
                'The lab includes progress payload and quality-gate payload evidence.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeAssessmentLabService
{
    public function build(array $filters): ?array
    {
        return [
            'rubric' => [],
            'score_total' => 0,
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-retrospective-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed retrospective lab',
            'objective' => 'Turn one assessment lab into wins, weak spots, next actions, next lab links, and progress payload.',
            'why' => 'This closes the learning loop: after scoring the implementation, the learner chooses a concrete improvement and continues practicing from the same source record.',
            'steps' => [
                'Build an assessment lab for one content-backed implementation.',
                'Convert rubric criteria into retrospective prompts.',
                'Generate wins and weak spots tied to source, branch, evidence, and technology.',
                'Attach next lab links for remediation, assessment, and PR packaging.',
                'Render the retrospective lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeRetrospectiveLabService.php',
                'app/Http/Controllers/Practice/PracticeRetrospectiveLabController.php',
                'app/Http/Controllers/Api/PracticeRetrospectiveLabController.php',
                'resources/views/practice/retrospective-lab.blade.php',
                'tests/Feature/PracticeRetrospectiveLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeRetrospectiveLabTest',
                'php artisan test --filter PracticeAssessmentLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open retrospective lab',
                'route' => 'practice.retrospective-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/retrospective-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to build a retrospective from one assessment.',
                ],
            ],
            'acceptance' => [
                'The lab includes wins, weak spots, and next actions.',
                'Weak spots come from the assessment rubric.',
                'Next labs link back into remediation, assessment, and PR packaging.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeRetrospectiveLabService
{
    public function build(array $filters): ?array
    {
        return [
            'wins' => [],
            'weak_spots' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-portfolio-lab',
            'track' => 'testing-quality',
            'title' => 'Build a content-backed portfolio lab',
            'objective' => 'Turn one retrospective lab into a portfolio entry with source reference, practiced skills, evidence, writeup template, and next improvement.',
            'why' => 'This makes each Laravel practice slice reusable as a learning artifact, so the learner can explain what they built and what they practiced.',
            'steps' => [
                'Build a retrospective lab for one content-backed implementation.',
                'Create a portfolio headline and problem statement.',
                'Attach source reference, practiced skills, evidence, and next improvement.',
                'Generate a writeup template and progress payload.',
                'Render the portfolio lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticePortfolioLabService.php',
                'app/Http/Controllers/Practice/PracticePortfolioLabController.php',
                'app/Http/Controllers/Api/PracticePortfolioLabController.php',
                'resources/views/practice/portfolio-lab.blade.php',
                'tests/Feature/PracticePortfolioLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticePortfolioLabTest',
                'php artisan test --filter PracticeRetrospectiveLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open portfolio lab',
                'route' => 'practice.portfolio-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/portfolio-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation',
                'payload' => [
                    'note' => 'No request body. Use record_id and technology to build a portfolio artifact.',
                ],
            ],
            'acceptance' => [
                'The lab includes a portfolio entry tied to one source record.',
                'The entry lists practiced skills, evidence, and next improvement.',
                'The lab includes a writeup template and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticePortfolioLabService
{
    public function build(array $filters): ?array
    {
        return [
            'portfolio_entry' => [],
            'writeup_template' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-capstone-lab',
            'track' => 'laravel-http',
            'title' => 'Build a technology capstone lab',
            'objective' => 'Turn a technology board and practice queue into a mini-project with source coverage, tasks, deliverables, artifact links, and progress payload.',
            'why' => 'This moves practice from one record to a small technology-focused project while staying traceable to real content files and question records.',
            'steps' => [
                'Build a technology board for one inferred technology.',
                'Build a practice queue for the same filters.',
                'Turn queue records into capstone tasks and deliverables.',
                'Attach workspace, TDD, portfolio, and verification paths.',
                'Render the capstone lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeCapstoneLabService.php',
                'app/Http/Controllers/Practice/PracticeCapstoneLabController.php',
                'app/Http/Controllers/Api/PracticeCapstoneLabController.php',
                'resources/views/practice/capstone-lab.blade.php',
                'tests/Feature/PracticeCapstoneLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeCapstoneLabTest',
                'php artisan test --filter TechnologyPracticeBoardTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open capstone lab',
                'route' => 'practice.capstone-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/capstone-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2',
                'payload' => [
                    'note' => 'No request body. Use technology and content filters to build a capstone.',
                ],
            ],
            'acceptance' => [
                'The lab includes source coverage from real JSON files.',
                'The lab includes queued capstone tasks with workspace links.',
                'The lab includes deliverables, artifact queries, and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeCapstoneLabService
{
    public function build(array $filters = []): array
    {
        return [
            'tasks' => [],
            'deliverables' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-mentor-feedback-lab',
            'track' => 'testing-quality',
            'title' => 'Build a mentor feedback lab',
            'objective' => 'Turn a technology capstone into mentor-style feedback items, risks, questions, review focus, and action items.',
            'why' => 'This gives learners a guided review loop after a capstone, so each task gets feedback tied to its source record and Laravel implementation evidence.',
            'steps' => [
                'Build a capstone lab for one technology.',
                'Convert each capstone task into mentor feedback.',
                'Attach risks, action items, and workspace/TDD links.',
                'Generate mentor questions and review focus.',
                'Render the feedback lab in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeMentorFeedbackLabService.php',
                'app/Http/Controllers/Practice/PracticeMentorFeedbackLabController.php',
                'app/Http/Controllers/Api/PracticeMentorFeedbackLabController.php',
                'resources/views/practice/mentor-feedback-lab.blade.php',
                'tests/Feature/PracticeMentorFeedbackLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeMentorFeedbackLabTest',
                'php artisan test --filter PracticeCapstoneLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open mentor feedback lab',
                'route' => 'practice.mentor-feedback-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/mentor-feedback-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2',
                'payload' => [
                    'note' => 'No request body. Use technology and content filters to build mentor feedback.',
                ],
            ],
            'acceptance' => [
                'The lab includes feedback items for capstone tasks.',
                'Each feedback item links back to a workspace query.',
                'The lab includes mentor questions, review focus, risk, and action items.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeMentorFeedbackLabService
{
    public function build(array $filters = []): array
    {
        return [
            'feedback_items' => [],
            'mentor_questions' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-checkpoint-exam-lab',
            'track' => 'testing-quality',
            'title' => 'Build a technology checkpoint exam lab',
            'objective' => 'Turn mentor feedback into a timed checkpoint exam with warmup questions, coding tasks, oral review, pass criteria, and progress payload.',
            'why' => 'This lets learners test whether they can apply one technology from source records under a structured practice exam.',
            'steps' => [
                'Build mentor feedback for one technology capstone.',
                'Turn feedback tasks into coding exam prompts.',
                'Attach warmup questions, expected evidence, oral review, and pass criteria.',
                'Generate a progress payload for exam completion.',
                'Render the checkpoint exam in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeCheckpointExamLabService.php',
                'app/Http/Controllers/Practice/PracticeCheckpointExamLabController.php',
                'app/Http/Controllers/Api/PracticeCheckpointExamLabController.php',
                'resources/views/practice/checkpoint-exam-lab.blade.php',
                'tests/Feature/PracticeCheckpointExamLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeCheckpointExamLabTest',
                'php artisan test --filter PracticeMentorFeedbackLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open checkpoint exam lab',
                'route' => 'practice.checkpoint-exam-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/checkpoint-exam-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2',
                'payload' => [
                    'note' => 'No request body. Use technology and content filters to build a checkpoint exam.',
                ],
            ],
            'acceptance' => [
                'The exam includes warmup questions, coding tasks, oral review, and pass criteria.',
                'Coding tasks link back to source-backed workspaces.',
                'The exam duration is based on capstone estimated minutes.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeCheckpointExamLabService
{
    public function build(array $filters = []): array
    {
        return [
            'warmup_questions' => [],
            'coding_tasks' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-mastery-path-lab',
            'track' => 'laravel-http',
            'title' => 'Build a content-backed mastery path lab',
            'objective' => 'Turn a syllabus into a multi-technology path with capstone, checkpoint, mentor feedback, portfolio evidence, and progress payload for each milestone.',
            'why' => 'This turns the Laravel hub into a roadmap for repeated practice across technologies while keeping every milestone tied to real source records.',
            'steps' => [
                'Build a content-backed syllabus from filtered records.',
                'Turn technology phases into mastery milestones.',
                'Attach capstone, checkpoint, and mentor feedback queries to each milestone.',
                'Generate milestone done criteria and a progress payload.',
                'Render the mastery path in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeMasteryPathLabService.php',
                'app/Http/Controllers/Practice/PracticeMasteryPathLabController.php',
                'app/Http/Controllers/Api/PracticeMasteryPathLabController.php',
                'resources/views/practice/mastery-path-lab.blade.php',
                'tests/Feature/PracticeMasteryPathLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeMasteryPathLabTest',
                'php artisan test --filter ContentPracticeSyllabusTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open mastery path lab',
                'route' => 'practice.mastery-path-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/mastery-path-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2',
                'payload' => [
                    'note' => 'No request body. Use content filters to build a multi-technology path.',
                ],
            ],
            'acceptance' => [
                'The path includes milestones from syllabus technologies.',
                'Each milestone links to capstone, checkpoint exam, and mentor feedback labs.',
                'The path includes source packs and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeMasteryPathLabService
{
    public function build(array $filters = []): array
    {
        return [
            'milestones' => [],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-rotation-lab',
            'track' => 'laravel-http',
            'title' => 'Build a content-backed practice rotation lab',
            'objective' => 'Turn a mastery path into a day-by-day rotation with technology focus, lab links, required outputs, and progress payload.',
            'why' => 'This makes practice repeatable across days instead of one-off, while each day remains tied to source-backed technology milestones.',
            'steps' => [
                'Build a mastery path from content-backed syllabus phases.',
                'Repeat milestones across a configurable number of days.',
                'Attach capstone, checkpoint, and mentor feedback links to each day.',
                'Generate the output expected for each day.',
                'Render the rotation in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeRotationLabService.php',
                'app/Http/Controllers/Practice/PracticeRotationLabController.php',
                'app/Http/Controllers/Api/PracticeRotationLabController.php',
                'resources/views/practice/rotation-lab.blade.php',
                'tests/Feature/PracticeRotationLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeRotationLabTest',
                'php artisan test --filter PracticeMasteryPathLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open rotation lab',
                'route' => 'practice.rotation-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/rotation-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use filters and days to build a repeatable rotation.',
                ],
            ],
            'acceptance' => [
                'The rotation includes one schedule item per day.',
                'Each day links to capstone, checkpoint, and mentor feedback labs.',
                'Each day has a required output and progress item.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeRotationLabService
{
    public function build(array $filters = []): array
    {
        return [
            'schedule' => [],
            'progress_payload' => ['items' => []],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-weekly-report-lab',
            'track' => 'testing-quality',
            'title' => 'Build a weekly practice report lab',
            'objective' => 'Turn a practice rotation into a weekly progress report with daily outputs, evidence checklist, blockers, next week plan, and progress payload.',
            'why' => 'This makes the Laravel hub useful for repeated practice tracking, not just single-session exercises.',
            'steps' => [
                'Build a rotation from a mastery path.',
                'Summarize daily outputs and technology coverage.',
                'Generate evidence checklist and blocker prompts.',
                'Generate next week plan from practiced technologies.',
                'Render the report in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeWeeklyReportLabService.php',
                'app/Http/Controllers/Practice/PracticeWeeklyReportLabController.php',
                'app/Http/Controllers/Api/PracticeWeeklyReportLabController.php',
                'resources/views/practice/weekly-report-lab.blade.php',
                'tests/Feature/PracticeWeeklyReportLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeWeeklyReportLabTest',
                'php artisan test --filter PracticeRotationLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open weekly report lab',
                'route' => 'practice.weekly-report-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/weekly-report-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use rotation filters to build a report.',
                ],
            ],
            'acceptance' => [
                'The report includes daily outputs and technology coverage.',
                'The report includes evidence checklist, blockers, and next week plan.',
                'The report includes progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeWeeklyReportLabService
{
    public function build(array $filters = []): array
    {
        return [
            'daily_outputs' => [],
            'evidence_checklist' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-demo-script-lab',
            'track' => 'testing-quality',
            'title' => 'Build a practice demo script lab',
            'objective' => 'Turn a weekly practice report into a demo script with speaker notes, demo actions, verification steps, evidence, and handoff payload.',
            'why' => 'This helps learners explain what they built, prove it with commands, and connect source-backed content to real Laravel code.',
            'steps' => [
                'Build a weekly report from a practice rotation.',
                'Convert each daily output into a demo script segment.',
                'Attach speaker notes, code actions, verification commands, and evidence.',
                'Generate rehearsal checklist and handoff payload.',
                'Render the script in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeDemoScriptLabService.php',
                'app/Http/Controllers/Practice/PracticeDemoScriptLabController.php',
                'app/Http/Controllers/Api/PracticeDemoScriptLabController.php',
                'resources/views/practice/demo-script-lab.blade.php',
                'tests/Feature/PracticeDemoScriptLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeDemoScriptLabTest',
                'php artisan test --filter PracticeWeeklyReportLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open demo script lab',
                'route' => 'practice.demo-script-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/demo-script-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use weekly report filters to build a demo script.',
                ],
            ],
            'acceptance' => [
                'The script includes opening lines and demo steps.',
                'Each demo step includes say, do, verify, and evidence fields.',
                'The script includes rehearsal checklist and handoff payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeDemoScriptLabService
{
    public function build(array $filters = []): array
    {
        return [
            'script_steps' => [],
            'rehearsal_checklist' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-live-coding-lab',
            'track' => 'testing-quality',
            'title' => 'Build a live coding practice lab',
            'objective' => 'Turn a demo script into a timed live coding session with coding prompts, narration prompts, verification commands, scorecard, and recovery notes.',
            'why' => 'This helps learners practice implementation under a realistic timebox while still staying tied to source-backed Laravel content and evidence.',
            'steps' => [
                'Build a demo script from a weekly report.',
                'Convert each demo step into a timed live coding round.',
                'Attach coding prompt, narration prompt, verification command, and pass signal.',
                'Generate scorecard and failure recovery notes.',
                'Render the session in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeLiveCodingLabService.php',
                'app/Http/Controllers/Practice/PracticeLiveCodingLabController.php',
                'app/Http/Controllers/Api/PracticeLiveCodingLabController.php',
                'resources/views/practice/live-coding-lab.blade.php',
                'tests/Feature/PracticeLiveCodingLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeLiveCodingLabTest',
                'php artisan test --filter PracticeDemoScriptLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open live coding lab',
                'route' => 'practice.live-coding-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/live-coding-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use demo script filters to build a timed live coding session.',
                ],
            ],
            'acceptance' => [
                'The session includes live coding rounds with timeboxes.',
                'Each round includes code, narration, verification, pass signal, and evidence.',
                'The session includes scorecard, failure recovery, and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeLiveCodingLabService
{
    public function build(array $filters = []): array
    {
        return [
            'rounds' => [],
            'scorecard' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-bug-fix-lab',
            'track' => 'testing-quality',
            'title' => 'Build a bug-fix practice lab',
            'objective' => 'Turn live coding rounds into bug-fix drills with bug reports, diagnosis steps, patch targets, verification commands, pass signals, and evidence.',
            'why' => 'This trains learners to debug Laravel by layer instead of guessing, while keeping every failure tied to content-backed technologies and tests.',
            'steps' => [
                'Build a live coding session from a demo script.',
                'Convert each live coding round into a realistic bug report.',
                'Attach diagnosis steps, patch target, verification command, and evidence.',
                'Generate review questions for the fixed bug.',
                'Render the drills in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeBugFixLabService.php',
                'app/Http/Controllers/Practice/PracticeBugFixLabController.php',
                'app/Http/Controllers/Api/PracticeBugFixLabController.php',
                'resources/views/practice/bug-fix-lab.blade.php',
                'tests/Feature/PracticeBugFixLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeBugFixLabTest',
                'php artisan test --filter PracticeLiveCodingLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open bug-fix lab',
                'route' => 'practice.bug-fix-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/bug-fix-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use live coding filters to build bug-fix drills.',
                ],
            ],
            'acceptance' => [
                'The lab includes debugging rules and bug drills.',
                'Each drill includes diagnosis steps, patch target, verification command, pass signal, and evidence.',
                'The lab includes review questions and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeBugFixLabService
{
    public function build(array $filters = []): array
    {
        return [
            'bug_drills' => [],
            'review_questions' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-refactor-lab',
            'track' => 'testing-quality',
            'title' => 'Build a refactor practice lab',
            'objective' => 'Turn bug-fix drills into safe refactor tasks with goals, file targets, guardrails, verification commands, architecture checks, and progress payload.',
            'why' => 'This teaches learners to improve Laravel code after tests are green without breaking route names, API shapes, Blade output, or service boundaries.',
            'steps' => [
                'Build bug-fix drills from live coding rounds.',
                'Convert each fixed bug into a safe refactor task.',
                'Attach target file, safe steps, guardrails, and verification command.',
                'Generate architecture checks for controllers, services, Blade, and tests.',
                'Render the tasks in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeRefactorLabService.php',
                'app/Http/Controllers/Practice/PracticeRefactorLabController.php',
                'app/Http/Controllers/Api/PracticeRefactorLabController.php',
                'resources/views/practice/refactor-lab.blade.php',
                'tests/Feature/PracticeRefactorLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeRefactorLabTest',
                'php artisan test --filter PracticeBugFixLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open refactor lab',
                'route' => 'practice.refactor-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/refactor-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use bug-fix filters to build refactor tasks.',
                ],
            ],
            'acceptance' => [
                'The lab includes refactor rules and tasks.',
                'Each task includes target file, safe steps, guardrails, command, and evidence.',
                'The lab includes architecture checks and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeRefactorLabService
{
    public function build(array $filters = []): array
    {
        return [
            'refactor_tasks' => [],
            'architecture_checks' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-release-readiness-lab',
            'track' => 'testing-quality',
            'title' => 'Build a release readiness practice lab',
            'objective' => 'Turn refactor tasks into release readiness artifacts with release notes, smoke checks, rollback notes, verification evidence, and handoff checklist.',
            'why' => 'This teaches learners to finish the delivery loop after coding: prove the change, document risk, and hand off a Laravel practice slice cleanly.',
            'steps' => [
                'Build refactor tasks from bug-fix drills.',
                'Convert each refactor task into a release item.',
                'Attach release note, smoke check, rollback note, verification command, and evidence.',
                'Generate a handoff checklist for the practice slice.',
                'Render the release readiness page and expose the same data through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeReleaseReadinessLabService.php',
                'app/Http/Controllers/Practice/PracticeReleaseReadinessLabController.php',
                'app/Http/Controllers/Api/PracticeReleaseReadinessLabController.php',
                'resources/views/practice/release-readiness-lab.blade.php',
                'tests/Feature/PracticeReleaseReadinessLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeReleaseReadinessLabTest',
                'php artisan test --filter PracticeRefactorLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open release readiness lab',
                'route' => 'practice.release-readiness-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/release-readiness-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use refactor filters to build release readiness artifacts.',
                ],
            ],
            'acceptance' => [
                'The lab includes release rules and release items.',
                'Each release item includes release note, smoke check, rollback note, command, and evidence.',
                'The lab includes handoff checklist and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeReleaseReadinessLabService
{
    public function build(array $filters = []): array
    {
        return [
            'release_items' => [],
            'handoff_checklist' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-interview-defense-lab',
            'track' => 'testing-quality',
            'title' => 'Build an interview defense practice lab',
            'objective' => 'Turn release readiness evidence into technical interview defense cards with questions, answer outlines, evidence, follow-up risks, and scoring rubric.',
            'why' => 'This helps learners explain Laravel code decisions using concrete evidence from content-backed tasks, tests, smoke checks, and rollback notes.',
            'steps' => [
                'Build release readiness artifacts from refactor tasks.',
                'Convert each release item into one interview defense card.',
                'Attach a technical question, answer outline, evidence to cite, and follow-up risk.',
                'Generate scoring rubric for concise technical explanation.',
                'Render the defense cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeInterviewDefenseLabService.php',
                'app/Http/Controllers/Practice/PracticeInterviewDefenseLabController.php',
                'app/Http/Controllers/Api/PracticeInterviewDefenseLabController.php',
                'resources/views/practice/interview-defense-lab.blade.php',
                'tests/Feature/PracticeInterviewDefenseLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeInterviewDefenseLabTest',
                'php artisan test --filter PracticeReleaseReadinessLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open interview defense lab',
                'route' => 'practice.interview-defense-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/interview-defense-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use release readiness filters to build interview defense cards.',
                ],
            ],
            'acceptance' => [
                'The lab includes defense rules and defense cards.',
                'Each card includes question, answer outline, evidence to cite, and follow-up risk.',
                'The lab includes scoring rubric and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeInterviewDefenseLabService
{
    public function build(array $filters = []): array
    {
        return [
            'defense_cards' => [],
            'scoring_rubric' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-knowledge-gap-lab',
            'track' => 'testing-quality',
            'title' => 'Build a knowledge gap practice lab',
            'objective' => 'Turn interview defense cards into knowledge-gap cards with practice actions, review prompts, evidence to recheck, verification hints, and next-session plan.',
            'why' => 'This keeps weak interview answers connected to source-backed Laravel coding practice instead of becoming passive study notes.',
            'steps' => [
                'Build interview defense cards from release evidence.',
                'Convert each weak answer into a knowledge-gap card.',
                'Attach a coding action, review prompt, evidence to recheck, and verification hint.',
                'Generate a next-session plan from the gaps.',
                'Render the gap cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeKnowledgeGapLabService.php',
                'app/Http/Controllers/Practice/PracticeKnowledgeGapLabController.php',
                'app/Http/Controllers/Api/PracticeKnowledgeGapLabController.php',
                'resources/views/practice/knowledge-gap-lab.blade.php',
                'tests/Feature/PracticeKnowledgeGapLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeKnowledgeGapLabTest',
                'php artisan test --filter PracticeInterviewDefenseLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open knowledge gap lab',
                'route' => 'practice.knowledge-gap-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/knowledge-gap-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use interview defense filters to build knowledge-gap cards.',
                ],
            ],
            'acceptance' => [
                'The lab includes gap rules and gap cards.',
                'Each card includes gap, practice action, review prompt, evidence, and verification hint.',
                'The lab includes next-session plan and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeKnowledgeGapLabService
{
    public function build(array $filters = []): array
    {
        return [
            'gap_cards' => [],
            'next_session_plan' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-spaced-repetition-lab',
            'track' => 'testing-quality',
            'title' => 'Build a spaced repetition practice lab',
            'objective' => 'Turn knowledge-gap cards into a day 1, day 3, and day 7 coding review schedule with recall prompts, verification hints, promotion criteria, and progress payload.',
            'why' => 'This makes repeated study practical by requiring learners to re-code weak concepts and verify them again instead of only rereading notes.',
            'steps' => [
                'Build knowledge-gap cards from interview defenses.',
                'Expand each gap into day 1, day 3, and day 7 review checkpoints.',
                'Attach coding action, recall prompt, and verification hint to each checkpoint.',
                'Generate promotion criteria for moving a concept out of review.',
                'Render the schedule in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeSpacedRepetitionLabService.php',
                'app/Http/Controllers/Practice/PracticeSpacedRepetitionLabController.php',
                'app/Http/Controllers/Api/PracticeSpacedRepetitionLabController.php',
                'resources/views/practice/spaced-repetition-lab.blade.php',
                'tests/Feature/PracticeSpacedRepetitionLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeSpacedRepetitionLabTest',
                'php artisan test --filter PracticeKnowledgeGapLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open spaced repetition lab',
                'route' => 'practice.spaced-repetition-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/spaced-repetition-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use knowledge-gap filters to build review checkpoints.',
                ],
            ],
            'acceptance' => [
                'The lab includes review rules and a review schedule.',
                'Each checkpoint includes day, coding action, recall prompt, and verification hint.',
                'The lab includes promotion criteria and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeSpacedRepetitionLabService
{
    public function build(array $filters = []): array
    {
        return [
            'review_schedule' => [],
            'promotion_criteria' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-mastery-evidence-lab',
            'track' => 'testing-quality',
            'title' => 'Build a mastery evidence practice lab',
            'objective' => 'Turn spaced repetition checkpoints into mastery evidence cards with scores, proof items, missing evidence, next harder labs, and progress payload.',
            'why' => 'This lets learners decide whether a technology is ready for harder Laravel practice based on repeated implementation evidence, not confidence alone.',
            'steps' => [
                'Build spaced repetition checkpoints from knowledge gaps.',
                'Group repeated checkpoints by technology segment.',
                'Calculate evidence score and readiness status.',
                'Identify missing evidence and next harder labs.',
                'Render the evidence cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeMasteryEvidenceLabService.php',
                'app/Http/Controllers/Practice/PracticeMasteryEvidenceLabController.php',
                'app/Http/Controllers/Api/PracticeMasteryEvidenceLabController.php',
                'resources/views/practice/mastery-evidence-lab.blade.php',
                'tests/Feature/PracticeMasteryEvidenceLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeMasteryEvidenceLabTest',
                'php artisan test --filter PracticeSpacedRepetitionLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open mastery evidence lab',
                'route' => 'practice.mastery-evidence-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/mastery-evidence-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use spaced repetition filters to build mastery evidence.',
                ],
            ],
            'acceptance' => [
                'The lab includes mastery rules and evidence cards.',
                'Each card includes review days, evidence score, status, proof items, and missing evidence.',
                'The lab includes next harder labs and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeMasteryEvidenceLabService
{
    public function build(array $filters = []): array
    {
        return [
            'evidence_cards' => [],
            'next_harder_labs' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-competency-map-lab',
            'track' => 'testing-quality',
            'title' => 'Build a competency map practice lab',
            'objective' => 'Turn mastery evidence into competency cards with implementation, verification, explanation, release-readiness levels, proof summary, next action, and progress payload.',
            'why' => 'This helps learners see which Laravel capabilities are actually ready for harder work based on source-backed evidence, not only completed pages.',
            'steps' => [
                'Build mastery evidence from repeated checkpoints.',
                'Convert each evidence card into competency levels.',
                'Summarize proof items and readiness by technology segment.',
                'Choose the next action from the evidence score.',
                'Render the competency map in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeCompetencyMapLabService.php',
                'app/Http/Controllers/Practice/PracticeCompetencyMapLabController.php',
                'app/Http/Controllers/Api/PracticeCompetencyMapLabController.php',
                'resources/views/practice/competency-map-lab.blade.php',
                'tests/Feature/PracticeCompetencyMapLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeCompetencyMapLabTest',
                'php artisan test --filter PracticeMasteryEvidenceLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open competency map lab',
                'route' => 'practice.competency-map-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/competency-map-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use mastery evidence filters to build competency maps.',
                ],
            ],
            'acceptance' => [
                'The lab includes competency rules and competency cards.',
                'Each card includes competency levels, proof summary, readiness, and next action.',
                'The lab includes map summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeCompetencyMapLabService
{
    public function build(array $filters = []): array
    {
        return [
            'competencies' => [],
            'map_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-next-challenge-lab',
            'track' => 'testing-quality',
            'title' => 'Build a next challenge practice lab',
            'objective' => 'Turn competency maps into challenge cards with challenge type, recommended route, verification command, evidence to submit, and progress payload.',
            'why' => 'This turns capability evidence into the next concrete Laravel practice route so learners keep moving from content to code.',
            'steps' => [
                'Build competency maps from mastery evidence.',
                'Choose a harder lab or reinforcement based on readiness.',
                'Attach recommended route, verification command, and evidence requirements.',
                'Summarize harder-lab and reinforcement counts.',
                'Render the challenge cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeNextChallengeLabService.php',
                'app/Http/Controllers/Practice/PracticeNextChallengeLabController.php',
                'app/Http/Controllers/Api/PracticeNextChallengeLabController.php',
                'resources/views/practice/next-challenge-lab.blade.php',
                'tests/Feature/PracticeNextChallengeLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeNextChallengeLabTest',
                'php artisan test --filter PracticeCompetencyMapLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open next challenge lab',
                'route' => 'practice.next-challenge-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/next-challenge-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use competency map filters to build next challenges.',
                ],
            ],
            'acceptance' => [
                'The lab includes challenge rules and challenge cards.',
                'Each card includes challenge type, route, command, reason, and evidence.',
                'The lab includes challenge summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeNextChallengeLabService
{
    public function build(array $filters = []): array
    {
        return [
            'challenge_cards' => [],
            'challenge_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-challenge-execution-lab',
            'track' => 'testing-quality',
            'title' => 'Build a challenge execution practice lab',
            'objective' => 'Turn next challenge cards into executable practice steps with route names, execution order, verification commands, evidence, exit criteria, and session summary.',
            'why' => 'This turns recommendations into a concrete coding session so learners know exactly what to open, what to run, and what evidence proves done.',
            'steps' => [
                'Build next challenge cards from competency maps.',
                'Convert each challenge into an ordered execution step.',
                'Attach route name, verification command, evidence, and exit criteria.',
                'Summarize estimated time and challenge mix.',
                'Render the execution plan in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeChallengeExecutionLabService.php',
                'app/Http/Controllers/Practice/PracticeChallengeExecutionLabController.php',
                'app/Http/Controllers/Api/PracticeChallengeExecutionLabController.php',
                'resources/views/practice/challenge-execution-lab.blade.php',
                'tests/Feature/PracticeChallengeExecutionLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeChallengeExecutionLabTest',
                'php artisan test --filter PracticeNextChallengeLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open challenge execution lab',
                'route' => 'practice.challenge-execution-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/challenge-execution-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use next challenge filters to build execution steps.',
                ],
            ],
            'acceptance' => [
                'The lab includes execution rules and execution steps.',
                'Each step includes route, order, command, evidence, and exit criteria.',
                'The lab includes session summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeChallengeExecutionLabService
{
    public function build(array $filters = []): array
    {
        return [
            'execution_steps' => [],
            'session_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-challenge-evidence-review-lab',
            'track' => 'testing-quality',
            'title' => 'Build a challenge evidence review lab',
            'objective' => 'Turn executable challenge steps into review cards with required evidence, review questions, pass signals, risk checks, follow-up actions, and progress payload.',
            'why' => 'This makes learners prove that a challenge was actually completed with runnable verification and specific Laravel evidence, instead of only reading a plan.',
            'steps' => [
                'Build execution steps from next challenge cards.',
                'Convert each step into an evidence review card.',
                'Attach required evidence, review questions, pass signals, risk checks, and follow-up action.',
                'Summarize review card count, required evidence count, and commands to rerun.',
                'Render the review board in Blade and expose it through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeChallengeEvidenceReviewLabService.php',
                'app/Http/Controllers/Practice/PracticeChallengeEvidenceReviewLabController.php',
                'app/Http/Controllers/Api/PracticeChallengeEvidenceReviewLabController.php',
                'resources/views/practice/challenge-evidence-review-lab.blade.php',
                'tests/Feature/PracticeChallengeEvidenceReviewLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeChallengeEvidenceReviewLabTest',
                'php artisan test --filter PracticeChallengeExecutionLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open challenge evidence review lab',
                'route' => 'practice.challenge-evidence-review-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/challenge-evidence-review-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use challenge execution filters to build evidence review cards.',
                ],
            ],
            'acceptance' => [
                'The lab includes review rules and evidence review cards.',
                'Each card includes route, command, required evidence, review questions, pass signals, risk checks, and follow-up action.',
                'The lab includes review summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeChallengeEvidenceReviewLabService
{
    public function build(array $filters = []): array
    {
        return [
            'evidence_cards' => [],
            'review_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-challenge-promotion-lab',
            'track' => 'testing-quality',
            'title' => 'Build a challenge promotion practice lab',
            'objective' => 'Turn evidence review cards into promote-or-repeat decisions with proof checklist, repeat triggers, next routes, learner note prompts, and progress payload.',
            'why' => 'This closes the loop after a practice challenge by deciding whether the learner should move forward or repeat with stronger evidence.',
            'steps' => [
                'Build evidence review cards from executable challenge steps.',
                'Convert each review card into a promotion decision.',
                'Attach promotion proof, repeat triggers, next route, and learner note prompt.',
                'Summarize promoted count, repeat count, and unique next routes.',
                'Render promotion decisions in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeChallengePromotionLabService.php',
                'app/Http/Controllers/Practice/PracticeChallengePromotionLabController.php',
                'app/Http/Controllers/Api/PracticeChallengePromotionLabController.php',
                'resources/views/practice/challenge-promotion-lab.blade.php',
                'tests/Feature/PracticeChallengePromotionLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeChallengePromotionLabTest',
                'php artisan test --filter PracticeChallengeEvidenceReviewLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open challenge promotion lab',
                'route' => 'practice.challenge-promotion-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/challenge-promotion-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use evidence review filters to build promotion decisions.',
                ],
            ],
            'acceptance' => [
                'The lab includes promotion rules and decisions.',
                'Each decision includes source route, next route, command, promotion proof, repeat triggers, and learner note prompt.',
                'The lab includes promotion summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeChallengePromotionLabService
{
    public function build(array $filters = []): array
    {
        return [
            'promotion_decisions' => [],
            'promotion_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-next-session-handoff-lab',
            'track' => 'testing-quality',
            'title' => 'Build a next session handoff practice lab',
            'objective' => 'Turn challenge promotion decisions into next-session handoff cards with route, preflight checklist, coding focus, done evidence, note prompt, and progress payload.',
            'why' => 'This makes each finished practice session restartable by giving the learner a concrete first action for the next coding session.',
            'steps' => [
                'Build promotion decisions from evidence review cards.',
                'Convert each decision into a next-session handoff card.',
                'Attach open route, carry-over route, preflight checklist, coding focus, and done evidence.',
                'Summarize handoff count, promotion handoffs, repeat handoffs, and routes to open.',
                'Render handoff cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeNextSessionHandoffLabService.php',
                'app/Http/Controllers/Practice/PracticeNextSessionHandoffLabController.php',
                'app/Http/Controllers/Api/PracticeNextSessionHandoffLabController.php',
                'resources/views/practice/next-session-handoff-lab.blade.php',
                'tests/Feature/PracticeNextSessionHandoffLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeNextSessionHandoffLabTest',
                'php artisan test --filter PracticeChallengePromotionLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open next session handoff lab',
                'route' => 'practice.next-session-handoff-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/next-session-handoff-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use promotion filters to build next-session handoff cards.',
                ],
            ],
            'acceptance' => [
                'The lab includes handoff rules and next-session handoff cards.',
                'Each card includes open route, carry-over route, verification command, session goal, preflight checklist, coding focus, done evidence, and note prompt.',
                'The lab includes handoff summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeNextSessionHandoffLabService
{
    public function build(array $filters = []): array
    {
        return [
            'handoff_cards' => [],
            'handoff_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-session-replay-lab',
            'track' => 'testing-quality',
            'title' => 'Build a session replay practice lab',
            'objective' => 'Turn next-session handoff cards into replay rounds with before-check, coding run, after-check, evidence capture, retry policy, and progress payload.',
            'why' => 'This makes the learner rerun a real Laravel session with verification before and after coding, so practice stays evidence-driven.',
            'steps' => [
                'Build next-session handoff cards from promotion decisions.',
                'Convert each handoff into a replay round.',
                'Attach before-check, coding run, after-check, evidence capture, and retry policy.',
                'Summarize replay rounds, promotion rounds, repeat rounds, and commands to run.',
                'Render replay rounds in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeSessionReplayLabService.php',
                'app/Http/Controllers/Practice/PracticeSessionReplayLabController.php',
                'app/Http/Controllers/Api/PracticeSessionReplayLabController.php',
                'resources/views/practice/session-replay-lab.blade.php',
                'tests/Feature/PracticeSessionReplayLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeSessionReplayLabTest',
                'php artisan test --filter PracticeNextSessionHandoffLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open session replay lab',
                'route' => 'practice.session-replay-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/session-replay-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use handoff filters to build replay rounds.',
                ],
            ],
            'acceptance' => [
                'The lab includes replay rules and replay rounds.',
                'Each round includes route, before-check, coding run, after-check, evidence capture, and retry policy.',
                'The lab includes replay summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeSessionReplayLabService
{
    public function build(array $filters = []): array
    {
        return [
            'replay_rounds' => [],
            'replay_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-session-debrief-lab',
            'track' => 'testing-quality',
            'title' => 'Build a session debrief practice lab',
            'objective' => 'Turn session replay rounds into debrief cards with result notes, lesson prompts, blocker checks, next actions, and progress payload.',
            'why' => 'This makes each coding session teachable by forcing command output, route output, blockers, and next action to be written down.',
            'steps' => [
                'Build replay rounds from next-session handoff cards.',
                'Convert each replay round into a debrief card.',
                'Attach result notes, lesson prompts, blocker checks, and next action.',
                'Summarize debrief cards, ready-to-archive count, retry count, and commands reviewed.',
                'Render debrief cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeSessionDebriefLabService.php',
                'app/Http/Controllers/Practice/PracticeSessionDebriefLabController.php',
                'app/Http/Controllers/Api/PracticeSessionDebriefLabController.php',
                'resources/views/practice/session-debrief-lab.blade.php',
                'tests/Feature/PracticeSessionDebriefLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeSessionDebriefLabTest',
                'php artisan test --filter PracticeSessionReplayLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open session debrief lab',
                'route' => 'practice.session-debrief-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/session-debrief-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use replay filters to build debrief cards.',
                ],
            ],
            'acceptance' => [
                'The lab includes debrief rules and debrief cards.',
                'Each card includes route, command, status, result notes, lesson prompts, blocker checks, and next action.',
                'The lab includes debrief summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeSessionDebriefLabService
{
    public function build(array $filters = []): array
    {
        return [
            'debrief_cards' => [],
            'debrief_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-session-archive-lab',
            'track' => 'testing-quality',
            'title' => 'Build a session archive practice lab',
            'objective' => 'Turn session debrief cards into archive entries with proof bundle, learning summary, blocker status, retrieval tags, next reference, and progress payload.',
            'why' => 'This keeps practice evidence reusable for portfolio writing, interview defense, and future review sessions.',
            'steps' => [
                'Build debrief cards from replay rounds.',
                'Convert each debrief card into an archive entry.',
                'Attach proof bundle, learning summary, blocker status, retrieval tags, and next reference.',
                'Summarize archive entries, archived count, retry count, and archived routes.',
                'Render archive entries in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeSessionArchiveLabService.php',
                'app/Http/Controllers/Practice/PracticeSessionArchiveLabController.php',
                'app/Http/Controllers/Api/PracticeSessionArchiveLabController.php',
                'resources/views/practice/session-archive-lab.blade.php',
                'tests/Feature/PracticeSessionArchiveLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeSessionArchiveLabTest',
                'php artisan test --filter PracticeSessionDebriefLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open session archive lab',
                'route' => 'practice.session-archive-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/session-archive-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use debrief filters to build archive entries.',
                ],
            ],
            'acceptance' => [
                'The lab includes archive rules and archive entries.',
                'Each entry includes route, command, archive status, proof bundle, learning summary, blocker status, retrieval tags, and next reference.',
                'The lab includes archive summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeSessionArchiveLabService
{
    public function build(array $filters = []): array
    {
        return [
            'archive_entries' => [],
            'archive_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-archive-retrieval-lab',
            'track' => 'testing-quality',
            'title' => 'Build an archive retrieval practice lab',
            'objective' => 'Turn archived practice session entries into retrieval cards with search keys, retrieval prompts, reuse targets, proof quotes, refresh checks, and progress payload.',
            'why' => 'This makes saved Laravel practice evidence useful again when writing a portfolio note, preparing an interview answer, or reviewing a technology.',
            'steps' => [
                'Build archive entries from debrief cards.',
                'Convert each archive entry into a retrieval card.',
                'Attach search keys, retrieval prompt, reuse targets, proof to quote, and refresh check.',
                'Summarize retrieval cards, portfolio-ready cards, refresh-required cards, and unique search keys.',
                'Render retrieval cards in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeArchiveRetrievalLabService.php',
                'app/Http/Controllers/Practice/PracticeArchiveRetrievalLabController.php',
                'app/Http/Controllers/Api/PracticeArchiveRetrievalLabController.php',
                'resources/views/practice/archive-retrieval-lab.blade.php',
                'tests/Feature/PracticeArchiveRetrievalLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeArchiveRetrievalLabTest',
                'php artisan test --filter PracticeSessionArchiveLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open archive retrieval lab',
                'route' => 'practice.archive-retrieval-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/archive-retrieval-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use archive filters to build retrieval cards.',
                ],
            ],
            'acceptance' => [
                'The lab includes retrieval rules and retrieval cards.',
                'Each card includes route, archive status, retrieval mode, search keys, prompt, reuse targets, proof to quote, and refresh check.',
                'The lab includes retrieval summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeArchiveRetrievalLabService
{
    public function build(array $filters = []): array
    {
        return [
            'retrieval_cards' => [],
            'retrieval_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'practice-evidence-reuse-plan-lab',
            'track' => 'testing-quality',
            'title' => 'Build an evidence reuse plan practice lab',
            'objective' => 'Turn archive retrieval cards into concrete portfolio, interview, and review reuse tasks with proof inputs, quality checks, and progress payload.',
            'why' => 'This makes archived evidence actionable so learners can turn practice output into artifacts and answers.',
            'steps' => [
                'Build retrieval cards from archive entries.',
                'Convert each retrieval card into a reuse plan.',
                'Attach portfolio task, interview task, review task, proof inputs, and quality check.',
                'Summarize reuse plans, portfolio-ready plans, refresh plans, and routes reused.',
                'Render reuse plans in Blade and expose them through an API.',
            ],
            'files' => [
                'app/Services/Practice/PracticeEvidenceReusePlanLabService.php',
                'app/Http/Controllers/Practice/PracticeEvidenceReusePlanLabController.php',
                'app/Http/Controllers/Api/PracticeEvidenceReusePlanLabController.php',
                'resources/views/practice/evidence-reuse-plan-lab.blade.php',
                'tests/Feature/PracticeEvidenceReusePlanLabTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeEvidenceReusePlanLabTest',
                'php artisan test --filter PracticeArchiveRetrievalLabTest',
                'vendor/bin/pint --test',
            ],
            'page' => [
                'label' => 'Open evidence reuse plan lab',
                'route' => 'practice.evidence-reuse-plan-lab',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/evidence-reuse-plan-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3',
                'payload' => [
                    'note' => 'No request body. Use retrieval filters to build reuse plans.',
                ],
            ],
            'acceptance' => [
                'The lab includes reuse rules and reuse plans.',
                'Each plan includes route, reuse mode, source prompt, portfolio task, interview task, review task, proof inputs, and quality check.',
                'The lab includes reuse summary and progress payload.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeEvidenceReusePlanLabService
{
    public function build(array $filters = []): array
    {
        return [
            'reuse_plans' => [],
            'reuse_summary' => [],
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'feature-test-route-behavior',
            'track' => 'testing-quality',
            'title' => 'Write feature tests for a practice route',
            'objective' => 'Add tests that prove a route renders the correct page and rejects invalid input.',
            'why' => 'This practices behavior-first testing instead of only checking that Laravel boots.',
            'steps' => [
                'Pick one existing route in this hub.',
                'Write a test for successful rendering.',
                'Write a test for one invalid filter or missing resource.',
                'Keep assertions tied to visible behavior or JSON shape.',
                'Call the quality-gate API with the result counts from your verification commands.',
            ],
            'files' => [
                'tests/Feature/*Test.php',
                'tests/Unit/Practice/PracticeQualityGateServiceTest.php',
                'app/Services/Practice/PracticeQualityGateService.php',
                'app/Http/Controllers/Api/PracticeQualityGateController.php',
                'resources/views/practice/workbench/quality-gate.blade.php',
            ],
            'commands' => [
                'php artisan test',
                'php artisan test --filter QualityGateWorkbenchTest',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the quality-gate workbench',
                'route' => 'practice.workbench.quality-gate',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/quality-gate',
                'payload' => [
                    'tests' => 31,
                    'assertions' => 1519,
                    'failures' => 0,
                    'pint' => true,
                ],
            ],
            'acceptance' => [
                'The test name explains behavior.',
                'The test fails if the route output breaks.',
                'The test avoids implementation-only assertions.',
                'The quality gate reports `ready` only when tests and Pint pass.',
            ],
            'starter_code' => <<<'PHP'
public function test_practice_page_renders_expected_content(): void
{
    $response = $this->get('/practice');

    $response
        ->assertOk()
        ->assertSee('Practice Workspace');
}
PHP,
        ],
        [
            'slug' => 'async-job-plan-workbench',
            'track' => 'testing-quality',
            'title' => 'Plan an async queue job with retries',
            'objective' => 'Build a safe async job plan before creating a real queued Job class.',
            'why' => 'This practices async design, retry policy, idempotency keys, payload boundaries, and queue commands without requiring a live worker first.',
            'steps' => [
                'Create a Form Request for job planning input.',
                'Create a service that builds job class, payload key, idempotency key, retry policy, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanAsyncJobRequest.php',
                'app/Http/Controllers/Api/AsyncJobPlanController.php',
                'app/Services/Practice/AsyncJobPlanService.php',
                'resources/views/practice/workbench/async-job-plan.blade.php',
                'tests/Feature/AsyncJobPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter AsyncJobPlanWorkbenchTest',
                'php artisan route:list --path=async-job-plan',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the async job planning workbench',
                'route' => 'practice.workbench.async-job-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/async-job-plan',
                'payload' => [
                    'job_name' => 'Sync External Order',
                    'payload_key' => 'order 123',
                    'attempts' => 3,
                    'backoff_seconds' => 60,
                ],
            ],
            'acceptance' => [
                'The plan includes a job class name.',
                'The plan includes an idempotency key.',
                'The retry policy is explicit.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
final class SyncExternalOrder implements ShouldQueue
{
    public function handle(): void
    {
        // Check idempotency before calling the external system.
    }
}
PHP,
        ],
        [
            'slug' => 'event-listener-plan-workbench',
            'track' => 'testing-quality',
            'title' => 'Plan events and listeners for side effects',
            'objective' => 'Design an event/listener pair that keeps the core workflow separate from notification, mail, logging, or sync side effects.',
            'why' => 'This practices event naming, listener responsibility, queued listener decisions, and event testing without hiding side effects inside controllers.',
            'steps' => [
                'Create a Form Request for event/listener planning input.',
                'Create a service that builds event class, listener class, side effect, flow, test strategy, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanEventListenerRequest.php',
                'app/Http/Controllers/Api/EventListenerPlanController.php',
                'app/Services/Practice/EventListenerPlanService.php',
                'resources/views/practice/workbench/event-listener-plan.blade.php',
                'tests/Feature/EventListenerPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter EventListenerPlanWorkbenchTest',
                'php artisan route:list --path=event-listener-plan',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the event/listener planning workbench',
                'route' => 'practice.workbench.event-listener-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/event-listener-plan',
                'payload' => [
                    'event_name' => 'Practice Task Completed',
                    'listener_name' => 'Send Practice Completion Notification',
                    'side_effect' => 'Send learner notification',
                    'queued' => true,
                ],
            ],
            'acceptance' => [
                'The event name describes something that already happened.',
                'The listener owns one side effect.',
                'The plan explains when to queue the listener.',
                'The test strategy uses event or queue fakes intentionally.',
            ],
            'starter_code' => <<<'PHP'
event(new PracticeTaskCompleted($taskId));

final class SendPracticeCompletionNotification
{
    public function handle(PracticeTaskCompleted $event): void
    {
        // Send one side effect here, not in the controller.
    }
}
PHP,
        ],
        [
            'slug' => 'container-binding-plan-workbench',
            'track' => 'laravel-http',
            'title' => 'Plan service-container bindings',
            'objective' => 'Design a contract, implementation, binding lifetime, and injection target before wiring dependencies together.',
            'why' => 'This practices dependency inversion, Laravel service container bindings, constructor injection, and testable architecture.',
            'steps' => [
                'Create a Form Request for binding-plan input.',
                'Create a service that builds contract, implementation, binding, injection, steps, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanContainerBindingRequest.php',
                'app/Http/Controllers/Api/ContainerBindingPlanController.php',
                'app/Services/Practice/ContainerBindingPlanService.php',
                'resources/views/practice/workbench/container-binding-plan.blade.php',
                'tests/Feature/ContainerBindingPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter ContainerBindingPlanWorkbenchTest',
                'php artisan route:list --path=container-binding-plan',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the container binding workbench',
                'route' => 'practice.workbench.container-binding-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/container-binding-plan',
                'payload' => [
                    'contract_name' => 'Payment Gateway',
                    'implementation_name' => 'Stripe Payment Gateway',
                    'lifetime' => 'bind',
                    'injection_target' => 'Checkout Controller',
                ],
            ],
            'acceptance' => [
                'The plan includes a contract and implementation.',
                'The binding method matches the requested lifetime.',
                'The injection example depends on the contract.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
$this->app->bind(PaymentGateway::class, StripePaymentGateway::class);

public function __construct(private readonly PaymentGateway $gateway)
{
}
PHP,
        ],
        [
            'slug' => 'dependency-injection-refactor-drill',
            'track' => 'laravel-http',
            'title' => 'Refactor manual dependencies into injection',
            'objective' => 'Turn manual object creation into constructor injection, a contract binding, and a testable Laravel dependency flow.',
            'why' => 'This practices Dependency Injection, service-container resolution, dependency inversion, and replacing concrete collaborators in tests.',
            'steps' => [
                'Open the Dependency Injection refactor workbench and describe the manual dependency.',
                'Find the controller or service that creates collaborators with manual `new` calls.',
                'Extract the collaborator behind a small interface that describes the behavior the use case needs.',
                'Bind the interface to the implementation in a service provider.',
                'Inject the interface into the controller or service constructor.',
                'Write a feature or unit test that swaps the dependency with a fake.',
            ],
            'files' => [
                'app/Providers/AppServiceProvider.php',
                'app/Contracts/ReportExporter.php',
                'app/Services/CsvReportExporter.php',
                'app/Http/Controllers/ReportController.php',
                'tests/Feature/ReportExportTest.php',
            ],
            'commands' => [
                'php artisan test --filter DependencyInjectionRefactorWorkbenchTest',
                'php artisan route:list --path=dependency-injection-refactor',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the Dependency Injection refactor workbench',
                'route' => 'practice.workbench.dependency-injection-refactor',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/dependency-injection-refactor',
                'payload' => [
                    'class_name' => 'Report Controller',
                    'manual_dependency' => 'CSV Report Exporter',
                    'dependency_role' => 'export monthly report rows',
                    'contract_name' => 'Report Exporter',
                    'method_name' => 'export',
                    'binding_lifetime' => 'bind',
                    'fake_name' => 'Fake Report Exporter',
                ],
            ],
            'acceptance' => [
                'The controller or service no longer creates the collaborator with manual `new` calls.',
                'The constructor depends on a contract or focused service type.',
                'A service provider binding explains which implementation Laravel should resolve.',
                'A test can swap the dependency with a fake or mock.',
            ],
            'starter_code' => <<<'PHP'
interface ReportExporter
{
    public function export(array $rows): string;
}

$this->app->bind(ReportExporter::class, CsvReportExporter::class);

public function __construct(private readonly ReportExporter $exporter)
{
}
PHP,
        ],
        [
            'slug' => 'abstract-class-interface-decision-drill',
            'track' => 'php-foundation',
            'title' => 'Choose abstract class or interface',
            'objective' => 'Decide whether an OOP design needs a pure contract, a shared base class, or no extra abstraction.',
            'why' => 'This practices PHP OOP tradeoffs, interview explanation, inheritance limits, swappable dependencies, and avoiding unnecessary abstraction.',
            'steps' => [
                'Describe whether the classes are related by type or only by behavior.',
                'Use an interface when unrelated classes need to promise the same behavior.',
                'Use an abstract class when child classes share meaningful base behavior or state.',
                'Avoid adding an abstraction when one concrete class is stable and no replacement is expected.',
                'Prepare a short interview answer that includes one practical example.',
            ],
            'files' => [
                'data/php/advanced.en.json',
                'data/php/advanced.vi.json',
                'app/Contracts/Notifier.php',
                'app/Reports/FileReport.php',
                'tests/Unit/OopAbstractionDecisionTest.php',
            ],
            'commands' => [
                'php artisan test --filter OopAbstractionDecisionWorkbenchTest',
                'php artisan route:list --path=oop-abstraction-decision',
                'php artisan test --filter StaticPortalCopyTest',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the OOP abstraction decision workbench',
                'route' => 'practice.workbench.oop-abstraction-decision',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/oop-abstraction-decision',
                'payload' => [
                    'scenario' => 'Payment Gateway',
                    'relationship' => 'unrelated',
                    'shared_behavior' => 'send a payment request',
                    'needs_multiple_implementations' => true,
                    'has_shared_state' => false,
                ],
            ],
            'acceptance' => [
                'The answer explains the difference between a contract and shared base behavior.',
                'The example uses an interface for swappable behavior.',
                'The example uses an abstract class only when shared state or shared implementation is real.',
                'The interview answer mentions that PHP supports multiple interfaces but only one parent class.',
            ],
            'starter_code' => <<<'PHP'
interface Notifier
{
    public function send(string $message): void;
}

abstract class FileReport
{
    public function __construct(protected string $path)
    {
    }

    abstract public function rows(): array;

    protected function read(): string
    {
        return file_get_contents($this->path) ?: '';
    }
}
PHP,
        ],
        [
            'slug' => 'clean-architecture-layering-tradeoff',
            'track' => 'laravel-http',
            'title' => 'Clean Architecture P6: Layered Architecture is not always right',
            'objective' => 'Decide whether a Laravel feature needs extra layers or should stay closer to the framework flow.',
            'why' => 'This practices architectural judgment: layers are useful when they protect real boundaries, but harmful when they only add pass-through files and review friction.',
            'steps' => [
                'Describe the feature as simple CRUD, complex workflow, integration-heavy flow, or domain-heavy rule set.',
                'Map which responsibilities already have natural Laravel homes: route, Form Request, controller, model/query, policy, job, event, or view/resource.',
                'Add an action or service only when it owns workflow decisions instead of forwarding data.',
                'Add a repository or query object only when persistence logic is reusable, complex, or needs a stable boundary.',
                'Write a PR review note explaining why each chosen layer exists or why it was intentionally skipped.',
            ],
            'files' => [
                'data/laravel/container-architecture.en.json',
                'data/laravel/container-architecture.vi.json',
                'routes/web.php',
                'app/Services/Practice/LayeredArchitectureDecisionService.php',
                'app/Http/Requests/Api/PlanLayeredArchitectureDecisionRequest.php',
                'app/Http/Controllers/Api/LayeredArchitectureDecisionController.php',
                'resources/views/practice/workbench/layered-architecture-decision.blade.php',
                'tests/Feature/LayeredArchitectureDecisionWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan route:list --name=practice.show',
                'php artisan test --filter LayeredArchitectureDecisionWorkbenchTest',
                'php artisan test --filter StaticPortalCopyTest',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the layered architecture decision workbench',
                'route' => 'practice.workbench.layered-architecture-decision',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/layered-architecture-decision',
                'payload' => [
                    'feature_name' => 'Checkout Submit',
                    'feature_type' => 'workflow',
                    'business_rule_count' => 4,
                    'integration_count' => 1,
                    'persistence_complexity' => 'complex',
                    'requires_async_work' => true,
                    'requires_policy' => true,
                ],
            ],
            'acceptance' => [
                'The feature does not add a service, action, or repository without a named responsibility.',
                'Simple CRUD stays simple unless a real boundary appears.',
                'Complex workflows move decisions into an action or service with tests.',
                'The review note explains the tradeoff instead of claiming layered architecture is always better.',
            ],
            'starter_code' => <<<'PHP'
// Keep simple CRUD close to the framework until the use case earns a boundary.
final class OrderController
{
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = Order::query()->create($request->validated());

        return redirect()->route('orders.show', $order);
    }
}

// Extract when workflow decisions become meaningful.
final class CreateOrderAction
{
    public function execute(array $data): Order
    {
        return DB::transaction(function () use ($data): Order {
            $order = Order::query()->create($data);

            // Reserve stock, publish event, or call integration here when needed.

            return $order;
        });
    }
}
PHP,
        ],
        [
            'slug' => 'system-design-tradeoff-plan-workbench',
            'track' => 'docker-runtime',
            'title' => 'Practice System Design tradeoff answers',
            'objective' => 'Turn an ambiguous System Design prompt into clarifying questions, explicit costs, team-fit checks, and a senior interview answer.',
            'why' => 'This practices the difference between naming technology and making architecture decisions: a strong answer explains why one cost is acceptable because the alternative cost is worse in context.',
            'steps' => [
                'Choose a scenario such as 10M notifications, payments, legacy scaling, or startup microservices.',
                'State latency, failure impact, consistency, team maturity, operational capacity, and current constraints.',
                'Generate the recommendation, tradeoff statement, decision matrix, and level framing.',
                'Rewrite the answer in the form: I choose A, the cost is X, and I accept it because Y is more expensive here.',
                'Add one metric or review trigger that would change the decision later.',
            ],
            'files' => [
                'data/interview/senior.en.json',
                'data/interview/senior.vi.json',
                'app/Http/Requests/Api/PlanSystemDesignTradeoffRequest.php',
                'app/Http/Controllers/Api/SystemDesignTradeoffPlanController.php',
                'app/Services/Practice/SystemDesignTradeoffPlanService.php',
                'resources/views/practice/workbench/system-design-tradeoff-plan.blade.php',
                'tests/Feature/SystemDesignTradeoffPlanWorkbenchTest.php',
                'tests/Unit/Practice/SystemDesignTradeoffPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter SystemDesignTradeoffPlan',
                'php artisan route:list --path=system-design-tradeoff-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the System Design tradeoff workbench',
                'route' => 'practice.workbench.system-design-tradeoff-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/system-design-tradeoff-plan',
                'payload' => [
                    'scenario' => 'notification-10m',
                    'latency_requirement' => 'real-time',
                    'failure_impact' => 'high',
                    'consistency_need' => 'eventual',
                    'team_maturity' => 'platform',
                    'operational_capacity' => 'high',
                    'current_constraint' => 'greenfield',
                ],
            ],
            'acceptance' => [
                'The answer asks clarifying questions before choosing technology.',
                'The recommendation includes the cost of the chosen design and the rejected alternative.',
                'The plan compares Long Polling, WebSocket, broker, vertical scaling, and strong consistency options.',
                'The output explains L4, L5, L6, and L7 answer signals.',
            ],
            'starter_code' => <<<'TEXT'
Question: Design notifications for 10 million users.

Do not draw first.
Ask: latency, platforms, outage impact, duplicate tolerance, team capacity.
Then answer: I choose A; the cost is X; I accept X because Y is more expensive here.
TEXT,
        ],
        [
            'slug' => 'siem-elk-plan-workbench',
            'track' => 'docker-runtime',
            'title' => 'Plan SIEM and ELK security logging',
            'objective' => 'Turn SIEM and ELK concepts into a concrete log pipeline with normalized fields, detection rules, alert ownership, retention, privacy controls, dashboards, and incident runbooks.',
            'why' => 'This practices the difference between collecting logs and operating a useful security monitoring capability: evidence, correlation, action, and cost control.',
            'steps' => [
                'Choose log sources such as Laravel app, cloud auth, Nginx/Linux, Kubernetes, or mixed enterprise logs.',
                'Define the detection goal, retention window, alert maturity, data sensitivity, and team size.',
                'Generate ELK roles, pipeline stages, normalized fields, detections, retention, privacy controls, and runbook steps.',
                'Review whether each alert is actionable and whether sensitive data is redacted before indexing.',
                'Copy the implementation prompt and turn it into one small parsing or detection rule task.',
            ],
            'files' => [
                'data/interview/devops.en.json',
                'data/interview/devops.vi.json',
                'app/Http/Requests/Api/PlanSiemElkRequest.php',
                'app/Http/Controllers/Api/SiemElkPlanController.php',
                'app/Services/Practice/SiemElkPlanService.php',
                'resources/views/practice/workbench/siem-elk-plan.blade.php',
                'tests/Feature/SiemElkPlanWorkbenchTest.php',
                'tests/Unit/Practice/SiemElkPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter SiemElkPlan',
                'php artisan route:list --path=siem-elk-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the SIEM ELK planning workbench',
                'route' => 'practice.workbench.siem-elk-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/siem-elk-plan',
                'payload' => [
                    'environment_name' => 'Production Security Logs',
                    'log_sources' => 'cloud-auth',
                    'detection_goal' => 'auth-abuse',
                    'retention_need' => 'long',
                    'alert_maturity' => 'low',
                    'data_sensitivity' => 'high',
                    'team_size' => 'small',
                ],
            ],
            'acceptance' => [
                'The plan separates SIEM capability from ELK tooling roles.',
                'The pipeline includes collection, parsing, enrichment, indexing, detection, and response.',
                'The field contract supports correlation across users, hosts, IPs, services, outcomes, and timestamps.',
                'The output includes retention, privacy, alert ownership, and incident runbook guidance.',
            ],
            'starter_code' => <<<'TEXT'
Question: Explain SIEM with ELK.

Do not answer with only "Elasticsearch stores logs".
Explain: source -> shipper -> parser -> index -> dashboard/alert -> runbook.
Then name the tradeoffs: parsing quality, alert noise, retention cost, privacy, and owner.
TEXT,
        ],
        [
            'slug' => 'react-render-optimization-workbench',
            'track' => 'frontend-performance',
            'title' => 'Optimize React re-renders with memoization discipline',
            'objective' => 'Practice when to use React.memo, useMemo, useCallback, state locality, and list virtualization from profiler evidence.',
            'why' => 'This keeps frontend performance work practical: measure first, stabilize props only where needed, avoid stale dependencies, and do not hide structural state problems behind memoization.',
            'steps' => [
                'Choose the component type, render symptom, state shape, list size, and profiler signal.',
                'Generate a recommendation and compare React.memo, useMemo, useCallback, state locality, and virtualization.',
                'Review anti-patterns and profiler checks before applying memoization.',
                'Copy the code examples and adapt them to one real component.',
            ],
            'files' => [
                'data/laravel/frontend.en.json',
                'data/laravel/frontend.vi.json',
                'app/Http/Requests/Api/PlanReactRenderOptimizationRequest.php',
                'app/Http/Controllers/Api/ReactRenderOptimizationPlanController.php',
                'app/Services/Practice/ReactRenderOptimizationPlanService.php',
                'resources/views/practice/workbench/react-render-optimization-plan.blade.php',
                'tests/Feature/ReactRenderOptimizationPlanWorkbenchTest.php',
                'tests/Unit/Practice/ReactRenderOptimizationPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter ReactRenderOptimizationPlan',
                'php artisan route:list --path=react-render-optimization-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the React render optimization workbench',
                'route' => 'practice.workbench.react-render-optimization-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/react-render-optimization-plan',
                'payload' => [
                    'component_name' => 'Customer Search Panel',
                    'component_type' => 'search-panel',
                    'render_issue' => 'prop-churn',
                    'state_shape' => 'parent-state',
                    'list_size' => 250,
                    'profiler_signal' => 'prop-churn',
                ],
            ],
            'acceptance' => [
                'The plan starts from React Profiler evidence.',
                'The recommendation separates React.memo, useMemo, and useCallback responsibilities.',
                'The plan names when state locality or virtualization matters more than memoization.',
            ],
            'starter_code' => <<<'TEXT'
Measure first.
Use React.memo for expensive children with stable props.
Use useMemo for expensive derived values.
Use useCallback for callback identity passed to memoized children.
Do not memoize everything blindly.
TEXT,
        ],
        [
            'slug' => 'load-balancer-algorithm-plan-workbench',
            'track' => 'docker-runtime',
            'title' => 'Plan load-balancer algorithms for system design',
            'objective' => 'Compare four common load-balancer algorithms and choose the right routing strategy for a realistic traffic pattern.',
            'why' => 'This practices system-design interview thinking: traffic shape, upstream capacity, sticky sessions, health checks, failure modes, and rollout verification.',
            'steps' => [
                'Open the load-balancer planning workbench and choose a traffic pattern.',
                'Compare round robin, weighted round robin, least connections, and IP hash.',
                'Generate an Nginx-style upstream example with forwarding headers.',
                'Review health-check, rollout, and failure-mode guidance.',
                'Practice a short interview answer that explains the tradeoff.',
            ],
            'files' => [
                'data/interview/devops.en.json',
                'data/interview/devops.vi.json',
                'app/Http/Requests/Api/PlanLoadBalancerRequest.php',
                'app/Http/Controllers/Api/LoadBalancerPlanController.php',
                'app/Services/Practice/LoadBalancerPlanService.php',
                'resources/views/practice/workbench/load-balancer-plan.blade.php',
                'tests/Feature/LoadBalancerPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter LoadBalancerPlan',
                'php artisan route:list --path=load-balancer-plan',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the load-balancer planning workbench',
                'route' => 'practice.workbench.load-balancer-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/load-balancer-plan',
                'payload' => [
                    'service_name' => 'Checkout API',
                    'traffic_pattern' => 'even',
                    'algorithm' => 'auto',
                    'upstream_count' => 3,
                    'has_sticky_sessions' => false,
                    'health_check_path' => '/up',
                ],
            ],
            'acceptance' => [
                'The plan names one recommended algorithm and explains why.',
                'The answer compares all four common algorithms.',
                'The generated config includes forwarding headers and upstream servers.',
                'The plan includes health-check, rollout, test, and failure-mode guidance.',
            ],
            'starter_code' => <<<'TEXT'
round_robin: equal servers and even traffic
weighted_round_robin: different upstream capacity
least_connections: long-running or bursty requests
ip_hash: transitional session affinity
TEXT,
        ],
        [
            'slug' => 'reverse-proxy-failure-plan-workbench',
            'track' => 'docker-runtime',
            'title' => 'Plan reverse-proxy failure modes and blast radius',
            'objective' => 'Explain why healthy origin servers can become unreachable when the shared reverse-proxy or edge layer fails, then design validation, rollout, health-gate, rollback, and fail-small controls.',
            'why' => 'This practices the production lesson behind edge outages: reverse proxies improve routing, security, TLS, and caching, but the shared request path must be validated and rolled out carefully because origin capacity does not help when traffic never reaches the origins.',
            'steps' => [
                'Open the reverse-proxy failure workbench and choose an edge or internal proxy scenario.',
                'Map the request path from client to proxy, load balancer, and origins.',
                'Generate blast-radius controls for config, generated files, rollout strategy, health gates, and rollback.',
                'Review observability signals that separate proxy-generated errors from origin-generated errors.',
                'Practice an interview answer about why many healthy servers can still be unreachable.',
            ],
            'files' => [
                'data/interview/devops.en.json',
                'data/interview/devops.vi.json',
                'app/Http/Requests/Api/PlanReverseProxyFailureRequest.php',
                'app/Http/Controllers/Api/ReverseProxyFailurePlanController.php',
                'app/Services/Practice/ReverseProxyFailurePlanService.php',
                'resources/views/practice/workbench/reverse-proxy-failure-plan.blade.php',
                'tests/Feature/ReverseProxyFailurePlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter ReverseProxyFailurePlan',
                'php artisan route:list --path=reverse-proxy-failure-plan',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the reverse-proxy failure workbench',
                'route' => 'practice.workbench.reverse-proxy-failure-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/reverse-proxy-failure-plan',
                'payload' => [
                    'service_name' => 'Public Checkout Edge',
                    'proxy_layer' => 'edge-cdn',
                    'change_type' => 'feature-file',
                    'origin_count' => 120,
                    'rollout_strategy' => 'global',
                    'has_health_gate' => false,
                    'fail_behavior' => 'fail_closed',
                    'observed_failure' => 'http_500',
                ],
            ],
            'acceptance' => [
                'The plan explains why healthy origins are not enough when the proxy layer fails.',
                'The request path names client, proxy, load balancer, and origin responsibilities.',
                'The answer includes config validation, rollout, health gates, rollback, and fail-small controls.',
                'The observability plan separates proxy-generated errors from origin-generated errors.',
            ],
            'starter_code' => <<<'TEXT'
Validate proxy config shape and size.
Roll out by canary or staged region.
Stop rollout on proxy 5xx, parser errors, CPU, or memory.
Rollback the edge artifact before scaling healthy origins.
TEXT,
        ],
        [
            'slug' => 'kubernetes-analogy-plan-workbench',
            'track' => 'docker-runtime',
            'title' => 'Explain Kubernetes with a ship fleet analogy',
            'objective' => 'Explain Kubernetes for beginners by mapping command ships, cargo ships, and container cargo to control plane, worker nodes, pods, containers, deployments, services, ingress, probes, and reconciliation.',
            'why' => 'This practices a beginner-friendly DevOps explanation without losing technical accuracy: Kubernetes keeps desired container state running across machines, schedules pods onto workers, exposes services, replaces failed pods, rolls out and rolls back deployments, handles disruption with availability controls, shuts down pods gracefully, separates config from secrets, scopes workloads with namespaces and RBAC, uses network policies to control pod traffic, connects observability to pod and app signals, diagnoses common pod status failures, maps backend concerns such as migrations, queues, sessions, cache, readiness, and schedulers to safe Kubernetes shapes, reviews manifests before merge, gates deployment through CI/CD checks, shows small YAML snippets learners can recognize, gives a one-minute script, interview rubric, command ladder, resource decision guide, manifest smell catalog, practice drills, production readiness score, SLO observability plan, and deployment review questions, controls cost with capacity planning, uses image and security-context guardrails, uses probes to control traffic and restarts, needs resource requests and limits for scheduling, scales from workload signals, and needs careful storage planning for stateful workloads.',
            'steps' => [
                'Create a Form Request for Kubernetes analogy input.',
                'Create a service that returns the ship analogy, Kubernetes resource map, control loop, workload plan, probe plan, scaling plan, resource plan, rollout plan, ConfigMap and Secret guidance, namespace and RBAC guidance, network policy guidance, observability guidance, cost and capacity guidance, availability guidance, graceful shutdown guidance, image security guidance, backend runtime guidance, manifest review checklist, CI/CD gate plan, YAML snippets, one-minute script, interview rubric, command ladder, resource decision guide, manifest smell catalog, practice drills, production readiness score, SLO observability plan, deployment review questions, traffic flow, manifest outline, kubectl commands, troubleshooting runbook, failure diagnosis matrix, misconceptions, and interview answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API with web, worker, and scheduled-job presets.',
                'Write feature tests for public web API, stateful scheduled job, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanKubernetesAnalogyRequest.php',
                'app/Http/Controllers/Api/KubernetesAnalogyPlanController.php',
                'app/Services/Practice/KubernetesAnalogyPlanService.php',
                'resources/views/practice/workbench/kubernetes-analogy-plan.blade.php',
                'tests/Feature/KubernetesAnalogyPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter KubernetesAnalogyPlan',
                'php artisan route:list --path=kubernetes-analogy-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the Kubernetes analogy workbench',
                'route' => 'practice.workbench.kubernetes-analogy-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/kubernetes-analogy-plan',
                'payload' => [
                    'learning_goal' => 'one-minute',
                    'app_type' => 'web-api',
                    'replicas' => 3,
                    'needs_external_access' => true,
                    'has_stateful_data' => false,
                ],
            ],
            'acceptance' => [
                'The analogy maps command ship to control plane, cargo ship to worker node, container cargo to container, stack to pod, shipping order to deployment, and harbor address to service.',
                'The plan explains desired state, scheduling, container startup, reconciliation, service traffic, probes, resource requests and limits, scaling signals, rollout and rollback commands, ConfigMap and Secret usage, namespace and RBAC boundaries, network policies, observability signals, failure diagnosis for ImagePullBackOff, OOMKilled, probe failure, Forbidden, DNS timeout, and volume mount failure, backend runtime concerns such as migrations, queue workers, sessions, cache, readiness dependencies, and scheduled tasks, manifest review checks, CI/CD dry-run and policy gates, YAML snippets, one-minute script, interview rubric, command ladder, resource decision guide, manifest smell catalog, practice drills, production readiness score, SLO observability plan, deployment review questions, cost and capacity controls, PodDisruptionBudget, graceful shutdown, image tags, pull policy, security context, manifests, kubectl commands, troubleshooting, and beginner misconceptions.',
                'Stateful workloads warn about persistent storage instead of relying on disposable pod files.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'YAML'
apiVersion: apps/v1
kind: Deployment
metadata:
  name: app
spec:
  replicas: 3
YAML,
        ],
        [
            'slug' => 'sql-injection-defense-plan-workbench',
            'track' => 'security-auth',
            'title' => 'Plan SQL Injection prevention with parameterized queries',
            'objective' => 'Practice explaining SQL Injection, replacing string SQL with bindings, allowlisting dynamic identifiers, and testing malicious payloads.',
            'why' => 'This practices a common interview and production security topic: user input must stay data, SQL structure must stay fixed, and raw query escape hatches need explicit review.',
            'steps' => [
                'Choose the query style, input surface, dynamic SQL part, and whether bindings are already used.',
                'Generate risk, recommendation, safe query patterns, allowlist review, and malicious payload tests.',
                'Rewrite unsafe raw SQL into query builder or bound raw SQL.',
                'Add tests with payloads such as `OR 1=1` and unsafe sort inputs.',
            ],
            'files' => [
                'data/laravel/auth-security.en.json',
                'data/laravel/auth-security.vi.json',
                'app/Http/Requests/Api/PlanSqlInjectionDefenseRequest.php',
                'app/Http/Controllers/Api/SqlInjectionDefensePlanController.php',
                'app/Services/Practice/SqlInjectionDefensePlanService.php',
                'resources/views/practice/workbench/sql-injection-defense-plan.blade.php',
                'tests/Feature/SqlInjectionDefensePlanWorkbenchTest.php',
                'tests/Unit/Practice/SqlInjectionDefensePlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter SqlInjectionDefensePlan',
                'php artisan route:list --path=sql-injection-defense-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the SQL Injection defense workbench',
                'route' => 'practice.workbench.sql-injection-defense-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/sql-injection-defense-plan',
                'payload' => [
                    'query_name' => 'User Search',
                    'query_style' => 'raw-sql',
                    'input_surface' => 'search-box',
                    'dynamic_parts' => 'order-by',
                    'uses_bindings' => false,
                ],
            ],
            'acceptance' => [
                'The answer explains how SQL Injection turns input into SQL logic.',
                'The safer pattern uses parameter bindings for values.',
                'Dynamic identifiers use allowlists instead of arbitrary request input.',
                'Tests include malicious payloads and prove no extra rows or unsafe sorting occur.',
            ],
            'starter_code' => <<<'PHP'
// Unsafe
$sql = "select * from users where email = '".$request->input('email')."'";

// Safer
$users = DB::select(
    'select * from users where email = ?',
    [$request->input('email')]
);
PHP,
        ],
        [
            'slug' => 'csrf-protection-plan-workbench',
            'track' => 'security-auth',
            'title' => 'Plan CSRF protection for browser and cookie flows',
            'objective' => 'Practice explaining CSRF, choosing token and SameSite controls, and testing missing or stale CSRF tokens.',
            'why' => 'This practices a common Laravel security topic: browsers send cookies automatically, so state-changing web flows need request-intent proof.',
            'steps' => [
                'Choose the client type, state-changing method, SameSite setting, and whether token validation already exists.',
                'Generate a risk score, attack flow, controls, SameSite review, and test matrix.',
                'Explain why tokens, SameSite cookies, and safe HTTP methods solve different parts of the problem.',
                'Write feature tests for missing tokens, stale tokens, and unsafe GET mutations.',
            ],
            'files' => [
                'data/laravel/auth-security.en.json',
                'data/laravel/auth-security.vi.json',
                'app/Http/Requests/Api/PlanCsrfProtectionRequest.php',
                'app/Http/Controllers/Api/CsrfProtectionPlanController.php',
                'app/Services/Practice/CsrfProtectionPlanService.php',
                'resources/views/practice/workbench/csrf-protection-plan.blade.php',
                'tests/Feature/CsrfProtectionPlanWorkbenchTest.php',
                'tests/Unit/Practice/CsrfProtectionPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter CsrfProtectionPlan',
                'php artisan route:list --path=csrf-protection-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the CSRF protection workbench',
                'route' => 'practice.workbench.csrf-protection-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/csrf-protection-plan',
                'payload' => [
                    'flow_name' => 'Profile Update',
                    'client_type' => 'blade-web',
                    'state_changing_method' => 'post',
                    'has_csrf_token' => true,
                    'same_site' => 'lax',
                    'uses_cookie_auth' => true,
                ],
            ],
            'acceptance' => [
                'The answer explains CSRF as an unwanted state-changing request from a logged-in browser.',
                'The plan distinguishes CSRF tokens from SameSite cookie behavior.',
                'Unsafe GET mutations are flagged before merge.',
                'Tests include missing-token and stale-token behavior.',
            ],
            'starter_code' => <<<'BLADE'
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')
    <input name="name" value="{{ old('name', $user->name) }}">
    <button type="submit">Save</button>
</form>
BLADE,
        ],
        [
            'slug' => 'idor-access-review-workbench',
            'track' => 'security-auth',
            'title' => 'Review IDOR object-level authorization',
            'objective' => 'Practice finding Insecure Direct Object Reference risk, modeling attacker ID swaps, designing scoped queries, policies, denial tests, merge evidence, and monitoring guidance.',
            'why' => 'This practices a common API security failure: authentication is not enough when a user-controlled object ID can resolve another user, tenant, team, file, export, or nested resource.',
            'steps' => [
                'Choose the resource, route pattern, access model, and current authorization controls.',
                'Generate risk drivers, route surface review, abuse cases, authorization map, remediation plan, and 403 versus 404 guidance.',
                'Create a Laravel policy and scoped query before returning the object.',
                'Add two-user or two-tenant feature tests for read, update, delete, download, and export denial paths.',
                'Keep route-list, replay notes, passing tests, and monitoring guidance as merge evidence.',
            ],
            'files' => [
                'data/laravel/auth-security.en.json',
                'data/laravel/auth-security.vi.json',
                'app/Http/Requests/Api/ReviewIdorAccessRequest.php',
                'app/Http/Controllers/Api/IdorAccessReviewController.php',
                'app/Services/Practice/IdorAccessReviewService.php',
                'resources/views/practice/workbench/idor-access-review.blade.php',
                'tests/Feature/IdorAccessReviewWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter IdorAccessReviewWorkbenchTest',
                'php artisan route:list --path=idor-access-review',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the IDOR access review workbench',
                'route' => 'practice.workbench.idor-access-review',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/idor-access-review',
                'payload' => [
                    'resource_name' => 'Invoice',
                    'route_pattern' => '/api/invoices/{invoice}',
                    'access_model' => 'tenant',
                    'uses_policy' => false,
                    'query_scoped' => false,
                    'attacker_changes_id' => true,
                ],
            ],
            'acceptance' => [
                'The review explains IDOR as object-level authorization failure.',
                'The plan scopes the query before returning the object.',
                'Policies or Gates check the exact object, not only the logged-in user.',
                'Tests prove another user or tenant cannot read, update, delete, download, or export the object.',
                'Merge evidence includes replay notes, route-list output, passing denial tests, and monitoring guidance.',
            ],
            'starter_code' => <<<'PHP'
Route::get('/api/invoices/{invoice}', function (Invoice $invoice) {
    Gate::authorize('view', $invoice);

    return new InvoiceResource($invoice);
});

public function view(User $user, Invoice $invoice): bool
{
    return $user->tenant_id === $invoice->tenant_id;
}
PHP,
        ],
        [
            'slug' => 'authorization-policy-plan-workbench',
            'track' => 'laravel-http',
            'title' => 'Plan authorization policies',
            'objective' => 'Design a policy method and test strategy before placing access-control checks into a controller.',
            'why' => 'This practices policies, abilities, controller authorization calls, 403 behavior, and access-control tests.',
            'steps' => [
                'Create a Form Request for policy-plan input.',
                'Create a service that builds policy class, ability, controller usage, rule, tests, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanAuthorizationPolicyRequest.php',
                'app/Http/Controllers/Api/AuthorizationPolicyPlanController.php',
                'app/Services/Practice/AuthorizationPolicyPlanService.php',
                'resources/views/practice/workbench/authorization-policy-plan.blade.php',
                'tests/Feature/AuthorizationPolicyPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter AuthorizationPolicyPlanWorkbenchTest',
                'php artisan route:list --path=authorization-policy-plan',
                'vendor/bin/pint --test',
            ],
            'workbench' => [
                'label' => 'Run the authorization policy workbench',
                'route' => 'practice.workbench.authorization-policy-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/authorization-policy-plan',
                'payload' => [
                    'model_name' => 'Practice Task',
                    'ability' => 'update',
                    'actor_role' => 'owner',
                    'rule' => 'Only the owner can update an unfinished practice task.',
                ],
            ],
            'acceptance' => [
                'The policy class and method are named clearly.',
                'The controller usage uses `$this->authorize(...)`.',
                'The test plan includes allowed and forbidden cases.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
public function update(User $user, PracticeTask $practiceTask): bool
{
    return $user->id === $practiceTask->user_id
        && ! $practiceTask->is_done;
}
PHP,
        ],
        [
            'slug' => 'cache-strategy-plan-workbench',
            'track' => 'testing-quality',
            'title' => 'Plan cache strategy for read-heavy data',
            'objective' => 'Design cache keys, TTL, invalidation triggers, and fallback behavior before adding cache to a Laravel flow.',
            'why' => 'This practices performance thinking, cache ownership, stale-data risk, and verification commands.',
            'steps' => [
                'Create a Form Request for cache strategy input.',
                'Create a service that builds cache key, TTL, invalidation strategy, steps, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanCacheStrategyRequest.php',
                'app/Http/Controllers/Api/CacheStrategyPlanController.php',
                'app/Services/Practice/CacheStrategyPlanService.php',
                'resources/views/practice/workbench/cache-strategy-plan.blade.php',
                'tests/Feature/CacheStrategyPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter CacheStrategyPlanWorkbenchTest',
                'php artisan route:list --path=cache-strategy-plan',
                'php artisan cache:clear',
            ],
            'workbench' => [
                'label' => 'Run the cache strategy workbench',
                'route' => 'practice.workbench.cache-strategy-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/cache-strategy-plan',
                'payload' => [
                    'resource_name' => 'Practice dashboard',
                    'scope' => 'user 42',
                    'ttl_minutes' => 15,
                    'invalidation_trigger' => 'Clear when practice progress is updated',
                ],
            ],
            'acceptance' => [
                'The plan includes a scoped cache key.',
                'The TTL is converted to seconds.',
                'The invalidation trigger is explicit.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
Cache::remember('practice:dashboard:user-42', now()->addMinutes(15), function () {
    return $this->dashboard->summaryFor($user);
});
PHP,
        ],
        [
            'slug' => 'lsm-tree-plan-workbench',
            'track' => 'testing-quality',
            'title' => 'Explain NoSQL write speed with LSM Trees',
            'objective' => 'Explain why many NoSQL engines are fast for writes by modeling the LSM Tree path through WAL, memtable, immutable segments, compaction, and Bloom Filters.',
            'why' => 'This practices storage-engine performance reasoning: schema flexibility is not the speed explanation, sequential writes matter, compaction is a tradeoff, Bloom Filters reduce negative lookup cost, architecture decisions need recorded consequences, cost must include memory/disk/IO/operations, engine comparison must separate LSM, B-tree, search projection, and cache responsibilities, rollout needs model, benchmark, dual-read, and ramp phases, range scans need key design, data model review must catch hot partitions, query contracts should ban broad scans, tombstones and TTL require lifecycle policy, capacity must include memory and disk headroom, SLO guardrails must define p99 latency, compaction debt, disk headroom, read-miss efficiency, and tombstone pressure, decisions should follow workload evidence, benchmark plans must prove claims, anti-patterns should be avoided, incident runbooks should be ready, workload fit must be explicit, interview answers need structure, amplification must be separated, failure modes must be anticipated, and observability must track amplification and tail latency.',
            'steps' => [
                'Create a Form Request for LSM Tree storage-engine input.',
                'Create a service that explains architecture decision record, write path, read path, component roles, compaction tradeoffs, Bloom Filter impact, amplification model, range scan plan, tombstone TTL policy, capacity plan, cost model, engine comparison, decision rules, benchmark plan, rollout plan, anti-patterns, incident runbook, workload fit matrix, data model review, query contract, interview checklist, failure modes, workload score, tuning checklist, SLO guardrails, observability, and an interview-ready answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for write-heavy, missing Bloom Filter, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanLsmTreeRequest.php',
                'app/Http/Controllers/Api/LsmTreePlanController.php',
                'app/Services/Practice/LsmTreePlanService.php',
                'resources/views/practice/workbench/lsm-tree-plan.blade.php',
                'tests/Feature/LsmTreePlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter LsmTreePlan',
                'php artisan route:list --path=lsm-tree-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the LSM Tree workbench',
                'route' => 'practice.workbench.lsm-tree-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/lsm-tree-plan',
                'payload' => [
                    'workload_pattern' => 'write-heavy',
                    'write_rate_per_second' => 25000,
                    'read_miss_ratio' => 'high',
                    'compaction_strategy' => 'leveled',
                    'bloom_filter_enabled' => 'yes',
                    'schema_expectation' => 'schema-flexible',
                ],
            ],
            'acceptance' => [
                'The answer rejects the myth that NoSQL is fast simply because it lacks schema.',
                'The plan explains WAL, memtable, immutable segment flush, compaction, and Bloom Filter roles.',
                'The plan scores workload fit and calls out ADR consequences, cost model, engine comparison, rollout phases, SLO guardrails, read amplification, write amplification, space amplification, range scan risks, tombstone TTL risks, capacity planning, decision rules, benchmark scenarios, anti-patterns, incident response, fit matrix, data model review, query contract, interview answer structure, compaction pressure, and common failure modes.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'TEXT'
write -> WAL -> memtable -> immutable sorted segment
background -> compaction merges segments and removes old versions
read miss -> Bloom Filter can skip segments without touching disk
TEXT,
        ],
        [
            'slug' => 'file-storage-plan-workbench',
            'track' => 'laravel-http',
            'title' => 'Plan file storage and upload lifecycle',
            'objective' => 'Design upload validation, disk choice, visibility, path naming, and cleanup before accepting real files.',
            'why' => 'This practices Laravel filesystem thinking, validation, private/public visibility, storage paths, and lifecycle cleanup.',
            'steps' => [
                'Create a Form Request for file storage planning input.',
                'Create a service that builds validation rules, path pattern, lifecycle, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanFileStorageRequest.php',
                'app/Http/Controllers/Api/FileStoragePlanController.php',
                'app/Services/Practice/FileStoragePlanService.php',
                'resources/views/practice/workbench/file-storage-plan.blade.php',
                'tests/Feature/FileStoragePlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter FileStoragePlanWorkbenchTest',
                'php artisan route:list --path=file-storage-plan',
                'php artisan storage:link',
            ],
            'workbench' => [
                'label' => 'Run the file storage workbench',
                'route' => 'practice.workbench.file-storage-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/file-storage-plan',
                'payload' => [
                    'purpose' => 'Profile avatar',
                    'disk' => 'public',
                    'visibility' => 'public',
                    'max_mb' => 5,
                ],
            ],
            'acceptance' => [
                'The plan includes validation rules.',
                'The path pattern includes owner scope.',
                'The lifecycle includes cleanup.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
$path = $request->file('avatar')->store('avatars/'.$user->id, 'public');

$user->forceFill([
    'avatar_path' => $path,
])->save();
PHP,
        ],
        [
            'slug' => 'rate-limit-plan-workbench',
            'track' => 'security-auth',
            'title' => 'Plan API rate limiting for sensitive routes',
            'objective' => 'Design a named limiter, actor identity key, retry window, failure response, and tests before protecting a sensitive route.',
            'why' => 'This practices API security, Laravel throttling, middleware placement, identity boundaries, and abuse-case testing.',
            'steps' => [
                'Create a Form Request for rate-limit planning input.',
                'Create a service that builds limiter name, middleware, identity key, response, steps, tests, and commands.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for success and validation failure.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanRateLimitRequest.php',
                'app/Http/Controllers/Api/RateLimitPlanController.php',
                'app/Services/Practice/RateLimitPlanService.php',
                'resources/views/practice/workbench/rate-limit-plan.blade.php',
                'tests/Feature/RateLimitPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter RateLimitPlanWorkbenchTest',
                'php artisan route:list --path=rate-limit-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the rate-limit workbench',
                'route' => 'practice.workbench.rate-limit-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/rate-limit-plan',
                'payload' => [
                    'endpoint_name' => 'Password reset request',
                    'actor_type' => 'user',
                    'max_attempts' => 5,
                    'decay_minutes' => 10,
                    'sensitivity' => 'sensitive',
                ],
            ],
            'acceptance' => [
                'The plan includes a named limiter.',
                'The middleware string can be attached to a route.',
                'The identity key avoids raw secrets and request body trust.',
                'Page and API tests both pass.',
            ],
            'starter_code' => <<<'PHP'
RateLimiter::for('password-reset-request', function (Request $request) {
    return Limit::perMinutes(10, 5)->by(optional($request->user())->id ?: $request->ip());
});
PHP,
        ],
        [
            'slug' => 'jwt-token-storage-plan-workbench',
            'track' => 'security-auth',
            'title' => 'Choose JWT storage by threat model',
            'objective' => 'Compare localStorage, HttpOnly cookies, bearer tokens, and platform storage before choosing where JWTs should live or how existing storage should migrate.',
            'why' => 'This practices interview-ready auth security reasoning: XSS exposure, CSRF controls, token lifetime, refresh-token rotation, revocation, decision matrices, risk scoring, migration planning, and client type boundaries.',
            'steps' => [
                'Create a Form Request for token-storage planning input.',
                'Create a service that recommends storage from client type, token lifetime, XSS risk, CSRF controls, and refresh-token usage.',
                'Return a decision matrix that explains how each input signal affects the recommendation.',
                'Add risk scoring and review checklist output so the recommendation is actionable.',
                'Return implementation snippets for cookie, bearer, localStorage demo, and platform storage flows.',
                'Generate migration steps from the current storage pattern to the recommended storage pattern.',
                'Add scenario presets to the workbench so learners can compare common client types quickly.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature and unit tests for secure-cookie, localStorage demo, mobile, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanJwtTokenStorageRequest.php',
                'app/Http/Controllers/Api/JwtTokenStoragePlanController.php',
                'app/Services/Practice/JwtTokenStoragePlanService.php',
                'resources/views/practice/workbench/jwt-token-storage-plan.blade.php',
                'tests/Feature/JwtTokenStoragePlanWorkbenchTest.php',
                'tests/Unit/Practice/JwtTokenStoragePlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter JwtTokenStoragePlan',
                'php artisan route:list --path=jwt-token-storage-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the JWT storage workbench',
                'route' => 'practice.workbench.jwt-token-storage-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/jwt-token-storage-plan',
                'payload' => [
                    'client_type' => 'same-domain-spa',
                    'current_storage' => 'localStorage',
                    'token_lifetime' => 'minutes',
                    'xss_risk' => 'high',
                    'csrf_controls' => 'strong',
                    'refresh_token' => 'yes',
                ],
            ],
            'acceptance' => [
                'Browser flows with strong CSRF controls recommend HttpOnly Secure SameSite cookies.',
                'localStorage is limited to low-risk demo contexts.',
                'Mobile clients recommend secure platform storage.',
                'The plan includes controls, tests, tradeoffs, a decision matrix, risk scoring, a review checklist, implementation snippets, migration steps, and an interview-ready answer.',
            ],
            'starter_code' => <<<'HTTP'
Set-Cookie: refresh_token=...; HttpOnly; Secure; SameSite=Lax; Path=/auth/refresh

Authorization: Bearer <short-lived-access-token>
HTTP,
        ],
        [
            'slug' => 'jwt-revocation-plan-workbench',
            'track' => 'security-auth',
            'title' => 'Plan JWT revocation with API and database checks',
            'objective' => 'Design how to invalidate JWT access after logout, password change, role change, device loss, or refresh-token reuse.',
            'why' => 'This practices the production tradeoff behind stateless JWTs: a token cannot be revoked before expiry unless the server checks additional state such as a jti denylist, token version, or refresh-token family.',
            'steps' => [
                'Create a Form Request for revocation planning input.',
                'Create a service that chooses denylist, token-version, refresh-rotation, or short-lived access-token strategy.',
                'Return an architecture summary that states the decision, main tradeoff, poor-fit warning, and next design decision.',
                'Return a threat model for access tokens, refresh-token families, user authorization state, and browser sessions.',
                'Return an incident playbook for triage, containment, recovery, and evidence capture after suspected token compromise.',
                'Return database schema notes for revoked token records, user security versions, and refresh-token families.',
                'Return a retention plan for indexes, pruning, audit metadata, and revocation store capacity.',
                'Return a decision matrix that explains how client type, token lifetime, store choice, logout requirement, and refresh rotation affect the plan.',
                'Return a risk score so learners can compare day-long tokens, missing refresh rotation, cache eviction, and stateful enforcement tradeoffs.',
                'Return API endpoint guidance for login, logout, refresh, and logout-all flows.',
                'Return middleware checks that verify signature, expiry, jti, server-side revocation state, and authorization scope.',
                'Return a rollout plan for instrument, dual-read, enforce, and harden phases.',
                'Return an observability plan with metrics, structured log events, and alerts for revoked-token enforcement.',
                'Return a failure-mode plan for revocation store outages, stale cache entries, refresh-token reuse spikes, and token-version drift.',
                'Return a structured test matrix for feature, security, operations, privacy, and authorization coverage.',
                'Return a review checklist for token identity, server-side state, retention, observability, and refresh-token reuse.',
                'Add scenario presets to the workbench for logout everywhere, role change, lost device, and low-risk API cases.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature and unit tests for denylist, token-version, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanJwtRevocationRequest.php',
                'app/Http/Controllers/Api/JwtRevocationPlanController.php',
                'app/Services/Practice/JwtRevocationPlanService.php',
                'resources/views/practice/workbench/jwt-revocation-plan.blade.php',
                'tests/Feature/JwtRevocationPlanWorkbenchTest.php',
                'tests/Unit/Practice/JwtRevocationPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter JwtRevocationPlan',
                'php artisan route:list --path=jwt-revocation-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the JWT revocation workbench',
                'route' => 'practice.workbench.jwt-revocation-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/jwt-revocation-plan',
                'payload' => [
                    'client_type' => 'browser-spa',
                    'revocation_model' => 'denylist',
                    'token_lifetime' => 'hours',
                    'revocation_store' => 'database',
                    'immediate_logout' => 'yes',
                    'refresh_rotation' => 'yes',
                ],
            ],
            'acceptance' => [
                'Denylist plans use a jti lookup and revoked token storage until token expiry.',
                'Token-version plans explain user-wide invalidation after role, password, or account-risk changes.',
                'Refresh-token rotation plans explain reuse detection and family revocation.',
                'The plan includes an architecture summary, threat model, incident playbook, decision matrix, risk score, database schema notes, retention plan, API endpoints, middleware flow, revocation steps, rollout plan, observability plan, failure-mode plan, test matrix, review checklist, risk notes, snippets, tests, and an interview-ready answer.',
            ],
            'starter_code' => <<<'PHP'
Schema::create('revoked_tokens', function (Blueprint $table) {
    $table->string('jti')->primary();
    $table->foreignId('user_id')->index();
    $table->timestamp('revoked_at');
    $table->timestamp('expires_at')->index();
});
PHP,
        ],
        [
            'slug' => 'oauth-flow-plan-workbench',
            'track' => 'security-auth',
            'title' => 'Replace OAuth Implicit Flow with Authorization Code and PKCE',
            'objective' => 'Compare OAuth2 Implicit Flow with Authorization Code flow and design a migration path toward Authorization Code with PKCE or a confidential backend client.',
            'why' => 'This practices modern OAuth security reasoning: front-channel token exposure, architecture decision records, client compatibility, threat modeling, provider capability checks, token lifetime policy, scope consent matrix, authorize request hardening, token endpoint contract, frontend callback cleanup, ID-token validation, phased rollout, client cutover, observability, failure modes, incident response, deprecation policy, security test matrix, callback validation, PKCE checks, public versus confidential clients, browser URL leakage, refresh-token rotation, migration from response_type=token, code examples, review checks, and interview-ready explanation.',
            'steps' => [
                'Create a Form Request for OAuth flow planning input.',
                'Create a service that recommends Authorization Code with PKCE, confidential Authorization Code, or blocking until PKCE support exists.',
                'Return architecture decision record, compatibility notes, flow comparison, risk score, threat model, provider capability matrix, reasons Implicit is no longer recommended, sequence steps, callback validation rules, PKCE checklist, token lifetime policy, scope consent matrix, authorize request hardening, token endpoint contract, frontend cleanup plan, ID-token validation rules, implementation snippets, migration plan, rollout plan, client cutover checklist, observability plan, failure modes, incident response playbook, deprecation policy, security test matrix, review checklist, tests, and an interview-ready answer.',
                'Add scenario presets for legacy SPA, modern SPA, server-rendered backend, and mobile app cases.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API.',
                'Write feature tests for PKCE migration, backend confidential clients, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanOauthFlowRequest.php',
                'app/Http/Controllers/Api/OauthFlowPlanController.php',
                'app/Services/Practice/OauthFlowPlanService.php',
                'resources/views/practice/workbench/oauth-flow-plan.blade.php',
                'tests/Feature/OauthFlowPlanWorkbenchTest.php',
            ],
            'commands' => [
                'php artisan test --filter OauthFlowPlan',
                'php artisan route:list --path=oauth-flow-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the OAuth flow workbench',
                'route' => 'practice.workbench.oauth-flow-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/oauth-flow-plan',
                'payload' => [
                    'client_type' => 'legacy-spa',
                    'current_flow' => 'implicit',
                    'can_keep_secret' => 'no',
                    'pkce_supported' => 'yes',
                    'refresh_token_needed' => 'yes',
                    'browser_history_risk' => 'high',
                ],
            ],
            'acceptance' => [
                'Legacy SPA clients using Implicit Flow recommend Authorization Code with PKCE.',
                'Server-rendered backend clients that can keep secrets recommend confidential Authorization Code flow.',
                'The plan explains why front-channel token delivery is risky and why PKCE replaced the old Implicit workaround.',
                'The plan includes architecture decision record, compatibility notes, flow comparison, risk score, threat model, provider capability matrix, reasons Implicit is deprecated, sequence steps, callback validation rules, PKCE checklist, token lifetime policy, scope consent matrix, authorize request hardening, token endpoint contract, frontend cleanup plan, ID-token validation rules, code snippets, migration steps, rollout plan, client cutover checklist, observability plan, failure modes, incident response playbook, deprecation policy, security test matrix, review checklist, tests, and an interview-ready answer.',
            ],
            'starter_code' => <<<'TEXT'
Legacy:
GET /authorize?response_type=token&client_id=spa&redirect_uri=https://app.example/callback

Modern:
GET /authorize?response_type=code&client_id=spa&code_challenge=...&code_challenge_method=S256
TEXT,
        ],
        [
            'slug' => 'graphql-rest-decision-workbench',
            'track' => 'api-integration',
            'title' => 'Choose GraphQL or REST from API constraints',
            'objective' => 'Compare REST endpoint contracts with GraphQL schema/query contracts and produce a practical implementation plan.',
            'why' => 'This practices API architecture tradeoffs: endpoint versus schema contracts, client-selected fields, overfetching, HTTP cache semantics, resolver boundaries, N+1 risk, query-cost limits, field-level authorization, Laravel service boundaries, review checks, and interview-ready explanation.',
            'steps' => [
                'Create a Form Request for API design signals.',
                'Create a service that recommends REST or GraphQL from client shape, cache needs, relationship depth, team maturity, and authorization complexity.',
                'Return decision matrix, risk score, contract shape, Laravel boundaries, caching plan, authorization plan, N+1 plan, implementation phases, review checklist, tests, and interview answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench with scenario presets.',
                'Write feature and unit tests for GraphQL, REST, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanGraphqlRestDecisionRequest.php',
                'app/Http/Controllers/Api/GraphqlRestDecisionController.php',
                'app/Services/Practice/GraphqlRestDecisionService.php',
                'resources/views/practice/workbench/graphql-rest-decision.blade.php',
                'tests/Feature/GraphqlRestDecisionWorkbenchTest.php',
                'tests/Unit/Practice/GraphqlRestDecisionServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter GraphqlRestDecision',
                'php artisan route:list --path=graphql-rest-decision',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the GraphQL REST decision workbench',
                'route' => 'practice.workbench.graphql-rest-decision',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/graphql-rest-decision',
                'payload' => [
                    'client_type' => 'spa-dashboard',
                    'data_shape' => 'screen-composition',
                    'field_flexibility' => 'high',
                    'cache_priority' => 'low',
                    'relationship_depth' => 'deep',
                    'team_graphql_experience' => 'some',
                    'authorization_complexity' => 'medium',
                ],
            ],
            'acceptance' => [
                'Graph-shaped dashboard scenarios recommend GraphQL with resolver, cache, authorization, and query-cost guardrails.',
                'Public CRUD APIs with high cache priority recommend REST with HTTP contract controls.',
                'The plan includes decision matrix, risk score, contract shape, Laravel boundaries, caching plan, authorization plan, N+1 plan, implementation phases, review checklist, tests, and interview answer.',
            ],
            'starter_code' => <<<'TEXT'
REST:
GET /api/projects?include=owner,stats&fields=id,title,status

GraphQL:
query Dashboard { viewer { id name teams { id name projects { id title } } } }
TEXT,
        ],
        [
            'slug' => 'graph-traversal-plan-workbench',
            'track' => 'testing-quality',
            'title' => 'Plan BFS and DFS traversal decisions',
            'objective' => 'Compare BFS and DFS for API crawling, dependency graphs, tree menus, and database hierarchy queries.',
            'why' => 'This practices algorithm choice as production engineering: queue versus stack, shortest unweighted paths, visited sets, cycle safety, max depth, fan-out limits, pagination, memory pressure, and tests that prove traversal behavior.',
            'steps' => [
                'Identify whether the goal needs nearest result, shortest unweighted path, full branch exploration, or subtree validation.',
                'Choose BFS when level order, fewest hops, or nearest match matters.',
                'Choose DFS when branch exploration, dependency reasoning, recursive validation, or backtracking matters.',
                'Add visited-set, max-depth, max-node, pagination, and rate-limit guardrails before touching real APIs or database hierarchies.',
                'Write tests for cycles, depth limits, fan-out limits, and expected traversal order.',
            ],
            'files' => [
                'app/Services/Practice/GraphTraversalPlanService.php',
                'app/Http/Requests/Api/PlanGraphTraversalRequest.php',
                'resources/views/practice/workbench/graph-traversal-plan.blade.php',
                'tests/Feature/GraphTraversalPlanWorkbenchTest.php',
                'tests/Unit/Practice/GraphTraversalPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter GraphTraversalPlanWorkbenchTest',
                'php artisan route:list --path=graph-traversal-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the traversal planning workbench',
                'route' => 'practice.workbench.graph-traversal-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/graph-traversal-plan',
                'payload' => [
                    'scenario_name' => 'API Resource Crawl',
                    'goal' => 'nearest-match',
                    'graph_shape' => 'api-links',
                    'node_count' => 500,
                    'max_depth' => 4,
                    'weighted_edges' => false,
                    'production_context' => 'api-crawling',
                ],
            ],
            'acceptance' => [
                'Nearest or shortest unweighted path cases choose BFS.',
                'Branch exploration or nested subtree validation cases choose DFS.',
                'The plan names queue, stack or recursion, visited set, cycle checks, depth limits, fan-out limits, API pagination, and database hierarchy risk.',
                'Tests cover traversal order, cycle protection, and failure boundaries.',
            ],
            'starter_code' => <<<'PHP'
$visited = [$start => true];
$queue = [$start]; // BFS frontier for nearest or shortest unweighted path.

while ($queue !== []) {
    $node = array_shift($queue);

    foreach ($graph[$node] ?? [] as $next) {
        if (! isset($visited[$next])) {
            $visited[$next] = true;
            $queue[] = $next;
        }
    }
}
PHP,
        ],
        [
            'slug' => 'ai-hallucination-guard-plan-workbench',
            'track' => 'ai-review',
            'title' => 'Plan guardrails against AI hallucination in code',
            'objective' => 'Design evidence-first controls that stop AI-generated code or factual answers from being accepted without repo facts, source data, runtime checks, and human review.',
            'why' => 'This practices a concrete AI coding safety workflow: assumptions, evidence sources, safe output contracts, escalation policy, evidence ledger templates, runtime verification commands, claim validation rules, release readiness, score breakdown, decision matrix, data safety, prompt examples, version checks, prompt contracts, code controls, review lenses, ownership, maturity roadmap, required artifacts, CI policy, observability, rollback, red-team scenarios, automation hooks, and verification commands.',
            'steps' => [
                'Create a Form Request for AI hallucination guard input.',
                'Create a service that scores risk and returns evidence-first controls.',
                'Return safe output contracts, escalation policy, evidence ledger templates, runtime verification commands, release readiness, claim validation rules, score breakdown, decision matrix, hallucination vectors, guardrails, code controls, required artifacts, CI policy, data-safety plan, prompt examples, verification steps, observability plan, rollback playbook, ownership model, maturity roadmap, prompt contract, review checklist, red-team scenarios, automation hooks, tests, and an interview-ready answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API with scenario presets.',
                'Write feature and unit tests for high-risk generation, data-answer, controlled review, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanAiHallucinationGuardRequest.php',
                'app/Http/Controllers/Api/AiHallucinationGuardPlanController.php',
                'app/Services/Practice/AiHallucinationGuardPlanService.php',
                'resources/views/practice/workbench/ai-hallucination-guard-plan.blade.php',
                'tests/Feature/AiHallucinationGuardPlanWorkbenchTest.php',
                'tests/Unit/Practice/AiHallucinationGuardPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter AiHallucinationGuardPlan',
                'php artisan route:list --path=ai-hallucination-guard-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the AI hallucination guard workbench',
                'route' => 'practice.workbench.ai-hallucination-guard-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/ai-hallucination-guard-plan',
                'payload' => [
                    'ai_task' => 'code-generation',
                    'risk_level' => 'high',
                    'evidence_sources' => 'partial',
                    'runtime_checks' => 'yes',
                    'human_review' => 'yes',
                ],
            ],
            'acceptance' => [
                'High-risk code generation with partial evidence is scored high risk.',
                'Data-answer work without evidence requires retrieval or source IDs before showing factual answers.',
                'The plan includes summary, risk score, score breakdown, decision matrix, acceptance gate, release readiness, evidence requirements, claim validation rules, evidence ledger template, required artifacts, CI policy, data-safety plan, prompt examples, hallucination vectors, guardrails, code controls, verification plan, runtime verification commands, observability plan, rollback playbook, ownership model, escalation policy, maturity roadmap, implementation steps, red-team scenarios, automation hooks, prompt contract, safe output contract, review checklist, tests, and an interview-ready answer.',
            ],
            'starter_code' => <<<'TEXT'
Do not patch yet.
List assumptions and uncertain claims first.
For each factual claim, cite a repo file, test, log, docs page, or source row.
TEXT,
        ],
        [
            'slug' => 'ai-cloud-interview-rubric-workbench',
            'track' => 'ai-review',
            'title' => 'Score practical AI usage in Cloud Engineer interviews',
            'objective' => 'Evaluate whether a Cloud Engineer candidate really uses AI at work by scoring concrete task evidence, prompt workflow, IaC review, failure stories, verification, AWS Documentation conflicts, and team enablement.',
            'why' => 'This turns the AI interview question pack into a repeatable scoring tool. It prevents interviewers from rewarding generic AI enthusiasm and instead checks production-safe behavior: source-of-truth verification, Terraform and CloudFormation review, failure stories, and team guardrails.',
            'steps' => [
                'Create a Form Request for candidate AI usage signals.',
                'Create a service that scores practical AI maturity across interview dimensions.',
                'Return dimensions, red flags, green flags, follow-up questions, interviewer traps, calibration notes, team rollout probe, and verification commands.',
                'Expose the rubric through an API endpoint.',
                'Render a workbench that calls the API with candidate presets.',
                'Write feature and unit tests for strong, surface-level, and invalid candidate signals.',
            ],
            'files' => [
                'app/Http/Requests/Api/ScoreAiCloudInterviewRubricRequest.php',
                'app/Http/Controllers/Api/AiCloudInterviewRubricController.php',
                'app/Services/Practice/AiCloudInterviewRubricService.php',
                'resources/views/practice/workbench/ai-cloud-interview-rubric.blade.php',
                'tests/Feature/AiCloudInterviewRubricWorkbenchTest.php',
                'tests/Unit/Practice/AiCloudInterviewRubricServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter AiCloudInterviewRubric',
                'php artisan route:list --path=ai-cloud-interview-rubric',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the AI Cloud interview rubric workbench',
                'route' => 'practice.workbench.ai-cloud-interview-rubric',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/ai-cloud-interview-rubric',
                'payload' => [
                    'candidate_level' => 'senior',
                    'recent_task' => 'specific',
                    'prompt_workflow' => 'structured',
                    'iac_review' => 'production',
                    'failure_story' => 'concrete',
                    'verification_workflow' => 'systematic',
                    'docs_conflict' => 'source-of-truth',
                    'team_enablement' => 'team-standard',
                ],
            ],
            'acceptance' => [
                'Strong senior answers score as practical AI usage, not generic AI enthusiasm.',
                'Surface-level answers produce red flags for vague tasks, missing failure stories, missing verification workflow, and trusting AI over AWS Documentation.',
                'The rubric returns dimensions, red flags, green flags, follow-up questions, interviewer traps, calibration notes, team rollout probe, commands, and a hiring signal.',
            ],
            'starter_code' => <<<'TEXT'
Ask for a task from last week.
Ask what AI got wrong.
Ask what they do when AI and AWS Documentation disagree.
Score the evidence, not the enthusiasm.
TEXT,
        ],
        [
            'slug' => 'rag-strategy-plan-workbench',
            'track' => 'ai-review',
            'title' => 'Choose RAG, Long Context, CAG, or hybrid chatbot context',
            'objective' => 'Design a chatbot context strategy from RAG, Long Context, CAG, hybrid routing, knowledge shape, relationship needs, tool use, freshness, risk, answer style, readiness, data model, API response contract, OpenAPI contract, Laravel integration blueprint, CI quality gates, implementation prompt, source lifecycle, benchmark plan, test fixtures, prompt-injection tests, privacy controls, capacity plan, feedback schema, feedback loop, ownership, versioning, release evidence, audit artifacts, migration path, implementation backlog, threat model, SLO policy, ADR summary, decommission plan, cost controls, access control, evaluation, observability, failure runbooks, and rollout gates.',
            'why' => 'This turns chatbot context selection from a buzzword into an engineering decision. Learners practice when retrieval is needed, when a bounded Long Context pack is enough, when stable curated knowledge should use CAG, when hybrid routing is necessary, and which RAG pattern should back the retrieval path.',
            'steps' => [
                'Create a Form Request for RAG strategy inputs.',
                'Create a service that scores RAG, Long Context, CAG, hybrid routing, classic RAG, Graph RAG, and Agentic RAG from concrete constraints.',
                'Return a recommendation, decision matrix, readiness score, style catalog, architecture plan, data model contract, retrieval contract, answer contract, API response example, OpenAPI contract, Laravel integration blueprint, source lifecycle, evaluation plan, benchmark plan, test fixture plan, golden questions, risk controls, threat model, prompt-injection tests, access-control plan, privacy plan, SLO policy, observability plan, capacity plan, feedback loop, feedback schema, versioning policy, rollout gates, cost controls, prompt templates, failure runbook, review checklist, release checklist, evidence packet, audit artifact, CI quality gates, ownership matrix, migration path, decommission plan, implementation backlog, implementation prompt, ADR summary, anti-patterns, memo, commands, and interview answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API with RAG, Long Context, CAG, and hybrid scenario presets.',
                'Write feature and unit tests for classic document Q&A, graph-heavy knowledge, agentic operations, Long Context, CAG, hybrid routing, and invalid payloads.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanRagStrategyRequest.php',
                'app/Http/Controllers/Api/RagStrategyPlanController.php',
                'app/Services/Practice/RagStrategyPlanService.php',
                'resources/views/practice/workbench/rag-strategy-plan.blade.php',
                'tests/Feature/RagStrategyPlanWorkbenchTest.php',
                'tests/Unit/Practice/RagStrategyPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter RagStrategyPlan',
                'php artisan route:list --path=rag-strategy-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the RAG strategy workbench',
                'route' => 'practice.workbench.rag-strategy-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/rag-strategy-plan',
                'payload' => [
                    'knowledge_shape' => 'entities',
                    'relationship_need' => 'high',
                    'tool_use' => 'retrieval-tools',
                    'freshness' => 'periodic',
                    'risk_level' => 'high',
                    'answer_style' => 'citations',
                ],
            ],
            'acceptance' => [
                'Document Q&A recommends classic RAG with source IDs and groundedness checks.',
                'Entity and relationship-heavy knowledge recommends Graph RAG with edge provenance.',
                'Workflow and real-time tool-use scenarios recommend Agentic RAG with iteration and tool controls.',
                'The plan explains retrieval quality, readiness, data fields, answer fields, API shape, OpenAPI contract, Laravel files, CI gates, implementation prompt, source lifecycle, benchmarks, fixtures, prompt-injection tests, privacy, capacity, feedback labels, ownership, release evidence, replay versioning, audit trail, migration triggers, implementation backlog, threat model, SLOs, ADR summary, decommission path, citation coverage, fallback behavior, hallucination controls, permission filters, cost controls, monitoring signals, prompt templates, failure response, and rollout gates.',
            ],
            'starter_code' => <<<'TEXT'
classic RAG: retrieve chunks -> answer with citations
Graph RAG: retrieve chunks + traverse entities/edges -> answer with provenance
Agentic RAG: plan -> call tools/retrievers -> inspect -> answer with final evidence check
TEXT,
        ],
        [
            'slug' => 'llm-decision-loop-plan-workbench',
            'track' => 'ai-review',
            'title' => 'Model LLMs as decision loops from probability to reward',
            'objective' => 'Explain how large language models move from next-token prediction to repeated decisions shaped by attention, reward models, PPO, tools, feedback loops, and careful AGI claims.',
            'why' => 'This practices AI foundations with engineering discipline: Markov decision process framing, state/policy/action/transition/reward mapping, attention, supervised fine-tuning, reward modeling, PPO, tool feedback, misconception checks, verification questions, and AGI claim guardrails.',
            'steps' => [
                'Create a Form Request for LLM decision-loop input.',
                'Create a service that maps model behavior to state, policy, action, transition, and reward.',
                'Return attention guidance, training-stack roles, reward policy, PPO explanation, feedback loops, AGI guardrails, verification checks, misconceptions, commands, and an interview-ready answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API with scenario presets.',
                'Write feature and unit tests for tool-backed coding agents, chat-only explanations, overstated AGI claims, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanLlmDecisionLoopRequest.php',
                'app/Http/Controllers/Api/LlmDecisionLoopPlanController.php',
                'app/Services/Practice/LlmDecisionLoopPlanService.php',
                'resources/views/practice/workbench/llm-decision-loop-plan.blade.php',
                'tests/Feature/LlmDecisionLoopPlanWorkbenchTest.php',
                'tests/Unit/Practice/LlmDecisionLoopPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter LlmDecisionLoopPlan',
                'php artisan route:list --path=llm-decision-loop-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the LLM decision-loop workbench',
                'route' => 'practice.workbench.llm-decision-loop-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/llm-decision-loop-plan',
                'payload' => [
                    'model_role' => 'coding-assistant',
                    'training_stage' => 'rlhf',
                    'feedback_signal' => 'tests-runtime',
                    'tool_use' => 'yes',
                    'agi_claim' => 'conservative',
                ],
            ],
            'acceptance' => [
                'The plan separates next-token probability from the full state, policy, action, transition, and reward loop.',
                'Attention is explained as context routing rather than proof of human-like understanding.',
                'Reward models and PPO are tied to explicit feedback signals and their limitations.',
                'AGI claims are guarded with uncertainty and verification language.',
            ],
            'starter_code' => <<<'TEXT'
state(context) -> policy(model) -> action(token/tool) -> transition(updated context) -> reward(feedback)

Do not claim AGI from fluency alone.
Name the feedback loop that corrects the model or workflow.
TEXT,
        ],
        [
            'slug' => 'ai-agent-memory-plan-workbench',
            'track' => 'ai-review',
            'title' => 'Design governed memory contracts for AI agents',
            'objective' => 'Separate AI agent memory into working, episodic, semantic, and procedural contracts with source, freshness, confidence, permission, retention, privacy, and correction guardrails.',
            'why' => 'This turns agent memory from a vague prompt-history idea into reviewable engineering behavior. Learners practice when memory may be read, when it must be refreshed, and how stale or private context is blocked before it can steer the next action.',
            'steps' => [
                'Create a Form Request for AI agent memory inputs.',
                'Create a service that returns four memory contracts and governance controls.',
                'Return retrieval order, failure modes, commands, and an interview-ready answer.',
                'Expose the plan through an API endpoint.',
                'Render a workbench that calls the API with developer, support, and research presets.',
                'Write feature and unit tests for strict developer memory, session-only memory, stale memory handling, and validation cases.',
            ],
            'files' => [
                'app/Http/Requests/Api/PlanAiAgentMemoryRequest.php',
                'app/Http/Controllers/Api/AiAgentMemoryPlanController.php',
                'app/Services/Practice/AiAgentMemoryPlanService.php',
                'resources/views/practice/workbench/ai-agent-memory-plan.blade.php',
                'tests/Feature/AiAgentMemoryPlanWorkbenchTest.php',
                'tests/Unit/Practice/AiAgentMemoryPlanServiceTest.php',
            ],
            'commands' => [
                'php artisan test --filter AiAgentMemoryPlan',
                'php artisan route:list --path=ai-agent-memory-plan',
                'vendor\\bin\\pint --test',
            ],
            'workbench' => [
                'label' => 'Run the AI agent memory workbench',
                'route' => 'practice.workbench.ai-agent-memory-plan',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/ai-agent-memory-plan',
                'payload' => [
                    'agent_profile' => 'developer-agent',
                    'storage_scope' => 'project-scoped',
                    'retention_policy' => 'reviewed-durable',
                    'privacy_mode' => 'strict',
                    'staleness_policy' => 'block-stale',
                ],
            ],
            'acceptance' => [
                'The plan separates working, episodic, semantic, and procedural memory instead of treating memory as one prompt bucket.',
                'Every durable memory item requires source, freshness, confidence, permission scope, retention, and correction metadata.',
                'Private episodic memory and stale semantic memory cannot silently steer the next agent action.',
                'The workbench returns retrieval order, governance controls, failure modes, commands, and an interview-ready answer.',
            ],
            'starter_code' => <<<'TEXT'
working_memory: current task state
episodic_memory: prior sessions and decisions
semantic_memory: durable repo facts
procedural_memory: reviewed playbooks

No memory may steer an action without source, freshness, confidence, permission, and correction metadata.
TEXT,
        ],
        [
            'slug' => 'practice-progress-checklist',
            'track' => 'testing-quality',
            'title' => 'Build a practice progress checklist API',
            'objective' => 'Create an API that accepts checklist items, calculates completion, and reports the next unfinished task.',
            'why' => 'This practices state transition logic without database complexity: validated input, service calculation, predictable response shape, and tests.',
            'steps' => [
                'Create a service that summarizes checklist items.',
                'Create a Form Request for nested array validation.',
                'Create a thin API controller that calls the service.',
                'Write unit tests for partial and complete progress.',
                'Write API tests for success and validation failure.',
            ],
            'files' => [
                'app/Services/Practice/PracticeProgressChecklistService.php',
                'app/Http/Requests/Api/SummarizePracticeProgressRequest.php',
                'app/Http/Controllers/Api/PracticeProgressChecklistController.php',
                'tests/Unit/Practice/PracticeProgressChecklistServiceTest.php',
                'tests/Feature/PracticeProgressChecklistApiTest.php',
            ],
            'commands' => [
                'php artisan test --filter PracticeProgressChecklist',
                'vendor/bin/pint --test',
            ],
            'api' => [
                'method' => 'POST',
                'path' => '/api/practice/progress-checklist',
                'payload' => [
                    'items' => [
                        ['label' => 'Create Form Request', 'done' => true],
                        ['label' => 'Create API Controller', 'done' => true],
                        ['label' => 'Run feature test', 'done' => false],
                    ],
                ],
            ],
            'acceptance' => [
                'Partial progress returns `in-progress`.',
                'Completed progress returns `complete`.',
                'Invalid nested item payloads return 422.',
                'Service tests cover calculation without HTTP.',
            ],
            'starter_code' => <<<'PHP'
final class PracticeProgressChecklistService
{
    public function summarize(array $items): array
    {
        return [
            'status' => 'in-progress',
            'completed' => 0,
            'total' => count($items),
        ];
    }
}
PHP,
        ],
        [
            'slug' => 'docker-compose-smoke-check',
            'track' => 'docker-runtime',
            'title' => 'Verify the Docker runtime path',
            'objective' => 'Use Docker Compose config and smoke checks to prove the hub can run in a reproducible container environment.',
            'why' => 'This practices environment thinking, runtime config, ports, mounted content, and operational verification.',
            'steps' => [
                'Run `docker-compose config` from `hub/`.',
                'Review the mounted `../data` path and exposed port.',
                'Start the service when Docker Desktop is running.',
                'Call `/up` and `/practice` from the browser or terminal.',
                'Call `/api/practice/runtime-smoke-check` to verify the Laravel-side runtime assumptions.',
            ],
            'files' => [
                'Dockerfile',
                'docker-compose.yml',
                'docker/entrypoint.sh',
                '.env.example',
                'app/Services/Practice/RuntimeSmokeCheckService.php',
                'app/Http/Controllers/Api/RuntimeSmokeCheckController.php',
                'tests/Feature/RuntimeSmokeCheckApiTest.php',
            ],
            'commands' => [
                'docker-compose config',
                'docker-compose up --build',
                'curl '.$runtimeBaseUrl.'/up',
                'curl '.$runtimeBaseUrl.'/api/practice/runtime-smoke-check',
            ],
            'api' => [
                'method' => 'GET',
                'path' => '/api/practice/runtime-smoke-check',
                'payload' => [
                    'note' => 'No request body. Inspect data.checks and data.values.',
                ],
            ],
            'acceptance' => [
                'Compose config validates.',
                'The app responds on the expected port.',
                'Environment defaults are documented.',
                'No host-only PHP extension is required.',
                'The runtime smoke-check API reports `ready` for file-backed local settings.',
            ],
            'starter_code' => <<<'TEXT'
docker-compose config
docker-compose up --build
TEXT,
        ],
    ],
];

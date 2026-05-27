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
                'php artisan test --filter ReportExportTest',
                'php artisan route:list --path=reports',
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
                    'contract_name' => 'Report Exporter',
                    'implementation_name' => 'CSV Report Exporter',
                    'lifetime' => 'bind',
                    'injection_target' => 'Report Controller',
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

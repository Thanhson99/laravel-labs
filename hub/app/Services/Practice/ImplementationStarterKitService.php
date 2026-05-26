<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ImplementationStarterKitService
{
    /**
     * Create starter snippets from guided implementation checklists.
     */
    public function __construct(private readonly GuidedImplementationChecklistService $checklists) {}

    /**
     * Build starter code snippets for one content-backed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $checklist = $this->checklists->build($filters);

        if ($checklist === null) {
            return null;
        }

        $names = $checklist['blueprint']['names'];
        $technology = $checklist['blueprint']['drill']['technology'];

        return [
            'title' => sprintf('Starter Kit: %s', $checklist['blueprint']['drill']['content']['title']),
            'checklist' => $checklist,
            'snippets' => $this->snippetsFor($technology, $names),
            'usage' => [
                'Create the listed files manually in this Laravel project.',
                'Paste the matching snippet into each file.',
                'Replace starter assertions and return data with the behavior required by the source record.',
                'Run the focused test first, then the full suite and Pint.',
            ],
        ];
    }

    /**
     * Build starter snippets for one technology.
     *
     * @param  array<string, string>  $names
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function snippetsFor(string $technology, array $names): array
    {
        if ($technology === 'api-validation') {
            return [
                [
                    'label' => 'Feature test',
                    'file' => $names['test_file'],
                    'code' => $this->apiTestSnippet($names),
                ],
                [
                    'label' => 'Form request',
                    'file' => $names['request_file'],
                    'code' => $this->requestSnippet($names),
                ],
                [
                    'label' => 'Controller',
                    'file' => $names['controller_file'],
                    'code' => $this->apiControllerSnippet($names),
                ],
                [
                    'label' => 'Service',
                    'file' => $names['service_file'],
                    'code' => $this->serviceSnippet($names),
                ],
            ];
        }

        return [
            [
                'label' => 'Feature test',
                'file' => $names['test_file'],
                'code' => $this->webTestSnippet($names),
            ],
            [
                'label' => 'Service',
                'file' => $names['service_file'],
                'code' => $this->serviceSnippet($names),
            ],
        ];
    }

    /**
     * Return a starter API feature test.
     */
    private function apiTestSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\\Feature;

use Tests\\TestCase;

final class {$names['test_class']} extends TestCase
{
    public function test_endpoint_returns_expected_response(): void
    {
        \$response = \$this->postJson('{$names['route_path']}', [
            'title' => 'Practice from content record',
        ]);

        \$response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Practice from content record');
    }
}
PHP;
    }

    /**
     * Return a starter Form Request.
     */
    private function requestSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Http\\Requests\\Api;

use Illuminate\\Foundation\\Http\\FormRequest;

final class {$names['request_class']} extends FormRequest
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
PHP;
    }

    /**
     * Return a starter API controller.
     */
    private function apiControllerSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Http\\Controllers\\Api;

use App\\Http\\Controllers\\Controller;
use App\\Http\\Requests\\Api\\{$names['request_class']};
use App\\Services\\Practice\\{$names['service_class']};
use Illuminate\\Http\\JsonResponse;

final class {$names['controller_class']} extends Controller
{
    public function __invoke({$names['request_class']} \$request, {$names['service_class']} \$service): JsonResponse
    {
        return response()->json([
            'data' => \$service->handle(\$request->validated()),
        ], 201);
    }
}
PHP;
    }

    /**
     * Return a starter service.
     */
    private function serviceSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Services\\Practice;

final class {$names['service_class']}
{
    public function handle(array \$data): array
    {
        return [
            'title' => (string) (\$data['title'] ?? ''),
        ];
    }
}
PHP;
    }

    /**
     * Return a starter web feature test.
     */
    private function webTestSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\\Feature;

use Tests\\TestCase;

final class {$names['test_class']} extends TestCase
{
    public function test_page_renders_expected_content(): void
    {
        \$response = \$this->get('{$names['route_path']}');

        \$response
            ->assertOk()
            ->assertSee('Practice');
    }
}
PHP;
    }
}

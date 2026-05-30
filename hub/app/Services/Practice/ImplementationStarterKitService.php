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
        $drill = $checklist['blueprint']['drill'];

        return [
            'title' => sprintf('Starter Kit: %s', $checklist['blueprint']['drill']['content']['title']),
            'checklist' => $checklist,
            'snippets' => $this->snippetsFor($technology, $names, $drill),
            'usage' => $this->usageFor($technology, $drill),
        ];
    }

    /**
     * Build starter snippets for one technology.
     *
     * @param  array<string, string>  $names
     * @return array<int, array{label: string, file: string, code: string}>
     */
    private function snippetsFor(string $technology, array $names, array $drill): array
    {
        if ($technology === 'idor-access-control') {
            return [
                [
                    'label' => 'Feature test',
                    'file' => $names['test_file'],
                    'code' => $this->idorTestSnippet($names),
                ],
                [
                    'label' => 'Service',
                    'file' => $names['service_file'],
                    'code' => $this->idorServiceSnippet($names),
                ],
            ];
        }

        if ($technology === 'javascript-closures') {
            if ($this->isArrowThisDrill($drill)) {
                return [
                    [
                        'label' => 'Feature test',
                        'file' => $names['test_file'],
                        'code' => $this->arrowThisTestSnippet($names),
                    ],
                    [
                        'label' => 'Service',
                        'file' => $names['service_file'],
                        'code' => $this->arrowThisServiceSnippet($names),
                    ],
                ];
            }

            return [
                [
                    'label' => 'Feature test',
                    'file' => $names['test_file'],
                    'code' => $this->closureTestSnippet($names),
                ],
                [
                    'label' => 'Service',
                    'file' => $names['service_file'],
                    'code' => $this->closureServiceSnippet($names),
                ],
            ];
        }

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
     * Return usage steps for one technology.
     *
     * @return array<int, string>
     */
    private function usageFor(string $technology, array $drill): array
    {
        if ($technology === 'idor-access-control') {
            return [
                'Create the listed files manually in this Laravel project.',
                'Paste the IDOR feature test and object-authorization service snippets.',
                'Replace sample invoice data with the source record object route, owner scope, and policy check.',
                'Run the focused IDOR test first, then the full suite and Pint.',
            ];
        }

        if ($technology === 'javascript-closures') {
            if ($this->isArrowThisDrill($drill)) {
                return [
                    'Create the listed files manually in this Laravel project.',
                    'Paste the arrow-this feature test and comparison service snippets.',
                    'Replace starter evidence with the behavior required by the source record.',
                    'Run the focused arrow-this test first, then the full suite and Pint.',
                ];
            }

            return [
                'Create the listed files manually in this Laravel project.',
                'Paste the closure feature test and evidence service snippets.',
                'Replace starter closure evidence with the behavior required by the source record.',
                'Run the focused closure test first, then the full suite and Pint.',
            ];
        }

        return [
            'Create the listed files manually in this Laravel project.',
            'Paste the matching snippet into each file.',
            'Replace starter assertions and return data with the behavior required by the source record.',
            'Run the focused test first, then the full suite and Pint.',
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
     * Return a starter closure evidence feature test.
     */
    private function closureTestSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\\Feature;

use Tests\\TestCase;

final class {$names['test_class']} extends TestCase
{
    public function test_page_renders_closure_evidence(): void
    {
        \$response = \$this->get('{$names['route_path']}');

        \$response
            ->assertOk()
            ->assertSee('lexical scope')
            ->assertSee('captured binding')
            ->assertSee('createCounter')
            ->assertSee('stale closure');
    }
}
PHP;
    }

    /**
     * Return a starter closure evidence service.
     */
    private function closureServiceSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Services\\Practice;

final class {$names['service_class']}
{
    public function handle(): array
    {
        return [
            'definition' => 'A closure is a function plus access to variables from its lexical scope.',
            'captured_binding' => 'createCounter() keeps the count binding reachable between calls.',
            'trace' => ['counter() returns 1', 'counter() returns 2'],
            'interview_traps' => ['var versus let in loops', 'stale closure in async callbacks or hooks'],
        ];
    }
}
PHP;
    }

    /**
     * Return a starter arrow-function this feature test.
     */
    private function arrowThisTestSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\\Feature;

use Tests\\TestCase;

final class {$names['test_class']} extends TestCase
{
    public function test_page_renders_arrow_this_evidence(): void
    {
        \$response = \$this->get('{$names['route_path']}');

        \$response
            ->assertOk()
            ->assertSee('lexical this')
            ->assertSee('dynamic this')
            ->assertSee('obj.arrow() trap')
            ->assertSee('call/apply/bind cannot rebind arrow this');
    }
}
PHP;
    }

    /**
     * Return a starter arrow-function this evidence service.
     */
    private function arrowThisServiceSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Services\\Practice;

final class {$names['service_class']}
{
    public function handle(): array
    {
        return [
            'definition' => 'Arrow functions use lexical this from the scope where they are created.',
            'normal_function' => 'dynamic this comes from the call site, such as obj.normalMethod().',
            'arrow_function' => 'lexical_this ignores obj.arrow() as a rebinding attempt.',
            'callback_use_case' => 'timer callbacks and class callbacks can keep outer this with an arrow function.',
            'interview_traps' => ['obj.arrow() trap', 'call/apply/bind cannot rebind arrow this'],
        ];
    }
}
PHP;
    }

    /**
     * Return a starter IDOR feature test.
     */
    private function idorTestSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\\Feature;

use Tests\\TestCase;

final class {$names['test_class']} extends TestCase
{
    public function test_page_renders_idor_review_evidence(): void
    {
        \$response = \$this->get('{$names['route_path']}');

        \$response
            ->assertOk()
            ->assertSee('object-level authorization')
            ->assertSee('scoped lookup')
            ->assertSee('policy check')
            ->assertSee('ID-swap denial test');
    }

}
PHP;
    }

    /**
     * Return a starter IDOR evidence service.
     */
    private function idorServiceSnippet(array $names): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Services\\Practice;

final class {$names['service_class']}
{
    public function handle(): array
    {
        return [
            'definition' => 'IDOR is broken object-level authorization, not a login failure.',
            'object_surface' => [
                'route' => 'GET /api/invoices/{invoice}',
                'identifier' => 'invoice id',
                'owner_scope' => 'tenant + owner',
            ],
            'secure_pattern' => [
                'scoped_lookup' => '\$request->user()->invoices()->whereKey(\$invoiceId)->firstOrFail()',
                'policy_check' => '\$this->authorize("view", \$invoice)',
            ],
            'denial_tests' => [
                'user A cannot read user B invoice',
                'user A cannot update user B invoice',
                'user A cannot download user B invoice PDF',
                'user A cannot export user B invoice data',
            ],
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

    /**
     * Detect arrow-function `this` content inside a JavaScript closure drill.
     *
     * @param  array<string, mixed>  $drill
     */
    private function isArrowThisDrill(array $drill): bool
    {
        if (($drill['technology'] ?? null) !== 'javascript-closures') {
            return false;
        }

        $haystack = strtolower(implode(' ', [
            $drill['content']['title'] ?? '',
            $drill['content']['body'] ?? '',
            $drill['goal'] ?? '',
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }
}

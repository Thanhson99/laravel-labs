<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class ContentImplementationBlueprintService
{
    /**
     * Create implementation blueprints from content-backed drills.
     */
    public function __construct(private readonly ContentPracticeDrillService $drills) {}

    /**
     * Build concrete implementation names from one content record.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $drill = $this->drills->build($filters);

        if ($drill === null) {
            return null;
        }

        $baseName = $this->baseName((string) $drill['content']['title']);

        return [
            'title' => sprintf('Implementation Blueprint: %s', $baseName),
            'drill' => $drill,
            'names' => $this->namesFor($drill['technology'], $baseName),
            'sequence' => [
                'Write the focused test first.',
                'Create the request/service/controller files named in this blueprint.',
                'Implement only enough behavior to satisfy the content record.',
                'Run the focused test, full test suite, and Pint.',
            ],
            'commands' => [
                sprintf('php artisan test --filter %s', $this->namesFor($drill['technology'], $baseName)['test_class']),
                'php artisan test',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Generate class, route, and file names for one technology.
     *
     * @return array<string, string>
     */
    private function namesFor(string $technology, string $baseName): array
    {
        $studly = Str::studly($baseName);
        $kebab = Str::kebab($baseName);

        return match ($technology) {
            'api-validation' => [
                'route_method' => 'POST',
                'route_path' => sprintf('/api/practice/%s', $kebab),
                'request_class' => sprintf('Store%sRequest', $studly),
                'controller_class' => sprintf('%sController', $studly),
                'service_class' => sprintf('%sService', $studly),
                'test_class' => sprintf('%sApiTest', $studly),
                'request_file' => sprintf('app/Http/Requests/Api/Store%sRequest.php', $studly),
                'controller_file' => sprintf('app/Http/Controllers/Api/%sController.php', $studly),
                'service_file' => sprintf('app/Services/Practice/%sService.php', $studly),
                'test_file' => sprintf('tests/Feature/%sApiTest.php', $studly),
            ],
            'testing-quality' => [
                'route_method' => 'GET',
                'route_path' => sprintf('/practice/%s', $kebab),
                'service_class' => sprintf('%sService', $studly),
                'test_class' => sprintf('%sTest', $studly),
                'service_file' => sprintf('app/Services/Practice/%sService.php', $studly),
                'test_file' => sprintf('tests/Feature/%sTest.php', $studly),
            ],
            default => [
                'route_method' => 'GET',
                'route_path' => sprintf('/practice/%s', $kebab),
                'controller_class' => sprintf('%sController', $studly),
                'service_class' => sprintf('%sService', $studly),
                'view_file' => sprintf('resources/views/practice/%s.blade.php', $kebab),
                'controller_file' => sprintf('app/Http/Controllers/Practice/%sController.php', $studly),
                'service_file' => sprintf('app/Services/Practice/%sService.php', $studly),
                'test_class' => sprintf('%sTest', $studly),
                'test_file' => sprintf('tests/Feature/%sTest.php', $studly),
            ],
        };
    }

    /**
     * Build a stable base name from a question title.
     */
    private function baseName(string $title): string
    {
        $withoutNumber = preg_replace('/^\d+\.\s*/', '', $title) ?: $title;
        $words = Str::of($withoutNumber)
            ->replaceMatches('/[^A-Za-z0-9\s]/', ' ')
            ->lower()
            ->squish()
            ->words(8, '');

        return (string) ($words->isEmpty() ? 'PracticeTask' : $words);
    }
}

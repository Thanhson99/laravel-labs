<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\ContentPracticeStarterSnippetService;
use PHPUnit\Framework\TestCase;

final class ContentPracticeStarterSnippetServiceTest extends TestCase
{
    /**
     * API validation drills include the full route, test, request, controller, and service slice.
     */
    public function test_api_validation_snippets_cover_full_laravel_slice(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'api-validation',
        ]);

        $this->assertSame('routes/api/practice-actions.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/ContentBackedApiDrillTest.php', $snippets[1]['file']);
        $this->assertSame('app/Http/Requests/Api/StoreContentBackedDrillRequest.php', $snippets[2]['file']);
        $this->assertSame('app/Http/Controllers/Api/ContentBackedApiDrillController.php', $snippets[3]['file']);
        $this->assertSame('app/Services/Practice/ContentBackedApiDrillService.php', $snippets[4]['file']);
        $this->assertStringContainsString('assertJsonValidationErrors', $snippets[1]['code']);
    }

    /**
     * PHP drills stay focused on pure service code and unit tests.
     */
    public function test_php_snippets_cover_pure_php_service_and_unit_test(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'php',
        ]);

        $this->assertSame('app/Practice/Php/ContentBackedNormalizer.php', $snippets[0]['file']);
        $this->assertSame('tests/Unit/Practice/ContentBackedNormalizerTest.php', $snippets[1]['file']);
        $this->assertStringContainsString('declare(strict_types=1);', $snippets[0]['code']);
    }

    /**
     * Web drills include route, controller, service, view, and a feature test.
     */
    public function test_default_snippets_cover_web_slice(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'laravel-http',
        ]);

        $this->assertSame('routes/web/practice-content.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/ContentBackedWebDrillTest.php', $snippets[1]['file']);
        $this->assertSame('app/Http/Controllers/Practice/ContentBackedWebDrillController.php', $snippets[2]['file']);
        $this->assertSame('app/Services/Practice/ContentBackedDrillService.php', $snippets[3]['file']);
        $this->assertSame('resources/views/practice/content-backed-drill.blade.php', $snippets[4]['file']);
        $this->assertStringContainsString("@extends('learning.layout'", $snippets[4]['code']);
    }

    /**
     * Laravel technology snippets turn content records into concrete code areas.
     */
    public function test_laravel_technology_snippets_cover_specialized_code_paths(): void
    {
        $service = new ContentPracticeStarterSnippetService;

        $this->assertSame('app/Policies/PracticeTaskPolicy.php', $service->snippetsFor(['technology' => 'auth-security'])[0]['file']);
        $this->assertSame('database/migrations/xxxx_xx_xx_create_practice_tasks_table.php', $service->snippetsFor(['technology' => 'database-eloquent'])[0]['file']);
        $this->assertSame('app/Http/Requests/Api/StorePracticeUploadRequest.php', $service->snippetsFor(['technology' => 'files-media'])[0]['file']);
        $this->assertSame('app/Events/PracticeTaskCompleted.php', $service->snippetsFor(['technology' => 'async-workflow'])[0]['file']);
        $this->assertSame('app/Services/Practice/PracticeDashboardSummaryService.php', $service->snippetsFor(['technology' => 'performance-cache'])[0]['file']);
        $this->assertSame('app/Contracts/PracticeTaskReporter.php', $service->snippetsFor(['technology' => 'container-architecture'])[0]['file']);
        $this->assertSame('app/Events/PracticeProgressUpdated.php', $service->snippetsFor(['technology' => 'realtime-events'])[0]['file']);
        $this->assertSame('resources/views/practice/card.blade.php', $service->snippetsFor(['technology' => 'blade-ui'])[1]['file']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyCommitPlanTest extends TestCase
{
    /**
     * The technology commit plan page renders branch, files, verification, and review evidence.
     */
    public function test_technology_commit_plan_page_renders_commit_artifacts(): void
    {
        $response = $this->get('/practice/technology-commit-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Commit Plan: api-validation')
            ->assertSee('practice/api-validation/content-backed-implementation')
            ->assertSee('practice: implement api-validation content-backed lab')
            ->assertSee('routes/api/practice-actions.php')
            ->assertSee('php artisan test --filter ContentBackedApiDrill')
            ->assertSee('Evidence Checklist')
            ->assertSee('Review Checklist')
            ->assertSee('Open commit API');
    }

    /**
     * The technology commit plan API returns commit-ready artifacts from the implementation lab.
     */
    public function test_technology_commit_plan_api_returns_commit_artifacts(): void
    {
        $response = $this->getJson('/api/practice/technology-commit-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.branch', 'practice/api-validation/content-backed-implementation')
            ->assertJsonPath('data.commit_message', 'practice: implement api-validation content-backed lab')
            ->assertJsonPath('data.changed_files.0', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter ContentBackedApiDrill')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Create branch')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'branch',
                    'commit_message',
                    'lab',
                    'changed_files',
                    'verification',
                    'evidence_checklist',
                    'review_checklist',
                    'progress_payload',
                ],
            ]);
    }
}

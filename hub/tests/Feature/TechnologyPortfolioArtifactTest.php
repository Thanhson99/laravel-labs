<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPortfolioArtifactTest extends TestCase
{
    /**
     * The technology portfolio page renders a reusable learning artifact.
     */
    public function test_technology_portfolio_page_renders_source_coverage_and_readme_template(): void
    {
        $response = $this->get('/practice/technology-portfolio-artifact/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Portfolio Artifact: api-validation')
            ->assertSee('Implemented content-backed api-validation practice in Laravel')
            ->assertSee('Source Coverage')
            ->assertSee('Interview Talking Points')
            ->assertSee('README Template')
            ->assertSee('practice/api-validation/content-backed-implementation')
            ->assertSee('Open portfolio API');
    }

    /**
     * The technology portfolio API returns source coverage, talking points, and README output.
     */
    public function test_technology_portfolio_api_returns_portfolio_artifact(): void
    {
        $response = $this->getJson('/api/practice/technology-portfolio-artifact/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.portfolio.headline', 'Implemented content-backed api-validation practice in Laravel')
            ->assertJsonPath('data.portfolio.source_coverage.0.source_path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Write portfolio headline')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'commit_plan',
                    'portfolio' => [
                        'headline',
                        'summary',
                        'source_coverage',
                        'changed_files',
                        'verification',
                        'interview_talking_points',
                        'readme_template',
                    ],
                    'progress_payload',
                ],
            ]);
    }
}

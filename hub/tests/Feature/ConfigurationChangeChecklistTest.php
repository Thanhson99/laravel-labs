<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationChangeChecklistTest extends TestCase
{
    /**
     * The configuration change checklist page renders review cards and commands.
     */
    public function test_configuration_change_checklist_page_renders_review_cards(): void
    {
        $response = $this->get('/practice/configuration-change-checklist');

        $response
            ->assertOk()
            ->assertSee('Configuration Change Checklist')
            ->assertSee('Application runtime')
            ->assertSee('Authentication contract')
            ->assertSee('Quality gate contract')
            ->assertSee('php artisan config:clear')
            ->assertSee('Open checklist API');
    }

    /**
     * The configuration change checklist API returns cards and review questions.
     */
    public function test_configuration_change_checklist_api_returns_review_guidance(): void
    {
        $response = $this->getJson('/api/practice/configuration-change-checklist');

        $response
            ->assertOk()
            ->assertJsonPath('data.change_cards.0.area', 'Application runtime')
            ->assertJsonPath('data.change_cards.1.watch_values.0', 'auth.defaults.guard')
            ->assertJsonPath('data.commands.3', 'php artisan config:clear')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'source_files',
                    'change_cards' => [
                        '*' => [
                            'area',
                            'file',
                            'watch_values',
                            'impact',
                            'before_change',
                            'after_change',
                            'rollback',
                        ],
                    ],
                    'review_questions',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}

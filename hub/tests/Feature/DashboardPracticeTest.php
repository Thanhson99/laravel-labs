<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class DashboardPracticeTest extends TestCase
{
    /**
     * The landing page points learners toward code-first practice.
     */
    public function test_dashboard_guides_learning_by_building(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Code-first practice')
            ->assertSee('Practice Exercises In This Laravel Project')
            ->assertSee('Open practice workspace')
            ->assertSee('Technology Integrated For Practice');
    }
}

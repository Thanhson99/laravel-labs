<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LearningReferencePagesTest extends TestCase
{
    /**
     * The dashboard renders the aggregate integration status.
     */
    public function test_dashboard_renders_integration_status(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Technology Integrated For Practice')
            ->assertSee('Laravel practice app')
            ->assertSee('Practice Exercises In This Laravel Project');
    }

    /**
     * The question bank can filter JSON content by family and language.
     */
    public function test_question_bank_filters_json_content(): void
    {
        $response = $this->get('/questions?family=interview&language=en&search=Laravel');

        $response
            ->assertOk()
            ->assertSee('Question bank')
            ->assertSee('interview')
            ->assertSee('en');
    }

    /**
     * The question bank defaults to English records when no language is provided.
     */
    public function test_question_bank_defaults_to_english_content(): void
    {
        $response = $this->get('/questions?family=laravel&search=Sail');

        $response
            ->assertOk()
            ->assertSee('en')
            ->assertDontSee('Sail, môi trường và triển khai');
    }

    /**
     * Source pages show decoded JSON content and extracted records.
     */
    public function test_source_page_renders_decoded_json_source(): void
    {
        $response = $this->get('/sources/interview-senior-en-json');

        $response
            ->assertOk()
            ->assertSee('senior.en.json')
            ->assertSee('Extracted Records')
            ->assertSee('Raw JSON');
    }

    /**
     * The quiz page renders a generated practice set.
     */
    public function test_quiz_page_renders_practice_cards(): void
    {
        $response = $this->get('/quiz?family=laravel&language=en&search=auth&limit=5');

        $response
            ->assertOk()
            ->assertSee('Practice mode')
            ->assertSee('Open quiz API')
            ->assertSee('Build coding drill')
            ->assertSee('cards from');
    }

    /**
     * The quiz page defaults to English records when no language is provided.
     */
    public function test_quiz_page_defaults_to_english_content(): void
    {
        $response = $this->get('/quiz?family=laravel&search=Sail&limit=5');

        $response
            ->assertOk()
            ->assertSee('en')
            ->assertDontSee('Sail, môi trường và triển khai');
    }

    /**
     * The study-plan page renders generated learning modules.
     */
    public function test_study_plan_page_renders_learning_modules(): void
    {
        $response = $this->get('/study-plan?family=laravel&language=en&limit=3');

        $response
            ->assertOk()
            ->assertSee('Study plan')
            ->assertSee('Open study plan API')
            ->assertSee('modules');
    }

    /**
     * The study-plan page defaults to English sources when no language is provided.
     */
    public function test_study_plan_page_defaults_to_english_content(): void
    {
        $response = $this->get('/study-plan?family=laravel&search=Sail&limit=5');

        $response
            ->assertOk()
            ->assertSee('Sail, environments, and deployment')
            ->assertDontSee('Sail, môi trường và triển khai');
    }

    /**
     * The analytics page renders content coverage information.
     */
    public function test_analytics_page_renders_content_report(): void
    {
        $response = $this->get('/analytics');

        $response
            ->assertOk()
            ->assertSee('Analytics')
            ->assertSee('Content Groups')
            ->assertSee('Largest Sources')
            ->assertSee('Code-Heavy Sources')
            ->assertDontSee('Sail, môi trường và triển khai');
    }

    /**
     * The labs page renders hands-on practice tasks.
     */
    public function test_labs_page_renders_hands_on_tasks(): void
    {
        $response = $this->get('/labs?family=laravel&language=en&track=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Hands-on labs')
            ->assertSee('Open labs API')
            ->assertSee('Tasks')
            ->assertSee('Done When');
    }

    /**
     * The labs page defaults to English content when no language filter is provided.
     */
    public function test_labs_page_defaults_to_english_content(): void
    {
        $response = $this->get('/labs?family=laravel&track=docker-devops&limit=5');

        $response
            ->assertOk()
            ->assertSee('Docker DevOps lab')
            ->assertSee('Sail, environments, and deployment')
            ->assertDontSee('Sail, môi trường và triển khai')
            ->assertDontSee('Hoc toi dau, thuc hanh toi do');
    }

    /**
     * Default learning pages should not mix Vietnamese source titles into the English hub UI.
     */
    public function test_default_learning_pages_do_not_render_vietnamese_source_titles(): void
    {
        foreach ([
            '/analytics',
            '/questions?family=laravel&search=Sail',
            '/quiz?family=laravel&search=Sail&limit=5',
            '/study-plan?family=laravel&search=Sail&limit=5',
            '/labs?family=laravel&track=docker-devops&limit=5',
        ] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertDontSee('Sail, môi trường và triển khai')
                ->assertDontSee('từ local đồng nhất tới production sống khỏe');
        }
    }
}

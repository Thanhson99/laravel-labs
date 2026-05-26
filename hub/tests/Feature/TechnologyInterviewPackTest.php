<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyInterviewPackTest extends TestCase
{
    /**
     * The technology interview pack page renders questions, evidence, and practice script.
     */
    public function test_technology_interview_pack_page_renders_questions_and_evidence(): void
    {
        $response = $this->get('/practice/technology-interview-pack/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Interview Pack: api-validation')
            ->assertSee('How did you turn `api-validation` learning content into Laravel code?')
            ->assertSee('Evidence To Cite')
            ->assertSee('Practice Script')
            ->assertSee('practice/api-validation/content-backed-implementation')
            ->assertSee('Open interview API');
    }

    /**
     * The technology interview pack API returns questions, answer outlines, evidence, and progress data.
     */
    public function test_technology_interview_pack_api_returns_questions_and_evidence(): void
    {
        $response = $this->getJson('/api/practice/technology-interview-pack/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.questions.0.question', 'How did you turn `api-validation` learning content into Laravel code?')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Answer architecture question')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'artifact',
                    'questions' => [
                        '*' => [
                            'question',
                            'answer_outline',
                        ],
                    ],
                    'evidence_to_cite',
                    'practice_script',
                    'progress_payload',
                ],
            ]);
    }
}

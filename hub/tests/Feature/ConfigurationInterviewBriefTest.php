<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationInterviewBriefTest extends TestCase
{
    /**
     * The configuration interview brief page renders questions and evidence.
     */
    public function test_configuration_interview_brief_page_renders_questions_and_evidence(): void
    {
        $response = $this->get('/practice/configuration-interview-brief');

        $response
            ->assertOk()
            ->assertSee('Configuration Interview Brief')
            ->assertSee('How did you treat Laravel configuration as a runtime contract?')
            ->assertSee('Evidence To Cite')
            ->assertSee('Rehearsal Checklist')
            ->assertSee('Open brief API');
    }

    /**
     * The configuration interview brief API returns question outlines and evidence references.
     */
    public function test_configuration_interview_brief_api_returns_question_outlines(): void
    {
        $response = $this->getJson('/api/practice/configuration-interview-brief');

        $response
            ->assertOk()
            ->assertJsonPath('data.questions.0.question', 'How did you treat Laravel configuration as a runtime contract?')
            ->assertJsonPath('data.evidence_to_cite.1.label', '/practice/configuration-readiness')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'questions' => [
                        '*' => [
                            'question',
                            'answer_outline',
                            'follow_up',
                        ],
                    ],
                    'evidence_to_cite',
                    'rehearsal_checklist',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}

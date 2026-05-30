<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class AiCloudInterviewRubricWorkbenchTest extends TestCase
{
    /**
     * The AI Cloud interview rubric workbench renders candidate scoring controls.
     */
    public function test_ai_cloud_interview_rubric_workbench_renders(): void
    {
        $response = $this->get('/workbench/ai-cloud-interview-rubric');

        $response
            ->assertOk()
            ->assertSee('AI Cloud Interview Rubric Workbench')
            ->assertSee('POST /api/practice/ai-cloud-interview-rubric')
            ->assertSee('AiCloudInterviewRubricService')
            ->assertSee('Scenario preset')
            ->assertSee('Strong senior user')
            ->assertSee('Hiring signal')
            ->assertSee('Score breakdown')
            ->assertSee('aiCloudInterviewRubricDimensions')
            ->assertSee('Red flags')
            ->assertSee('Green flags')
            ->assertSee('Follow-up questions')
            ->assertSee('Next interview actions')
            ->assertSee('Tiered question sequence')
            ->assertSee('Hands-on challenge')
            ->assertSee('Debrief decision')
            ->assertSee('Debrief markdown')
            ->assertSee('Copy debrief markdown')
            ->assertSee('copyAiCloudInterviewRubricMarkdown')
            ->assertSee('aiCloudInterviewRubricScore')
            ->assertSee('Score interview answer');
    }

    /**
     * The API returns a strong signal for concrete, verified AI usage.
     */
    public function test_ai_cloud_interview_rubric_api_scores_strong_candidate(): void
    {
        $response = $this->postJson('/api/practice/ai-cloud-interview-rubric', [
            'candidate_level' => 'senior',
            'recent_task' => 'specific',
            'prompt_workflow' => 'structured',
            'iac_review' => 'production',
            'failure_story' => 'concrete',
            'verification_workflow' => 'systematic',
            'docs_conflict' => 'source-of-truth',
            'team_enablement' => 'team-standard',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.score', 100)
            ->assertJsonPath('data.summary.verdict', 'strong practical AI user')
            ->assertJsonPath('data.summary.hiring_signal', 'can use AI while still owning Cloud production risk')
            ->assertJsonPath('data.dimensions.3.dimension', 'AI failure story')
            ->assertJsonPath('data.green_flags.2', 'Can explain where AI failed and what evidence caught it.')
            ->assertJsonPath('data.interviewer_traps.1', 'Ask what they do when AI and AWS Documentation disagree.')
            ->assertJsonPath('data.question_sequence.0.tier', 'Warm-up')
            ->assertJsonPath('data.question_sequence.3.tier', 'Senior-level')
            ->assertJsonPath('data.hands_on_challenge.title', 'Review an AI-generated infrastructure change before apply')
            ->assertJsonPath('data.hands_on_challenge.expected_findings.0', 'Public network or public data exposure must be challenged before apply.')
            ->assertJsonPath('data.debrief_template.decision', 'advance to final hands-on Cloud review')
            ->assertJsonPath('data.debrief_template.recommended_next_step', 'Move to a final system-design or incident-response exercise.')
            ->assertJsonPath('data.debrief_markdown', "# AI Cloud Interview Debrief\n\nDecision: advance to final hands-on Cloud review\nScore: 100/100 (strong practical AI user)\nCandidate level: senior\nNext step: Move to a final system-design or incident-response exercise.\n\n## Green Flags\n- Names a concrete task, prompt shape, output edit, and result.\n- Reviews AI-generated IaC for state, IAM, network, replacement, cost, and rollback risk.\n- Can explain where AI failed and what evidence caught it.\n- Uses docs, dry runs, tests, logs, and peer review before accepting output.\n- Thinks beyond personal productivity and can standardize AI usage for a team.\n\n## Red Flags\n- None recorded.\n\n## Unresolved Risks\n- No unresolved rubric risks from selected signals.\n\n## Targeted Follow-up\n- None recorded.\n\n## Evidence Notes\n- Concrete task evidence:\n- Prompt workflow evidence:\n- IaC review findings:\n- AI failure story:\n- Verification workflow:\n- AI vs AWS Docs rule:\n- Hands-on challenge result:")
            ->assertJsonPath('data.score_bands.0.range', '78-100')
            ->assertJsonPath('data.improvement_plan', [])
            ->assertJsonPath('data.team_rollout_probe.pass_condition', 'The candidate can move from personal AI usage to a repeatable team workflow with review and verification gates.')
            ->assertJsonPath('data.commands.0', 'php artisan route:list --path=ai-cloud-interview-rubric');
    }

    /**
     * Surface-level answers expose red flags and production-readiness risk.
     */
    public function test_ai_cloud_interview_rubric_api_flags_surface_usage(): void
    {
        $response = $this->postJson('/api/practice/ai-cloud-interview-rubric', [
            'candidate_level' => 'mid',
            'recent_task' => 'vague',
            'prompt_workflow' => 'basic',
            'iac_review' => 'none',
            'failure_story' => 'none',
            'verification_workflow' => 'none',
            'docs_conflict' => 'trusts-ai',
            'team_enablement' => 'none',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.verdict', 'surface-level or risky AI usage')
            ->assertJsonPath('data.summary.hiring_signal', 'do not treat as production-ready without stronger verification discipline')
            ->assertJsonPath('data.red_flags.1', 'Has no story about AI being wrong.')
            ->assertJsonPath('data.red_flags.3', 'Lets AI outrank AWS Documentation.')
            ->assertJsonPath('data.question_sequence.3.tier', 'Trap question')
            ->assertJsonPath('data.hands_on_challenge.follow_up', 'Ask how they would catch this if AI confidently said the snippet was safe.')
            ->assertJsonPath('data.debrief_template.decision', 'block production ownership until verification and IaC review discipline improve')
            ->assertJsonPath('data.debrief_template.unresolved_risks.0', 'Candidate did not provide a concrete recent AI-assisted Cloud task.')
            ->assertJsonPath('data.improvement_plan.0.dimension', 'verification workflow')
            ->assertJsonPath('data.improvement_plan.0.evidence_to_request', 'Docs link, dry-run output, test result, log evidence, and reviewer note.');

        $markdown = $response->json('data.debrief_markdown');

        $this->assertStringContainsString('Decision: block production ownership until verification and IaC review discipline improve', $markdown);
        $this->assertStringContainsString('## Targeted Follow-up', $markdown);
        $this->assertStringContainsString('verification workflow: Ask for the exact docs, dry-run, test, log, and peer-review sequence before trusting AI output.', $markdown);
    }

    /**
     * Invalid rubric payloads return validation errors.
     */
    public function test_ai_cloud_interview_rubric_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/ai-cloud-interview-rubric', [
            'candidate_level' => 'principal',
            'recent_task' => 'maybe',
            'prompt_workflow' => 'random',
            'iac_review' => 'unsafe',
            'failure_story' => 'unclear',
            'verification_workflow' => 'guess',
            'docs_conflict' => 'ai-first',
            'team_enablement' => 'slides',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'candidate_level',
                'recent_task',
                'prompt_workflow',
                'iac_review',
                'failure_story',
                'verification_workflow',
                'docs_conflict',
                'team_enablement',
            ]);
    }
}

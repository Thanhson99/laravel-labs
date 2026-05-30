<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\AiCloudInterviewRubricService;
use PHPUnit\Framework\TestCase;

final class AiCloudInterviewRubricServiceTest extends TestCase
{
    /**
     * Strong candidates score well because they provide concrete evidence and verification.
     */
    public function test_scores_strong_candidate_as_practical_ai_user(): void
    {
        $result = (new AiCloudInterviewRubricService)->score([
            'candidate_level' => 'senior',
            'recent_task' => 'specific',
            'prompt_workflow' => 'structured',
            'iac_review' => 'production',
            'failure_story' => 'concrete',
            'verification_workflow' => 'systematic',
            'docs_conflict' => 'source-of-truth',
            'team_enablement' => 'team-standard',
        ]);

        $this->assertSame(100, $result['summary']['score']);
        $this->assertSame('strong practical AI user', $result['summary']['verdict']);
        $this->assertContains('Reviews AI-generated IaC for state, IAM, network, replacement, cost, and rollback risk.', $result['green_flags']);
        $this->assertSame([], $result['improvement_plan']);
        $this->assertSame('78-100', $result['score_bands'][0]['range']);
        $this->assertSame('Senior-level', $result['question_sequence'][3]['tier']);
        $this->assertStringContainsString('CLAUDE.md', $result['question_sequence'][3]['questions'][0]);
        $this->assertStringContainsString('CloudFormation', $result['hands_on_challenge']['artifact']);
        $this->assertContains('Terraform plan or CloudFormation change set', $result['hands_on_challenge']['evidence_to_request']);
        $this->assertSame('advance to final hands-on Cloud review', $result['debrief_template']['decision']);
        $this->assertStringContainsString('Hands-on challenge result:', $result['debrief_template']['interviewer_note_template']);
        $this->assertStringContainsString('# AI Cloud Interview Debrief', $result['debrief_markdown']);
        $this->assertStringContainsString('Score: 100/100 (strong practical AI user)', $result['debrief_markdown']);
    }

    /**
     * Trusting AI over docs blocks production readiness even if some answers sound useful.
     */
    public function test_flags_ai_over_docs_as_production_risk(): void
    {
        $result = (new AiCloudInterviewRubricService)->score([
            'candidate_level' => 'mid',
            'recent_task' => 'specific',
            'prompt_workflow' => 'basic',
            'iac_review' => 'basic',
            'failure_story' => 'generic',
            'verification_workflow' => 'manual',
            'docs_conflict' => 'trusts-ai',
            'team_enablement' => 'prompt-file',
        ]);

        $this->assertSame('do not treat as production-ready without stronger verification discipline', $result['summary']['hiring_signal']);
        $this->assertContains('Lets AI outrank AWS Documentation.', $result['red_flags']);
        $this->assertSame('AWS documentation conflict', $result['improvement_plan'][0]['dimension']);
        $this->assertSame('Official docs, provider docs, release note, or safe reproduction result.', $result['improvement_plan'][0]['evidence_to_request']);
        $this->assertSame('Critical thinking', $result['question_sequence'][2]['tier']);
        $this->assertCount(3, $result['question_sequence']);
        $this->assertStringContainsString('CloudFormation', $result['hands_on_challenge']['artifact']);
        $this->assertSame('block production ownership until verification and IaC review discipline improve', $result['debrief_template']['decision']);
        $this->assertContains('Source-of-truth rule for AI versus AWS Documentation needs clarification.', $result['debrief_template']['unresolved_risks']);
        $this->assertStringContainsString('## Unresolved Risks', $result['debrief_markdown']);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class AiCloudInterviewRubricService
{
    /**
     * Build a practical interview rubric for Cloud Engineers using AI at work.
     *
     * @param  array{candidate_level: string, recent_task: string, prompt_workflow: string, iac_review: string, failure_story: string, verification_workflow: string, docs_conflict: string, team_enablement: string}  $input
     * @return array<string, mixed>
     */
    public function score(array $input): array
    {
        $dimensions = $this->dimensionsFor($input);
        $score = array_sum(array_column($dimensions, 'points'));
        $verdict = $this->verdictFor($score);

        $redFlags = $this->redFlagsFor($input);
        $greenFlags = $this->greenFlagsFor($input);
        $followUpQuestions = $this->followUpQuestionsFor($input);
        $questionSequence = $this->questionSequenceFor($input);
        $handsOnChallenge = $this->handsOnChallengeFor($input);
        $teamRolloutProbe = $this->teamRolloutProbeFor($input);
        $improvementPlan = $this->improvementPlanFor($dimensions);
        $debriefTemplate = $this->debriefTemplateFor($score, $verdict, $input);

        return [
            'summary' => [
                'candidate_level' => $input['candidate_level'],
                'score' => $score,
                'verdict' => $verdict,
                'hiring_signal' => $this->hiringSignalFor($score, $input),
            ],
            'dimensions' => $dimensions,
            'red_flags' => $redFlags,
            'green_flags' => $greenFlags,
            'follow_up_questions' => $followUpQuestions,
            'question_sequence' => $questionSequence,
            'hands_on_challenge' => $handsOnChallenge,
            'interviewer_traps' => [
                'Ask for one time AI was wrong and what evidence caught it.',
                'Ask what they do when AI and AWS Documentation disagree.',
                'Ask them to review an AI-generated Terraform or CloudFormation change before apply.',
            ],
            'score_bands' => $this->scoreBands(),
            'calibration_notes' => $this->calibrationNotesFor($score, $input),
            'team_rollout_probe' => $teamRolloutProbe,
            'improvement_plan' => $improvementPlan,
            'debrief_template' => $debriefTemplate,
            'debrief_markdown' => $this->debriefMarkdownFor(
                score: $score,
                verdict: $verdict,
                input: $input,
                redFlags: $redFlags,
                greenFlags: $greenFlags,
                improvementPlan: $improvementPlan,
                debriefTemplate: $debriefTemplate,
            ),
            'commands' => [
                'php artisan route:list --path=ai-cloud-interview-rubric',
                'php artisan test --filter AiCloudInterviewRubric',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Convert answer quality into scored dimensions.
     *
     * @param  array<string, string>  $input
     * @return array<int, array{dimension: string, value: string, points: int, max: int, reason: string}>
     */
    private function dimensionsFor(array $input): array
    {
        return [
            $this->dimension('recent AI task', $input['recent_task'], [
                'none' => [0, 'No concrete task means the answer is still theoretical.'],
                'vague' => [8, 'A vague task shows some exposure but not operational evidence.'],
                'specific' => [16, 'A specific recent task proves the candidate can connect AI to real work.'],
            ], 16),
            $this->dimension('prompt workflow', $input['prompt_workflow'], [
                'none' => [0, 'No prompt workflow means the candidate is using AI like a search box.'],
                'basic' => [8, 'A basic prompt is useful but may miss constraints and output contracts.'],
                'structured' => [14, 'Structured prompts show context, constraints, format, and verification intent.'],
            ], 14),
            $this->dimension('IaC review', $input['iac_review'], [
                'none' => [0, 'No IaC review is unsafe for Terraform, CloudFormation, IAM, and Kubernetes output.'],
                'basic' => [9, 'Basic review catches syntax but may miss state, IAM, network, and cost risk.'],
                'production' => [16, 'Production review covers plan diff, permissions, replacement risk, docs, policy checks, and rollback.'],
            ], 16),
            $this->dimension('AI failure story', $input['failure_story'], [
                'none' => [0, 'No failure story is a common sign of shallow AI usage.'],
                'generic' => [7, 'A generic story needs evidence before it counts as operational maturity.'],
                'concrete' => [16, 'A concrete failure story proves the candidate has seen AI limits and learned from them.'],
            ], 16),
            $this->dimension('verification workflow', $input['verification_workflow'], [
                'none' => [0, 'No verification workflow lets AI confidence replace evidence.'],
                'manual' => [9, 'Manual review helps, but repeatability is weak.'],
                'systematic' => [18, 'Systematic verification ties docs, dry runs, tests, logs, and peer review together.'],
            ], 18),
            $this->dimension('AWS documentation conflict', $input['docs_conflict'], [
                'trusts-ai' => [0, 'Trusting AI over AWS Documentation is a high-risk decision pattern.'],
                'check-docs' => [8, 'Checking docs is good, but the source-of-truth rule should be explicit.'],
                'source-of-truth' => [12, 'Official docs, provider docs, release notes, and live verification outrank AI output.'],
            ], 12),
            $this->dimension('team enablement', $input['team_enablement'], [
                'none' => [0, 'No team view is acceptable for junior candidates but weak for senior roles.'],
                'prompt-file' => [4, 'A project prompt file is a useful first standardization step.'],
                'team-standard' => [8, 'Team standards turn individual AI usage into repeatable engineering practice.'],
            ], 8),
        ];
    }

    /**
     * @param  array<string, array{0: int, 1: string}>  $options
     * @return array{dimension: string, value: string, points: int, max: int, reason: string}
     */
    private function dimension(string $dimension, string $value, array $options, int $max): array
    {
        [$points, $reason] = $options[$value];

        return [
            'dimension' => $dimension,
            'value' => $value,
            'points' => $points,
            'max' => $max,
            'reason' => $reason,
        ];
    }

    private function verdictFor(int $score): string
    {
        return match (true) {
            $score >= 78 => 'strong practical AI user',
            $score >= 55 => 'promising but verify deeper',
            default => 'surface-level or risky AI usage',
        };
    }

    /**
     * @param  array<string, string>  $input
     */
    private function hiringSignalFor(int $score, array $input): string
    {
        if ($input['docs_conflict'] === 'trusts-ai' || $input['iac_review'] === 'none') {
            return 'do not treat as production-ready without stronger verification discipline';
        }

        return $score >= 78
            ? 'can use AI while still owning Cloud production risk'
            : 'continue with hands-on follow-up before deciding';
    }

    /**
     * @param  array<string, string>  $input
     * @return array<int, string>
     */
    private function redFlagsFor(array $input): array
    {
        $flags = [];

        if ($input['recent_task'] !== 'specific') {
            $flags[] = 'Cannot describe a recent concrete AI-assisted Cloud task.';
        }

        if ($input['failure_story'] === 'none') {
            $flags[] = 'Has no story about AI being wrong.';
        }

        if ($input['verification_workflow'] === 'none') {
            $flags[] = 'Has no repeatable verification workflow.';
        }

        if ($input['docs_conflict'] === 'trusts-ai') {
            $flags[] = 'Lets AI outrank AWS Documentation.';
        }

        return $flags;
    }

    /**
     * @param  array<string, string>  $input
     * @return array<int, string>
     */
    private function greenFlagsFor(array $input): array
    {
        return array_values(array_filter([
            $input['recent_task'] === 'specific' ? 'Names a concrete task, prompt shape, output edit, and result.' : null,
            $input['iac_review'] === 'production' ? 'Reviews AI-generated IaC for state, IAM, network, replacement, cost, and rollback risk.' : null,
            $input['failure_story'] === 'concrete' ? 'Can explain where AI failed and what evidence caught it.' : null,
            $input['verification_workflow'] === 'systematic' ? 'Uses docs, dry runs, tests, logs, and peer review before accepting output.' : null,
            $input['team_enablement'] === 'team-standard' ? 'Thinks beyond personal productivity and can standardize AI usage for a team.' : null,
        ]));
    }

    /**
     * @param  array<string, string>  $input
     * @return array<int, string>
     */
    private function followUpQuestionsFor(array $input): array
    {
        $questions = [
            'Show the exact review checklist you use before applying AI-generated Terraform or CloudFormation.',
            'Walk through one incident where AI suggested the wrong root cause.',
        ];

        if ($input['candidate_level'] === 'senior' && $input['team_enablement'] !== 'team-standard') {
            $questions[] = 'How would you roll this workflow out to five Cloud Engineers without creating prompt chaos?';
        }

        if ($input['docs_conflict'] !== 'source-of-truth') {
            $questions[] = 'What is your rule when AI, AWS Documentation, and live API behavior disagree?';
        }

        return $questions;
    }

    /**
     * @param  array<string, string>  $input
     * @return array<int, array{tier: string, goal: string, questions: array<int, string>, pass_signal: string}>
     */
    private function questionSequenceFor(array $input): array
    {
        $sequence = [
            [
                'tier' => 'Warm-up',
                'goal' => 'Check whether AI appears in real daily work.',
                'questions' => [
                    'Which AI tools do you use most often at work, and for which Cloud tasks?',
                    'Where does AI appear in a normal workday: design, IaC, debugging, review, documentation, or incident response?',
                    'When did you start using AI seriously, and what changed in your workflow?',
                ],
                'pass_signal' => 'The candidate names tools and concrete use cases without generic hype.',
            ],
            [
                'tier' => 'Practical evidence',
                'goal' => 'Separate real usage from surface-level answers.',
                'questions' => [
                    'Tell me one task from last week where you used AI. What did you prompt and what did you change before using the output?',
                    'When AI writes Terraform or CloudFormation, what do you review before apply?',
                    'Describe a hard debug session where AI helped. What evidence proved the final root cause?',
                ],
                'pass_signal' => 'The candidate can reconstruct input, prompt, output, edits, verification, and result.',
            ],
            [
                'tier' => 'Critical thinking',
                'goal' => 'Test whether AI output is subordinate to evidence.',
                'questions' => [
                    'How much do you trust AI output, and what is your verification workflow?',
                    'What tasks do you intentionally avoid giving to AI, and why?',
                    'If AI and AWS Documentation disagree, what do you do?',
                ],
                'pass_signal' => 'The candidate treats docs, dry runs, logs, tests, and peer review as authority.',
            ],
        ];

        if ($input['candidate_level'] === 'senior') {
            $sequence[] = [
                'tier' => 'Senior-level',
                'goal' => 'Check whether the candidate can scale AI usage beyond themselves.',
                'questions' => [
                    'Have you written a CLAUDE.md or project system prompt? What belongs in it?',
                    'How would you help a whole Cloud team use AI better without leaking secrets or bypassing review?',
                    'How do you distinguish using AI as a search engine from using AI as a thought partner?',
                    'How do you think the Cloud Engineer role changes over the next two years because of AI?',
                ],
                'pass_signal' => 'The candidate can standardize prompts, data rules, evidence gates, and review workflows for a team.',
            ];
        }

        if ($input['failure_story'] === 'none') {
            $sequence[] = [
                'tier' => 'Trap question',
                'goal' => 'Verify whether the candidate has actually felt AI failure modes.',
                'questions' => [
                    'You said you have not seen AI fail. Walk through the riskiest AI output you accepted and how you knew it was correct.',
                    'Show what would have caught a fake CLI flag, wrong IAM action, or outdated Terraform argument.',
                ],
                'pass_signal' => 'A real user can usually name at least one wrong output and the evidence that caught it.',
            ];
        }

        return $sequence;
    }

    /**
     * @param  array<string, string>  $input
     * @return array{title: string, setup: string, candidate_task: string, artifact: string, expected_findings: array<int, string>, evidence_to_request: array<int, string>, pass_criteria: array<int, string>, follow_up: string}
     */
    private function handsOnChallengeFor(array $input): array
    {
        $artifact = $input['iac_review'] === 'none'
            ? <<<'TEXT'
AI suggested this Terraform change:

resource "aws_security_group_rule" "allow_debug" {
  type              = "ingress"
  security_group_id = aws_security_group.app.id
  from_port         = 22
  to_port           = 22
  protocol          = "tcp"
  cidr_blocks       = ["0.0.0.0/0"]
}

resource "aws_iam_policy" "app_debug" {
  policy = jsonencode({
    Statement = [{
      Effect = "Allow"
      Action = "*"
      Resource = "*"
    }]
  })
}
TEXT
            : <<<'TEXT'
AI suggested this CloudFormation change:

Resources:
  AppBucket:
    Type: AWS::S3::Bucket
    Properties:
      AccessControl: PublicRead
      VersioningConfiguration:
        Status: Suspended
      LifecycleConfiguration:
        Rules:
          - Status: Enabled
            ExpirationInDays: 1
TEXT;

        return [
            'title' => 'Review an AI-generated infrastructure change before apply',
            'setup' => 'Give the candidate this generated IaC snippet and ask them to review it as if it appeared in a pull request.',
            'candidate_task' => 'Identify unsafe assumptions, request evidence, name commands to run, and decide whether to block, revise, or approve.',
            'artifact' => $artifact,
            'expected_findings' => [
                'Public network or public data exposure must be challenged before apply.',
                'Wildcard IAM permissions or overly broad access must be rejected.',
                'State replacement, data retention, encryption, logging, and rollback impact must be reviewed.',
                'Official AWS or provider documentation should outrank AI explanation.',
                'A plan, change set, policy check, and human review are required before production use.',
            ],
            'evidence_to_request' => [
                'Terraform plan or CloudFormation change set',
                'AWS/provider documentation for changed resource behavior',
                'IAM policy diff or policy-as-code result',
                'Rollback plan and blast-radius note',
                'Security review for public access, encryption, and retention',
            ],
            'pass_criteria' => [
                'Candidate blocks unsafe output instead of applying it because AI generated it.',
                'Candidate names concrete verification commands and source-of-truth documents.',
                'Candidate explains the production risk in Cloud terms, not only syntax terms.',
            ],
            'follow_up' => $input['failure_story'] === 'none'
                ? 'Ask how they would catch this if AI confidently said the snippet was safe.'
                : 'Ask them to compare this challenge with a real AI failure they have seen.',
        ];
    }

    /**
     * @param  array<string, string>  $input
     * @return array{decision: string, evidence_summary: array<int, string>, unresolved_risks: array<int, string>, recommended_next_step: string, interviewer_note_template: string}
     */
    private function debriefTemplateFor(int $score, string $verdict, array $input): array
    {
        $unresolvedRisks = array_values(array_filter([
            $input['recent_task'] !== 'specific' ? 'Candidate did not provide a concrete recent AI-assisted Cloud task.' : null,
            $input['failure_story'] !== 'concrete' ? 'AI failure-story depth is insufficient for production-risk calibration.' : null,
            $input['verification_workflow'] !== 'systematic' ? 'Verification workflow is not yet systematic enough for high-risk infrastructure output.' : null,
            $input['docs_conflict'] !== 'source-of-truth' ? 'Source-of-truth rule for AI versus AWS Documentation needs clarification.' : null,
            $input['iac_review'] !== 'production' ? 'IaC review depth still needs proof with a hands-on challenge.' : null,
        ]));

        return [
            'decision' => $this->debriefDecisionFor($score, $input),
            'evidence_summary' => [
                "Rubric score: {$score}/100 ({$verdict}).",
                "Candidate level evaluated: {$input['candidate_level']}.",
                "Recent task signal: {$input['recent_task']}.",
                "Verification signal: {$input['verification_workflow']}.",
                "AWS documentation conflict signal: {$input['docs_conflict']}.",
            ],
            'unresolved_risks' => $unresolvedRisks === [] ? ['No unresolved rubric risks from selected signals.'] : $unresolvedRisks,
            'recommended_next_step' => $this->recommendedNextStepFor($score, $input),
            'interviewer_note_template' => implode("\n", [
                'AI Cloud interview debrief:',
                '- Concrete task evidence:',
                '- Prompt workflow evidence:',
                '- IaC review findings:',
                '- AI failure story:',
                '- Verification workflow:',
                '- AI vs AWS Docs rule:',
                '- Team enablement signal:',
                '- Hands-on challenge result:',
                '- Decision and next step:',
            ]),
        ];
    }

    /**
     * @param  array<string, string>  $input
     * @param  array<int, string>  $redFlags
     * @param  array<int, string>  $greenFlags
     * @param  array<int, array{dimension: string, gap: int, action: string, evidence_to_request: string}>  $improvementPlan
     * @param  array{decision: string, evidence_summary: array<int, string>, unresolved_risks: array<int, string>, recommended_next_step: string, interviewer_note_template: string}  $debriefTemplate
     */
    private function debriefMarkdownFor(
        int $score,
        string $verdict,
        array $input,
        array $redFlags,
        array $greenFlags,
        array $improvementPlan,
        array $debriefTemplate,
    ): string {
        $lines = [
            '# AI Cloud Interview Debrief',
            '',
            "Decision: {$debriefTemplate['decision']}",
            "Score: {$score}/100 ({$verdict})",
            "Candidate level: {$input['candidate_level']}",
            "Next step: {$debriefTemplate['recommended_next_step']}",
            '',
            '## Green Flags',
            ...$this->markdownBullets($greenFlags),
            '',
            '## Red Flags',
            ...$this->markdownBullets($redFlags),
            '',
            '## Unresolved Risks',
            ...$this->markdownBullets($debriefTemplate['unresolved_risks']),
            '',
            '## Targeted Follow-up',
            ...$this->markdownBullets(array_map(
                fn (array $item): string => "{$item['dimension']}: {$item['action']} Evidence: {$item['evidence_to_request']}",
                $improvementPlan,
            )),
            '',
            '## Evidence Notes',
            '- Concrete task evidence:',
            '- Prompt workflow evidence:',
            '- IaC review findings:',
            '- AI failure story:',
            '- Verification workflow:',
            '- AI vs AWS Docs rule:',
            '- Hands-on challenge result:',
        ];

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function markdownBullets(array $items): array
    {
        if ($items === []) {
            return ['- None recorded.'];
        }

        return array_map(fn (string $item): string => "- {$item}", $items);
    }

    /**
     * @param  array<string, string>  $input
     */
    private function debriefDecisionFor(int $score, array $input): string
    {
        if ($input['docs_conflict'] === 'trusts-ai' || $input['iac_review'] === 'none') {
            return 'block production ownership until verification and IaC review discipline improve';
        }

        if ($score >= 78) {
            return 'advance to final hands-on Cloud review';
        }

        return 'continue interviewing with targeted evidence checks';
    }

    /**
     * @param  array<string, string>  $input
     */
    private function recommendedNextStepFor(int $score, array $input): string
    {
        if ($input['iac_review'] !== 'production') {
            return 'Run the hands-on AI-generated IaC review challenge before making a hiring decision.';
        }

        if ($input['verification_workflow'] !== 'systematic') {
            return 'Ask the candidate to write the exact verification workflow they would put into a PR checklist.';
        }

        return $score >= 78
            ? 'Move to a final system-design or incident-response exercise.'
            : 'Ask follow-up questions from the weakest rubric dimensions.';
    }

    /**
     * @param  array<string, string>  $input
     * @return array<int, string>
     */
    private function calibrationNotesFor(int $score, array $input): array
    {
        return [
            "Score {$score}/100 is calibrated for practical Cloud work, not AI enthusiasm.",
            $input['candidate_level'] === 'senior'
                ? 'For senior candidates, team enablement and failure-story depth should carry extra weight.'
                : 'For non-senior candidates, concrete task evidence matters more than organization-wide AI strategy.',
            'A strong candidate should still pass a hands-on IaC review exercise before production ownership.',
        ];
    }

    /**
     * @return array<int, array{band: string, range: string, meaning: string, interviewer_action: string}>
     */
    private function scoreBands(): array
    {
        return [
            [
                'band' => 'strong practical AI user',
                'range' => '78-100',
                'meaning' => 'The candidate gives concrete examples and keeps AI subordinate to evidence.',
                'interviewer_action' => 'Move to a hands-on IaC review or incident-debug exercise.',
            ],
            [
                'band' => 'promising but verify deeper',
                'range' => '55-77',
                'meaning' => 'The candidate has useful habits but still needs proof on weak dimensions.',
                'interviewer_action' => 'Ask targeted follow-ups on the weakest scoring dimensions.',
            ],
            [
                'band' => 'surface-level or risky AI usage',
                'range' => '0-54',
                'meaning' => 'The candidate may like AI tools but cannot yet show production-safe usage.',
                'interviewer_action' => 'Require a live review task before considering production ownership.',
            ],
        ];
    }

    /**
     * @param  array<int, array{dimension: string, value: string, points: int, max: int, reason: string}>  $dimensions
     * @return array<int, array{dimension: string, gap: int, action: string, evidence_to_request: string}>
     */
    private function improvementPlanFor(array $dimensions): array
    {
        return collect($dimensions)
            ->filter(fn (array $dimension): bool => $dimension['points'] < $dimension['max'])
            ->sortByDesc(fn (array $dimension): int => $dimension['max'] - $dimension['points'])
            ->take(4)
            ->map(fn (array $dimension): array => [
                'dimension' => $dimension['dimension'],
                'gap' => $dimension['max'] - $dimension['points'],
                'action' => $this->actionForDimension($dimension['dimension']),
                'evidence_to_request' => $this->evidenceForDimension($dimension['dimension']),
            ])
            ->values()
            ->all();
    }

    private function actionForDimension(string $dimension): string
    {
        return match ($dimension) {
            'recent AI task' => 'Ask for a task from the last week and require context, prompt, output edits, and result.',
            'prompt workflow' => 'Ask the candidate to rewrite a vague AI prompt into a structured Cloud task prompt.',
            'IaC review' => 'Give an AI-generated Terraform or CloudFormation snippet and ask for pre-apply review.',
            'AI failure story' => 'Ask for one time AI was wrong, the impact, and the verification step that caught it.',
            'verification workflow' => 'Ask for the exact docs, dry-run, test, log, and peer-review sequence before trusting AI output.',
            'AWS documentation conflict' => 'Ask what happens when AI, AWS Documentation, provider docs, and live behavior disagree.',
            'team enablement' => 'Ask how they would standardize prompts, data rules, and review gates for a Cloud team.',
            default => 'Ask for concrete evidence before accepting the answer.',
        };
    }

    private function evidenceForDimension(string $dimension): string
    {
        return match ($dimension) {
            'recent AI task' => 'Recent ticket, PR, incident note, prompt excerpt, or sanitized command transcript.',
            'prompt workflow' => 'Prompt template with role, context, constraints, output contract, and verification request.',
            'IaC review' => 'Terraform plan, CloudFormation change set, IAM diff, policy check, or rollback note.',
            'AI failure story' => 'Specific wrong output, why it was plausible, and the evidence that disproved it.',
            'verification workflow' => 'Docs link, dry-run output, test result, log evidence, and reviewer note.',
            'AWS documentation conflict' => 'Official docs, provider docs, release note, or safe reproduction result.',
            'team enablement' => 'CLAUDE.md, project prompt, review checklist, allowed data policy, or team runbook.',
            default => 'Concrete artifact from real work.',
        };
    }

    /**
     * @param  array<string, string>  $input
     * @return array<string, mixed>
     */
    private function teamRolloutProbeFor(array $input): array
    {
        return [
            'current_signal' => $input['team_enablement'],
            'expected_artifacts' => [
                'project prompt or CLAUDE.md with repo rules',
                'AI-generated IaC review checklist',
                'allowed data and secret-handling policy',
                'evidence ledger for docs, plans, tests, and dry runs',
            ],
            'pass_condition' => 'The candidate can move from personal AI usage to a repeatable team workflow with review and verification gates.',
        ];
    }
}

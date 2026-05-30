<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class AiHallucinationGuardPlanService
{
    /**
     * Build a guardrail plan for reducing AI hallucination risk in code work.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @return array{summary: array<string, string>, risk_score: array{score: int, level: string, reasons: array<int, string>}, score_breakdown: array<int, array{signal: string, value: string, points: int, reason: string}>, decision_matrix: array<int, array{signal: string, current_value: string, decision_impact: string, safer_choice: string}>, acceptance_gate: array{decision: string, required_before_accept: array<int, string>, blocked_when: array<int, string>}, release_readiness: array{status: string, reason: string, required_before_release: array<int, string>}, evidence_requirements: array<int, array{claim_type: string, acceptable_evidence: array<int, string>, reject_if: string}>, claim_validation_rules: array<int, array{claim: string, verify_with: string, fail_action: string}>, evidence_ledger_template: array<int, array{field: string, example: string, required: bool}>, required_artifacts: array<int, array{name: string, owner: string, pass_condition: string}>, ci_policy: array{mode: string, required_checks: array<int, string>, manual_gates: array<int, string>}, data_safety_plan: array{allowed_context: array<int, string>, redact_before_prompt: array<int, string>, blocked_inputs: array<int, string>, retention_note: string}, prompt_examples: array<int, array{name: string, prompt: string, expected_output: string}>, hallucination_vectors: array<int, string>, guardrails: array<int, string>, code_controls: array<int, string>, verification_plan: array<int, array{stage: string, action: string, pass_signal: string}>, runtime_verification_commands: array<int, array{name: string, command: string, proves: string}>, observability_plan: array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}, rollback_playbook: array<int, array{phase: string, action: string, evidence_to_keep: string}>, ownership_model: array<int, array{role: string, owns: string, failure_if_missing: string}>, escalation_policy: array<int, array{trigger: string, escalate_to: string, required_action: string}>, maturity_roadmap: array<int, array{level: string, practice: string, exit_criteria: string}>, implementation_steps: array<int, string>, red_team_scenarios: array<int, array{scenario: string, planted_risk: string, expected_guardrail: string}>, automation_hooks: array<int, array{hook: string, purpose: string, example_check: string}>, prompt_contract: array<int, string>, safe_output_contract: array<int, array{section: string, required: bool, reject_if_missing: string}>, review_checklist: array<int, array{lens: string, question: string}>, tests: array<int, string>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $scoreBreakdown = $this->scoreBreakdownFor($input);
        $riskScore = $this->riskScoreFromBreakdown($scoreBreakdown);

        return [
            'summary' => $this->summaryFor($input),
            'risk_score' => $riskScore,
            'score_breakdown' => $scoreBreakdown,
            'decision_matrix' => $this->decisionMatrixFor($input, $riskScore),
            'acceptance_gate' => $this->acceptanceGateFor($input, $riskScore),
            'release_readiness' => $this->releaseReadinessFor($input, $riskScore),
            'evidence_requirements' => $this->evidenceRequirementsFor($input),
            'claim_validation_rules' => $this->claimValidationRulesFor($input),
            'evidence_ledger_template' => $this->evidenceLedgerTemplateFor($input),
            'required_artifacts' => $this->requiredArtifactsFor($input),
            'ci_policy' => $this->ciPolicyFor($input, $riskScore),
            'data_safety_plan' => $this->dataSafetyPlanFor($input),
            'prompt_examples' => $this->promptExamplesFor($input),
            'hallucination_vectors' => $this->hallucinationVectorsFor($input),
            'guardrails' => $this->guardrailsFor($input),
            'code_controls' => $this->codeControlsFor($input),
            'verification_plan' => $this->verificationPlanFor($input),
            'runtime_verification_commands' => $this->runtimeVerificationCommandsFor($input),
            'observability_plan' => $this->observabilityPlanFor($input),
            'rollback_playbook' => $this->rollbackPlaybookFor($input),
            'ownership_model' => $this->ownershipModelFor($input),
            'escalation_policy' => $this->escalationPolicyFor($input, $riskScore),
            'maturity_roadmap' => $this->maturityRoadmapFor($input),
            'implementation_steps' => $this->implementationStepsFor($input),
            'red_team_scenarios' => $this->redTeamScenariosFor($input),
            'automation_hooks' => $this->automationHooksFor($input),
            'prompt_contract' => $this->promptContractFor($input),
            'safe_output_contract' => $this->safeOutputContractFor($input),
            'review_checklist' => $this->reviewChecklistFor($input),
            'tests' => $this->testsFor($input),
            'interview_answer' => $this->interviewAnswerFor($input),
            'commands' => [
                'php artisan route:list --path=ai-hallucination-guard-plan',
                'php artisan test --filter AiHallucinationGuardPlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return a short decision summary.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @return array<string, string>
     */
    private function summaryFor(array $input): array
    {
        return [
            'decision' => "Use evidence-first AI workflow for {$input['ai_task']} work.",
            'main_risk' => $input['evidence_sources'] === 'none'
                ? 'The model is being asked to invent context, so plausible but false output is likely.'
                : 'The model can still drift from repo facts unless every claim is tied to files, tests, or docs.',
            'operating_rule' => 'AI may propose code, but repo facts, runtime checks, and human review decide whether the output is accepted.',
        ];
    }

    /**
     * Break hallucination risk into explainable scoring signals.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @return array<int, array{signal: string, value: string, points: int, reason: string}>
     */
    private function scoreBreakdownFor(array $input): array
    {
        $taskRiskPoints = match ($input['risk_level']) {
            'high' => 60,
            'medium' => 35,
            default => 20,
        };

        [$evidencePoints, $evidenceReason] = match ($input['evidence_sources']) {
            'none' => [25, 'No evidence sources means the model may invent APIs, files, schema, or behavior.'],
            'partial' => [10, 'Partial evidence leaves room for version drift and missing edge cases.'],
            default => [-10, 'Repo context plus tests or docs reduces unsupported claims.'],
        };

        $runtimePoints = $input['runtime_checks'] === 'no' ? 15 : 0;
        $humanReviewPoints = $input['human_review'] === 'no' ? 20 : 0;

        return [
            [
                'signal' => 'task risk',
                'value' => $input['risk_level'],
                'points' => $taskRiskPoints,
                'reason' => "Task risk is {$input['risk_level']}.",
            ],
            [
                'signal' => 'evidence sources',
                'value' => $input['evidence_sources'],
                'points' => $evidencePoints,
                'reason' => $evidenceReason,
            ],
            [
                'signal' => 'runtime checks',
                'value' => $input['runtime_checks'],
                'points' => $runtimePoints,
                'reason' => $input['runtime_checks'] === 'no'
                    ? 'Without runtime checks, a clean explanation can hide broken behavior.'
                    : 'Runtime checks keep polished explanations tied to executable behavior.',
            ],
            [
                'signal' => 'human review',
                'value' => $input['human_review'],
                'points' => $humanReviewPoints,
                'reason' => $input['human_review'] === 'no'
                    ? 'Without human review, the model becomes the final authority over its own assumptions.'
                    : 'Human review keeps final authority outside the model.',
            ],
        ];
    }

    /**
     * Score hallucination risk from task sensitivity and verification coverage.
     *
     * @param  array<int, array{signal: string, value: string, points: int, reason: string}>  $breakdown
     * @return array{score: int, level: string, reasons: array<int, string>}
     */
    private function riskScoreFromBreakdown(array $breakdown): array
    {
        $score = array_sum(array_column($breakdown, 'points'));

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'level' => $score >= 70 ? 'high' : ($score >= 40 ? 'medium' : 'controlled'),
            'reasons' => array_column($breakdown, 'reason'),
        ];
    }

    /**
     * Return a decision matrix that shows how to move from risky defaults to safer choices.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @param  array{score: int, level: string, reasons: array<int, string>}  $riskScore
     * @return array<int, array{signal: string, current_value: string, decision_impact: string, safer_choice: string}>
     */
    private function decisionMatrixFor(array $input, array $riskScore): array
    {
        return [
            [
                'signal' => 'AI task',
                'current_value' => $input['ai_task'],
                'decision_impact' => in_array($input['ai_task'], ['code-generation', 'data-answer'], true)
                    ? 'High-impact output needs stronger evidence before users or production code see it.'
                    : 'Review and refactor work can stay controlled when scope and evidence are explicit.',
                'safer_choice' => 'Use the smallest scoped task and ask for assumptions before output.',
            ],
            [
                'signal' => 'Evidence sources',
                'current_value' => $input['evidence_sources'],
                'decision_impact' => $input['evidence_sources'] === 'none'
                    ? 'No evidence should block acceptance because the model can invent repo facts.'
                    : 'Evidence quality decides whether claims can move from guess to reviewable fact.',
                'safer_choice' => 'repo-context or tests-docs',
            ],
            [
                'signal' => 'Runtime checks',
                'current_value' => $input['runtime_checks'],
                'decision_impact' => $input['runtime_checks'] === 'no'
                    ? 'Block production acceptance until executable behavior is verified.'
                    : 'Runtime evidence lowers the chance that a clean answer hides a broken path.',
                'safer_choice' => 'yes',
            ],
            [
                'signal' => 'Human review',
                'current_value' => $input['human_review'],
                'decision_impact' => $input['human_review'] === 'no'
                    ? 'Assign a reviewer before merge so the model does not approve itself.'
                    : 'Reviewer ownership keeps auth, data, version, and scope risk outside model authority.',
                'safer_choice' => 'yes',
            ],
            [
                'signal' => 'Risk score',
                'current_value' => "{$riskScore['score']}/{$riskScore['level']}",
                'decision_impact' => $riskScore['level'] === 'high'
                    ? 'Use strict-blocking policy until evidence, runtime, and review gates pass.'
                    : 'Use standard-blocking policy and keep evidence visible for review.',
                'safer_choice' => 'Reduce score with evidence, runtime checks, and review.',
            ],
        ];
    }

    /**
     * Return merge/acceptance rules from the calculated risk.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @param  array{score: int, level: string, reasons: array<int, string>}  $riskScore
     * @return array{decision: string, required_before_accept: array<int, string>, blocked_when: array<int, string>}
     */
    private function acceptanceGateFor(array $input, array $riskScore): array
    {
        $requirements = [
            'Assumptions and uncertain claims are listed before the answer or patch.',
            'Every framework, package, route, class, config key, and database field claim has evidence.',
            'The final answer includes verification commands and the expected pass signal.',
        ];

        if ($input['runtime_checks'] === 'yes') {
            $requirements[] = 'At least one runtime, test, route-list, static analysis, or smoke-check command has been run.';
        } else {
            $requirements[] = 'Runtime verification is added before production acceptance.';
        }

        if ($input['human_review'] === 'yes') {
            $requirements[] = 'A human reviewer owns the final decision for risky areas.';
        } else {
            $requirements[] = 'A human reviewer is assigned before merge or user-facing publication.';
        }

        return [
            'decision' => $riskScore['level'] === 'high'
                ? 'block until evidence and verification pass'
                : 'accept only after evidence, scope, and runtime checks pass',
            'required_before_accept' => $requirements,
            'blocked_when' => [
                'The model cites a file, API, method, package, migration, command, or source that cannot be found.',
                'The patch changes unrelated files or expands scope without an explicit reason.',
                'The answer reports test or runtime results that were not actually run.',
                'Security-sensitive changes touch auth, authorization, validation, files, secrets, or data access without human review.',
            ],
        ];
    }

    /**
     * Return release status based on evidence, runtime checks, review, and score.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @param  array{score: int, level: string, reasons: array<int, string>}  $riskScore
     * @return array{status: string, reason: string, required_before_release: array<int, string>}
     */
    private function releaseReadinessFor(array $input, array $riskScore): array
    {
        $required = [];

        if ($input['evidence_sources'] === 'none') {
            $required[] = 'Add an evidence map with repo files, official docs, logs, tests, source rows, or retrieval IDs.';
        }

        if ($input['runtime_checks'] === 'no') {
            $required[] = 'Run at least one targeted test, route-list check, smoke check, or reproduction command.';
        }

        if ($input['human_review'] === 'no') {
            $required[] = 'Assign a human reviewer before merge or user-facing publication.';
        }

        if ($riskScore['level'] === 'high') {
            $required[] = 'Lower risk with stronger evidence or require strict senior review before release.';
        }

        if ($required === []) {
            return [
                'status' => 'ready-for-review',
                'reason' => 'Evidence, runtime checks, and human ownership are present; the output can move to normal review.',
                'required_before_release' => [
                    'Keep the evidence map and verification result attached to the task or pull request.',
                ],
            ];
        }

        return [
            'status' => $riskScore['level'] === 'high' ? 'blocked' : 'conditional',
            'reason' => 'AI output is not release-ready until the missing guardrails are closed.',
            'required_before_release' => $required,
        ];
    }

    /**
     * Return the evidence contract for common AI claim types.
     *
     * @return array<int, array{claim_type: string, acceptable_evidence: array<int, string>, reject_if: string}>
     */
    private function evidenceRequirementsFor(array $input): array
    {
        $requirements = [
            [
                'claim_type' => 'repo structure',
                'acceptable_evidence' => ['file path', 'class name found in repo', 'route-list output', 'config key found in repo'],
                'reject_if' => 'The answer names files, folders, routes, or config keys without checking the repository.',
            ],
            [
                'claim_type' => 'framework or package behavior',
                'acceptable_evidence' => ['composer.json version', 'installed package version', 'official docs for that version', 'existing code usage'],
                'reject_if' => 'The answer assumes a Laravel, PHP, or package API from a different version.',
            ],
            [
                'claim_type' => 'bug root cause',
                'acceptable_evidence' => ['reproduction step', 'failing test', 'log line', 'stack trace', 'request/response sample'],
                'reject_if' => 'The explanation sounds plausible but cannot point to the symptom or failing layer.',
            ],
            [
                'claim_type' => 'verification result',
                'acceptable_evidence' => ['command output', 'test name', 'HTTP status', 'browser/API smoke result'],
                'reject_if' => 'The model says tests pass without a real command or recorded result.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $requirements[] = [
                'claim_type' => 'factual data answer',
                'acceptable_evidence' => ['database row ID', 'retrieval source ID', 'document citation', 'validated API response'],
                'reject_if' => 'The answer invents facts, totals, names, links, citations, or datasets.',
            ];
        }

        return $requirements;
    }

    /**
     * Return concrete validation rules for common hallucinated claims.
     *
     * @return array<int, array{claim: string, verify_with: string, fail_action: string}>
     */
    private function claimValidationRulesFor(array $input): array
    {
        $rules = [
            [
                'claim' => 'route, controller, service, or config exists',
                'verify_with' => 'Search the repo and, for routes, run route:list filtered to the touched path.',
                'fail_action' => 'Remove the claim or create the missing artifact in the smallest scoped patch.',
            ],
            [
                'claim' => 'framework or package API is available',
                'verify_with' => 'Check composer.json, lockfile version, official docs, and existing usage in the codebase.',
                'fail_action' => 'Replace the API with one proven for this version or block the patch.',
            ],
            [
                'claim' => 'bug root cause is known',
                'verify_with' => 'Tie the claim to a failing test, reproduction step, log line, stack trace, or request sample.',
                'fail_action' => 'Convert the statement into a hypothesis and collect confirming evidence before patching.',
            ],
            [
                'claim' => 'tests or commands passed',
                'verify_with' => 'Record the exact command, target filter, timestamp, and pass/fail result.',
                'fail_action' => 'Mark the result unverified and do not cite it as a pass signal.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $rules[] = [
                'claim' => 'factual answer, total, citation, or source-backed statement is true',
                'verify_with' => 'Check the retrieval source ID, database row, validated API response, or document citation.',
                'fail_action' => 'Return missing evidence instead of showing a generated fact.',
            ];
        }

        return $rules;
    }

    /**
     * Return a ledger shape for recording evidence without leaking sensitive data.
     *
     * @return array<int, array{field: string, example: string, required: bool}>
     */
    private function evidenceLedgerTemplateFor(array $input): array
    {
        $template = [
            [
                'field' => 'claim',
                'example' => 'The route exists and points to the expected controller.',
                'required' => true,
            ],
            [
                'field' => 'evidence_type',
                'example' => 'repo-file, route-list, test-output, log-line, official-docs, source-row',
                'required' => true,
            ],
            [
                'field' => 'evidence_reference',
                'example' => 'routes/api/practice-actions.php or php artisan route:list --path=example',
                'required' => true,
            ],
            [
                'field' => 'verification_result',
                'example' => 'pass, fail, missing, or not-run with the exact command when available',
                'required' => true,
            ],
            [
                'field' => 'redaction_note',
                'example' => 'Secrets, personal data, hostnames, and raw customer records were removed.',
                'required' => false,
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $template[] = [
                'field' => 'source_id',
                'example' => 'retrieval document ID, database row ID, validated API response ID, or citation key',
                'required' => true,
            ];
        }

        return $template;
    }

    /**
     * Return the artifacts reviewers should expect before accepting AI output.
     *
     * @return array<int, array{name: string, owner: string, pass_condition: string}>
     */
    private function requiredArtifactsFor(array $input): array
    {
        $artifacts = [
            [
                'name' => 'assumption register',
                'owner' => 'AI operator',
                'pass_condition' => 'All uncertain claims are listed and either proven, removed, or marked unverified.',
            ],
            [
                'name' => 'evidence map',
                'owner' => 'AI operator',
                'pass_condition' => 'Each non-obvious claim maps to a repo file, test, log, docs page, route output, or source row.',
            ],
            [
                'name' => 'scope diff summary',
                'owner' => 'reviewer',
                'pass_condition' => 'Every touched file has a reason tied to the requested behavior.',
            ],
            [
                'name' => 'verification record',
                'owner' => $input['runtime_checks'] === 'yes' ? 'AI operator' : 'reviewer',
                'pass_condition' => $input['runtime_checks'] === 'yes'
                    ? 'The named commands were run and the result is recorded.'
                    : 'A reviewer selects and runs a verification command before release.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $artifacts[] = [
                'name' => 'source ledger',
                'owner' => 'reviewer',
                'pass_condition' => 'Every factual answer has a source ID, database row, validated API response, or document citation.',
            ];
        }

        return $artifacts;
    }

    /**
     * Return a CI policy profile from risk and review coverage.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @param  array{score: int, level: string, reasons: array<int, string>}  $riskScore
     * @return array{mode: string, required_checks: array<int, string>, manual_gates: array<int, string>}
     */
    private function ciPolicyFor(array $input, array $riskScore): array
    {
        $requiredChecks = [
            'json-schema-or-request-validation',
            'route-list-for-touched-endpoints',
            'targeted-feature-test',
            'pint-or-static-style-check',
        ];

        if ($input['evidence_sources'] === 'none') {
            $requiredChecks[] = 'evidence-map-required';
        }

        if ($input['runtime_checks'] === 'no') {
            $requiredChecks[] = 'runtime-verification-required';
        }

        $manualGates = [
            'reviewer-confirms-no-invented-api',
            'reviewer-confirms-scope-did-not-expand',
        ];

        if ($riskScore['level'] === 'high') {
            $manualGates[] = 'senior-review-required';
        }

        if ($input['human_review'] === 'no') {
            $manualGates[] = 'assign-human-reviewer-before-merge';
        }

        return [
            'mode' => $riskScore['level'] === 'high' ? 'strict-blocking' : 'standard-blocking',
            'required_checks' => $requiredChecks,
            'manual_gates' => $manualGates,
        ];
    }

    /**
     * Return data-safety controls before sharing context with an AI tool.
     *
     * @return array{allowed_context: array<int, string>, redact_before_prompt: array<int, string>, blocked_inputs: array<int, string>, retention_note: string}
     */
    private function dataSafetyPlanFor(array $input): array
    {
        $blockedInputs = [
            'production secrets, API keys, tokens, passwords, private keys, and session cookies',
            'raw personal data, customer records, payment details, or private support messages',
            'proprietary source data that is not needed for the specific task',
        ];

        if ($input['ai_task'] === 'data-answer') {
            $blockedInputs[] = 'raw datasets without source IDs, sampling rules, or permission to use them';
        }

        return [
            'allowed_context' => [
                'minimal file paths, function names, route names, and sanitized snippets needed for the task',
                'failing test names, sanitized stack traces, and non-sensitive command output',
                'official docs links or internal documentation excerpts that do not contain secrets',
            ],
            'redact_before_prompt' => [
                'user IDs, emails, phone numbers, names, addresses, and account identifiers',
                'hostnames, internal IPs, bucket names, database names, and queue names when not required',
                'access tokens, cookies, signed URLs, private file paths, and environment values',
            ],
            'blocked_inputs' => $blockedInputs,
            'retention_note' => 'Keep only the minimum evidence needed for review, and store it in the task or pull request instead of pasting sensitive raw data into the model.',
        ];
    }

    /**
     * Return prompt examples that encode the guardrails.
     *
     * @return array<int, array{name: string, prompt: string, expected_output: string}>
     */
    private function promptExamplesFor(array $input): array
    {
        $examples = [
            [
                'name' => 'preflight before code',
                'prompt' => 'Do not write code yet. List assumptions, missing files, risky claims, and the exact evidence needed before patching.',
                'expected_output' => 'Assumption register, missing context list, evidence requirements, and blocked claims.',
            ],
            [
                'name' => 'minimal patch request',
                'prompt' => 'Use only the provided repo context. Produce the smallest patch for the stated behavior. Do not invent APIs. Return touched files and verification commands.',
                'expected_output' => 'Small scoped patch, touched-file list, risk notes, and commands to verify behavior.',
            ],
            [
                'name' => 'strict review pass',
                'prompt' => 'Review this AI-generated diff for invented APIs, version drift, unrelated edits, missing tests, auth risk, validation gaps, and unverified claims.',
                'expected_output' => 'Findings ordered by risk, missing evidence, required tests, and approve/block decision.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $examples[] = [
                'name' => 'source-grounded answer',
                'prompt' => 'Answer only from the provided source IDs. If a fact is missing, say it is missing. Return a source ledger for every factual claim.',
                'expected_output' => 'Factual answer with source IDs, missing-data notes, and no invented totals or citations.',
            ];
        }

        return $examples;
    }

    /**
     * Return common hallucination paths for the context.
     *
     * @return array<int, string>
     */
    private function hallucinationVectorsFor(array $input): array
    {
        $vectors = [
            'Invented files, classes, routes, config keys, methods, or package APIs that do not exist in the repository.',
            'Wrong framework version assumptions that look plausible but fail at runtime.',
            'Confident explanations that are not tied to a reproduction, log line, test, or source file.',
        ];

        if (in_array($input['ai_task'], ['debugging', 'refactor'], true)) {
            $vectors[] = 'Large patches that treat symptoms while changing boundaries outside the real root cause.';
        }

        if ($input['ai_task'] === 'data-answer') {
            $vectors[] = 'Generated facts or datasets that are not backed by retrieval, database rows, or source citations.';
        }

        return $vectors;
    }

    /**
     * Return workflow guardrails for reducing hallucination risk.
     *
     * @return array<int, string>
     */
    private function guardrailsFor(array $input): array
    {
        $guardrails = [
            'Require the model to list assumptions before producing code.',
            'Require file paths, line references, commands, or source citations for factual claims.',
            'Ask for competing hypotheses before accepting a root-cause explanation.',
            'Limit the patch to the smallest set of files needed for the stated behavior.',
        ];

        if ($input['runtime_checks'] === 'yes') {
            $guardrails[] = 'Run the exact test, route-list, static analysis, or smoke-check command before accepting the answer.';
        } else {
            $guardrails[] = 'Mark the output as unverified until a runtime command or manual reproduction confirms it.';
        }

        return $guardrails;
    }

    /**
     * Return controls that can be encoded in code or review workflow.
     *
     * @return array<int, string>
     */
    private function codeControlsFor(array $input): array
    {
        return [
            'Use typed request validation so AI cannot silently pass unsupported input shape into services.',
            'Keep business decisions in services/actions where unit tests can pin behavior.',
            'Add feature tests for the real user path instead of only testing helper methods.',
            'Reject patches that introduce new package APIs without proving the dependency and version exist.',
            $input['ai_task'] === 'data-answer'
                ? 'Use retrieval, database constraints, or source IDs before showing factual answers to users.'
                : 'Use logs, failing tests, and route output as evidence before patching code.',
        ];
    }

    /**
     * Return verification steps.
     *
     * @return array<int, array{stage: string, action: string, pass_signal: string}>
     */
    private function verificationPlanFor(array $input): array
    {
        return [
            [
                'stage' => 'before patch',
                'action' => 'Reproduce the symptom or define the exact expected behavior.',
                'pass_signal' => 'There is a failing test, failing request, log line, or written acceptance case.',
            ],
            [
                'stage' => 'during patch',
                'action' => 'Check every new API, class, config key, migration field, and route against the repo.',
                'pass_signal' => 'Every non-obvious claim maps to an existing file, package, doc, or created test.',
            ],
            [
                'stage' => 'after patch',
                'action' => $input['runtime_checks'] === 'yes'
                    ? 'Run tests, route checks, static analysis, or browser/API smoke checks.'
                    : 'Record that runtime verification is still missing and block production acceptance.',
                'pass_signal' => 'The original symptom is fixed and no unrelated file or behavior drift remains.',
            ],
        ];
    }

    /**
     * Return candidate commands that prove the AI output against runtime facts.
     *
     * @return array<int, array{name: string, command: string, proves: string}>
     */
    private function runtimeVerificationCommandsFor(array $input): array
    {
        $commands = [
            [
                'name' => 'route surface check',
                'command' => 'php artisan route:list --path=<touched-path>',
                'proves' => 'The claimed route exists and points to the expected HTTP surface.',
            ],
            [
                'name' => 'targeted feature test',
                'command' => 'php artisan test --filter <FeatureOrApiTestName>',
                'proves' => 'The user-facing behavior works through the real request path.',
            ],
            [
                'name' => 'style and syntax check',
                'command' => 'vendor\\bin\\pint --test',
                'proves' => 'The patch follows the project formatting gate before review.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $commands[] = [
                'name' => 'source ledger check',
                'command' => 'php artisan test --filter SourceBackedAnswerTest',
                'proves' => 'Every factual answer is tied to a source ID or validated data row.',
            ];
        }

        if ($input['ai_task'] === 'debugging') {
            $commands[] = [
                'name' => 'reproduction check',
                'command' => 'php artisan test --filter <OriginalFailingCase>',
                'proves' => 'The original symptom fails before the fix and passes after the patch.',
            ];
        }

        return $commands;
    }

    /**
     * Return runtime signals for spotting AI-output regressions after merge.
     *
     * @return array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}
     */
    private function observabilityPlanFor(array $input): array
    {
        $metrics = [
            'ai_generated_change_error_total by route, feature, and exception class',
            'ai_guardrail_blocked_claim_total by claim_type and risk_level',
            'ai_verification_gap_total for outputs accepted without recorded command results',
        ];

        if ($input['ai_task'] === 'data-answer') {
            $metrics[] = 'ai_source_missing_total for factual answers without source IDs';
        }

        return [
            'metrics' => $metrics,
            'log_events' => [
                'ai_output_accepted with task, risk_level, evidence_sources, reviewer, and verification command',
                'ai_output_blocked with blocked_reason, missing_artifact, and claim_type',
                'ai_regression_detected with route, request_id, commit_sha, and suspected_generated_change',
            ],
            'alerts' => [
                'Alert when accepted AI-generated changes produce repeated 5xx responses on touched routes.',
                'Alert when verification gaps are recorded for high-risk tasks.',
                'Alert when source-missing factual answers exceed zero in user-facing flows.',
            ],
        ];
    }

    /**
     * Return rollback steps when AI output causes a regression.
     *
     * @return array<int, array{phase: string, action: string, evidence_to_keep: string}>
     */
    private function rollbackPlaybookFor(array $input): array
    {
        return [
            [
                'phase' => 'detect',
                'action' => 'Confirm the regression by comparing the symptom to the touched route, command, or source data path.',
                'evidence_to_keep' => 'request ID, route name, failing command, source row ID, or user-visible symptom',
            ],
            [
                'phase' => 'contain',
                'action' => $input['risk_level'] === 'high'
                    ? 'Rollback or disable the AI-generated change before attempting a larger refactor.'
                    : 'Patch forward only if the root cause is already reproduced and the diff stays small.',
                'evidence_to_keep' => 'commit SHA, feature flag state, deployment time, and reviewer decision',
            ],
            [
                'phase' => 'repair',
                'action' => 'Write the missing failing test or source-check first, then apply the smallest verified fix.',
                'evidence_to_keep' => 'new failing test, fixed test result, and exact verification command',
            ],
            [
                'phase' => 'learn',
                'action' => 'Add the missed hallucination pattern to the prompt contract, red-team scenario, or CI policy.',
                'evidence_to_keep' => 'missed claim type, blocked_when rule, and updated guardrail',
            ],
        ];
    }

    /**
     * Return clear ownership so AI output does not become ownerless code.
     *
     * @return array<int, array{role: string, owns: string, failure_if_missing: string}>
     */
    private function ownershipModelFor(array $input): array
    {
        return [
            [
                'role' => 'AI operator',
                'owns' => 'Prompt scope, assumptions, evidence map, touched-file list, and verification command proposal.',
                'failure_if_missing' => 'The generated answer becomes a polished guess with no traceable source of truth.',
            ],
            [
                'role' => 'code reviewer',
                'owns' => 'Scope control, version checks, invented API rejection, and test relevance.',
                'failure_if_missing' => 'The patch may look clean while carrying wrong framework assumptions or unrelated edits.',
            ],
            [
                'role' => 'domain owner',
                'owns' => $input['ai_task'] === 'data-answer'
                    ? 'Factual correctness, source ledger quality, and user-facing answer safety.'
                    : 'Business behavior, edge cases, and acceptance criteria.',
                'failure_if_missing' => 'The AI output may satisfy code shape while violating the real product rule.',
            ],
            [
                'role' => 'release owner',
                'owns' => 'CI policy, deployment risk, observability, rollback readiness, and post-merge monitoring.',
                'failure_if_missing' => 'A missed hallucination can reach runtime without a clear rollback or alert path.',
            ],
        ];
    }

    /**
     * Return escalation rules for risky or incomplete AI output.
     *
     * @param  array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}  $input
     * @param  array{score: int, level: string, reasons: array<int, string>}  $riskScore
     * @return array<int, array{trigger: string, escalate_to: string, required_action: string}>
     */
    private function escalationPolicyFor(array $input, array $riskScore): array
    {
        $policy = [
            [
                'trigger' => 'Invented route, class, method, package API, config key, migration field, citation, or test result.',
                'escalate_to' => 'code reviewer',
                'required_action' => 'Block the answer until the claim is removed or proven with repo evidence.',
            ],
            [
                'trigger' => 'Patch touches auth, authorization, validation, secrets, files, payments, or data access.',
                'escalate_to' => 'security reviewer',
                'required_action' => 'Require explicit security review and targeted tests before release.',
            ],
        ];

        if ($riskScore['level'] === 'high') {
            $policy[] = [
                'trigger' => 'Risk score is high after scoring evidence, runtime, and review coverage.',
                'escalate_to' => 'senior reviewer',
                'required_action' => 'Require strict-blocking policy and close every release-readiness item.',
            ];
        }

        if ($input['ai_task'] === 'data-answer') {
            $policy[] = [
                'trigger' => 'Factual answer lacks source IDs, database rows, validated API responses, or citations.',
                'escalate_to' => 'domain owner',
                'required_action' => 'Return missing evidence instead of publishing generated facts.',
            ];
        }

        return $policy;
    }

    /**
     * Return a practical adoption path for teams.
     *
     * @return array<int, array{level: string, practice: string, exit_criteria: string}>
     */
    private function maturityRoadmapFor(array $input): array
    {
        return [
            [
                'level' => 'manual',
                'practice' => 'Use the prompt contract, evidence map, and reviewer checklist manually on risky AI-assisted tasks.',
                'exit_criteria' => 'Reviewers consistently reject invented APIs, missing evidence, and unverified claims.',
            ],
            [
                'level' => 'templated',
                'practice' => 'Move assumptions, evidence map, verification record, and scope summary into the pull request template.',
                'exit_criteria' => 'Every AI-assisted change includes required artifacts before review starts.',
            ],
            [
                'level' => 'automated',
                'practice' => 'Add CI checks for route lists, targeted tests, dependency/version review, and evidence-map completion.',
                'exit_criteria' => 'High-risk AI output cannot merge without required checks and manual gates.',
            ],
            [
                'level' => 'operational',
                'practice' => 'Track AI-output regressions with metrics, alerts, rollback playbooks, and recurring red-team scenarios.',
                'exit_criteria' => $input['risk_level'] === 'high'
                    ? 'High-risk AI changes have monitoring and rollback evidence before release.'
                    : 'Controlled AI changes have enough telemetry to detect drift after merge.',
            ],
        ];
    }

    /**
     * Return implementation steps for adding the guardrail to a team workflow.
     *
     * @return array<int, string>
     */
    private function implementationStepsFor(array $input): array
    {
        return [
            'Add a prompt preflight step that asks for assumptions, missing context, and required files before code generation.',
            'Require a response section named evidence map that ties each non-obvious claim to files, docs, logs, tests, or source rows.',
            'Add a review checklist item that rejects invented APIs, version drift, unrelated files, and unverified test claims.',
            $input['runtime_checks'] === 'yes'
                ? 'Store the verification command and result beside the task or pull request.'
                : 'Block release until a runtime verification command is chosen and executed.',
            $input['human_review'] === 'yes'
                ? 'Keep the human reviewer as the final approval owner for sensitive areas.'
                : 'Assign a reviewer before the AI output can be merged or shown to users.',
        ];
    }

    /**
     * Return scenarios that intentionally pressure the workflow into catching hallucinations.
     *
     * @return array<int, array{scenario: string, planted_risk: string, expected_guardrail: string}>
     */
    private function redTeamScenariosFor(array $input): array
    {
        $scenarios = [
            [
                'scenario' => 'Ask the model to use a class, helper, or facade method that sounds Laravel-like but is not in the current app.',
                'planted_risk' => 'Invented framework or package API.',
                'expected_guardrail' => 'Reject until the answer cites composer.json, vendor docs for the installed version, or existing repo usage.',
            ],
            [
                'scenario' => 'Ask for a fix without sharing the failing route, request payload, log, test, or reproduction step.',
                'planted_risk' => 'Confident root-cause story without evidence.',
                'expected_guardrail' => 'Force a hypothesis list and ask what evidence would confirm or reject each hypothesis before patching.',
            ],
            [
                'scenario' => 'Ask the model to patch a bug while also cleaning up nearby files.',
                'planted_risk' => 'Scope creep hidden inside a polished refactor.',
                'expected_guardrail' => 'Block unrelated file changes unless the acceptance case proves they are required.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $scenarios[] = [
                'scenario' => 'Ask for exact totals, names, links, or citations without retrieval results or database rows.',
                'planted_risk' => 'Fabricated factual data presented as if it came from a source.',
                'expected_guardrail' => 'Require source IDs, row IDs, validated API responses, or document citations before showing the answer.',
            ];
        }

        return $scenarios;
    }

    /**
     * Return automation hooks that make the guardrail repeatable.
     *
     * @return array<int, array{hook: string, purpose: string, example_check: string}>
     */
    private function automationHooksFor(array $input): array
    {
        return [
            [
                'hook' => 'pull request template',
                'purpose' => 'Make evidence, scope, and verification visible before review starts.',
                'example_check' => 'Require sections for assumptions, evidence map, touched files, verification commands, and unverified claims.',
            ],
            [
                'hook' => 'CI route and test check',
                'purpose' => 'Catch invented routes, missing classes, and broken request paths early.',
                'example_check' => 'Run route:list plus the feature test filter named by the task before approving the AI patch.',
            ],
            [
                'hook' => 'dependency/version check',
                'purpose' => 'Stop plausible APIs from another framework or package version.',
                'example_check' => 'Compare composer.json, lockfile package versions, and existing code usage before accepting new API calls.',
            ],
            [
                'hook' => 'security review label',
                'purpose' => 'Escalate risky AI output to a human reviewer when auth, validation, files, secrets, or data access are touched.',
                'example_check' => $input['human_review'] === 'yes'
                    ? 'Require reviewer approval on risky files before merge.'
                    : 'Block merge until a reviewer is assigned for risky files.',
            ],
        ];
    }

    /**
     * Return a prompt contract that forces the model to expose uncertainty.
     *
     * @return array<int, string>
     */
    private function promptContractFor(array $input): array
    {
        return [
            "Task type: {$input['ai_task']}. State assumptions first and label uncertain claims.",
            'Use only provided repo context unless you explicitly ask for missing files or commands.',
            'Return touched files, risky areas, verification commands, and rollback notes.',
            'Do not invent APIs, database columns, config keys, package methods, citations, or test results.',
        ];
    }

    /**
     * Return the output sections an AI response must include before review.
     *
     * @return array<int, array{section: string, required: bool, reject_if_missing: string}>
     */
    private function safeOutputContractFor(array $input): array
    {
        $contract = [
            [
                'section' => 'assumptions and unknowns',
                'required' => true,
                'reject_if_missing' => 'The response hides uncertainty and presents guesses as facts.',
            ],
            [
                'section' => 'evidence map',
                'required' => true,
                'reject_if_missing' => 'Claims cannot be traced to repo files, tests, logs, docs, or source rows.',
            ],
            [
                'section' => 'smallest scoped change',
                'required' => $input['ai_task'] !== 'data-answer',
                'reject_if_missing' => 'The patch may include unrelated edits or unreviewed behavior changes.',
            ],
            [
                'section' => 'verification record',
                'required' => true,
                'reject_if_missing' => 'The answer cannot prove the behavior through commands or reproduction evidence.',
            ],
            [
                'section' => 'unverified claims',
                'required' => true,
                'reject_if_missing' => 'Reviewers cannot tell which claims still need evidence.',
            ],
        ];

        if ($input['ai_task'] === 'data-answer') {
            $contract[] = [
                'section' => 'source ledger',
                'required' => true,
                'reject_if_missing' => 'Factual output can be published without source IDs or citations.',
            ];
        }

        return $contract;
    }

    /**
     * Return review lenses for the final human pass.
     *
     * @return array<int, array{lens: string, question: string}>
     */
    private function reviewChecklistFor(array $input): array
    {
        return [
            ['lens' => 'evidence', 'question' => 'Which facts came from files, tests, logs, docs, or database rows?'],
            ['lens' => 'version', 'question' => 'Does every suggested API exist in this framework and package version?'],
            ['lens' => 'scope', 'question' => 'Did the patch touch only files required by the requested behavior?'],
            ['lens' => 'runtime', 'question' => 'Which command or reproduction proves the answer works?'],
            ['lens' => 'authority', 'question' => $input['human_review'] === 'yes' ? 'Who owns the final approval?' : 'Who will review this before merge?'],
        ];
    }

    /**
     * Return test ideas for the guardrail workflow.
     *
     * @return array<int, string>
     */
    private function testsFor(array $input): array
    {
        return [
            'A patch that references a missing class, route, package API, or config key is rejected during review.',
            'The original symptom has a failing test or reproduction before the AI patch is accepted.',
            'Generated code cannot bypass validation, authorization, or existing service boundaries.',
            $input['runtime_checks'] === 'yes'
                ? 'The planned verification command is run and recorded with the result.'
                : 'The output is marked unverified until runtime checks are added.',
        ];
    }

    /**
     * Return an interview-ready explanation.
     */
    private function interviewAnswerFor(array $input): string
    {
        return "I reduce AI hallucination in code by forcing evidence. The model must state assumptions, cite repo files or source data, avoid inventing APIs, keep the patch small, and provide verification commands. For {$input['risk_level']}-risk {$input['ai_task']} work, I would not accept the output until tests or runtime checks prove the behavior and a human reviewer checks auth, validation, version, dependency, and scope risk.";
    }
}

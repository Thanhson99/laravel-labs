<?php

declare(strict_types=1);

namespace App\Services\Practice;

/**
 * Build a governance-first memory plan for developer-style AI agents.
 */
final class AiAgentMemoryPlanService
{
    /**
     * Build a memory contract plan from learner-selected constraints.
     *
     * @param  array{agent_profile: string, storage_scope: string, retention_policy: string, privacy_mode: string, staleness_policy: string}  $input
     * @return array{summary: array{main_claim: string, scope: string, risk_posture: string}, memory_contracts: array<int, array{name: string, purpose: string, allowed_data: array<int, string>, retrieval_rule: string, safety_check: string}>, retrieval_order: array<int, string>, governance_controls: array<int, string>, failure_modes: array<int, array{risk: string, guardrail: string}>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        return [
            'summary' => $this->summaryFor($input),
            'memory_contracts' => $this->memoryContractsFor($input),
            'retrieval_order' => $this->retrievalOrderFor($input),
            'governance_controls' => $this->governanceControlsFor($input),
            'failure_modes' => $this->failureModesFor($input),
            'interview_answer' => $this->interviewAnswerFor($input),
            'commands' => [
                'php artisan route:list --path=ai-agent-memory-plan',
                'php artisan test --filter AiAgentMemoryPlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the top-level framing.
     *
     * @param  array{agent_profile: string, storage_scope: string, retention_policy: string, privacy_mode: string, staleness_policy: string}  $input
     * @return array{main_claim: string, scope: string, risk_posture: string}
     */
    private function summaryFor(array $input): array
    {
        return [
            'main_claim' => 'AI agent memory should be split into working, episodic, semantic, and procedural contracts before it is stored or retrieved.',
            'scope' => "For a {$input['agent_profile']} profile, store memory as {$input['storage_scope']} with {$input['retention_policy']} retention.",
            'risk_posture' => $input['privacy_mode'] === 'strict'
                ? 'Strict privacy mode requires explicit source, permission scope, and redaction before reuse.'
                : 'Balanced privacy mode still requires source, permission scope, and stale-memory handling before reuse.',
        ];
    }

    /**
     * Return four concrete memory contracts.
     *
     * @param  array{agent_profile: string, storage_scope: string, retention_policy: string, privacy_mode: string, staleness_policy: string}  $input
     * @return array<int, array{name: string, purpose: string, allowed_data: array<int, string>, retrieval_rule: string, safety_check: string}>
     */
    private function memoryContractsFor(array $input): array
    {
        return [
            [
                'name' => 'working_memory',
                'purpose' => 'Hold the current task state while the agent is actively working.',
                'allowed_data' => ['current goal', 'open files', 'latest user instruction', 'failing test', 'next action'],
                'retrieval_rule' => 'Read first for the current turn and discard when the task or session boundary changes.',
                'safety_check' => 'Never promote temporary task state into durable memory without review.',
            ],
            [
                'name' => 'episodic_memory',
                'purpose' => 'Remember prior sessions, decisions, commands, failed attempts, and reviewer feedback.',
                'allowed_data' => ['session summary', 'commands run', 'decisions made', 'review notes', 'known failed attempts'],
                'retrieval_rule' => $input['storage_scope'] === 'session-only'
                    ? 'Retrieve only from the current session transcript and do not reuse across projects.'
                    : 'Retrieve when the source project, user, and permission boundary match.',
                'safety_check' => 'Reject private or cross-user memories unless the actor has permission to reuse them.',
            ],
            [
                'name' => 'semantic_memory',
                'purpose' => 'Store durable facts about the repo, domain, data model, API contracts, and team rules.',
                'allowed_data' => ['repo rule', 'domain term', 'API contract', 'data model fact', 'team convention'],
                'retrieval_rule' => $this->semanticRetrievalRuleFor($input['staleness_policy']),
                'safety_check' => 'Require source path, freshness, confidence, and correction path for every durable fact.',
            ],
            [
                'name' => 'procedural_memory',
                'purpose' => 'Store repeatable playbooks for debugging, testing, releases, review, and rollback.',
                'allowed_data' => ['debug loop', 'test command', 'PR checklist', 'release step', 'incident playbook'],
                'retrieval_rule' => 'Retrieve only when the playbook matches the current technology, environment, and risk level.',
                'safety_check' => 'Prefer reviewed playbooks and require verification commands before acting.',
            ],
        ];
    }

    /**
     * Return the safe retrieval sequence for an agent task.
     *
     * @return array<int, string>
     */
    private function retrievalOrderFor(array $input): array
    {
        $order = [
            'Read working_memory for the latest user goal, constraints, open files, and failing evidence.',
            'Read semantic_memory only when source, freshness, confidence, and permission match the current repo.',
            'Read procedural_memory for the smallest reviewed playbook that fits the task.',
            'Read episodic_memory last, because prior sessions can be useful but stale, private, or overfit to old context.',
        ];

        if ($input['staleness_policy'] === 'refresh-before-use') {
            $order[] = 'Refresh stale semantic facts from source files or tests before using them in an action.';
        }

        return $order;
    }

    /**
     * Return governance controls required before memory can steer actions.
     *
     * @return array<int, string>
     */
    private function governanceControlsFor(array $input): array
    {
        $controls = [
            'Scope every memory by user, repo, project, environment, and permission boundary.',
            'Store source, observed_at, confidence, retention, and correction_path with every memory item.',
            'Separate temporary working memory from durable semantic or procedural memory.',
            'Block memory reuse when the current actor cannot inspect the original source.',
        ];

        if ($input['privacy_mode'] === 'strict') {
            $controls[] = 'Redact secrets, personal data, customer data, and private session notes before storage.';
        }

        if ($input['retention_policy'] === 'reviewed-durable') {
            $controls[] = 'Require human or test-backed review before promoting memory into durable storage.';
        }

        return $controls;
    }

    /**
     * Return common failure modes and guardrails.
     *
     * @return array<int, array{risk: string, guardrail: string}>
     */
    private function failureModesFor(array $input): array
    {
        return [
            [
                'risk' => 'stale_semantic_memory',
                'guardrail' => $input['staleness_policy'] === 'block-stale'
                    ? 'Block the action until the fact is refreshed from source.'
                    : 'Warn, refresh, or lower confidence before the fact affects a code edit.',
            ],
            [
                'risk' => 'private_episodic_memory',
                'guardrail' => 'Do not reuse session notes across users, repos, or permission scopes.',
            ],
            [
                'risk' => 'procedural_mismatch',
                'guardrail' => 'Reject playbooks that target the wrong framework version, environment, or risk level.',
            ],
            [
                'risk' => 'working_memory_leak',
                'guardrail' => 'Discard temporary task details at the boundary unless they pass promotion review.',
            ],
        ];
    }

    /**
     * Return retrieval guidance for semantic memory.
     */
    private function semanticRetrievalRuleFor(string $stalenessPolicy): string
    {
        return match ($stalenessPolicy) {
            'block-stale' => 'Retrieve only when the source is fresh enough; block stale facts until refreshed.',
            'warn-on-stale' => 'Retrieve stale facts only with a warning and lower confidence.',
            default => 'Refresh the source file, test, or API contract before using the fact.',
        };
    }

    /**
     * Return an interview-ready explanation.
     *
     * @param  array{agent_profile: string, storage_scope: string, retention_policy: string, privacy_mode: string, staleness_policy: string}  $input
     */
    private function interviewAnswerFor(array $input): string
    {
        return "I would not treat AI agent memory as one big prompt history. For a {$input['agent_profile']}, I would split it into working memory for the current task, episodic memory for prior sessions, semantic memory for durable repo facts, and procedural memory for reviewed playbooks. Each memory item needs source, freshness, confidence, permission scope, retention, and a correction path. With {$input['privacy_mode']} privacy and {$input['staleness_policy']} stale handling, stale or private memory cannot silently steer the next action.";
    }
}

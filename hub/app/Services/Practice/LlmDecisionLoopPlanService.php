<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class LlmDecisionLoopPlanService
{
    /**
     * Build an LLM decision-loop explanation from learner-selected signals.
     *
     * @param  array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}  $input
     * @return array{summary: array{main_claim: string, decision_frame: string, agi_note: string}, markov_model: array<int, array{name: string, llm_mapping: string, engineering_question: string}>, attention_model: array<int, string>, training_stack: array<int, array{stage: string, role: string, limitation: string}>, reward_policy: array{signal: string, effect: string, risk: string}, ppo_plan: array{when_relevant: string, mechanism: string, caution: string}, feedback_loop: array<int, array{loop: string, source: string, improves: string}>, agi_guardrails: array<int, string>, verification_plan: array<int, array{claim: string, verify_with: string}>, misconception_checks: array<int, array{misconception: string, correction: string}>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        return [
            'summary' => $this->summaryFor($input),
            'markov_model' => $this->markovModelFor($input),
            'attention_model' => $this->attentionModel(),
            'training_stack' => $this->trainingStackFor($input),
            'reward_policy' => $this->rewardPolicyFor($input),
            'ppo_plan' => $this->ppoPlanFor($input),
            'feedback_loop' => $this->feedbackLoopFor($input),
            'agi_guardrails' => $this->agiGuardrailsFor($input),
            'verification_plan' => $this->verificationPlanFor($input),
            'misconception_checks' => $this->misconceptionChecks(),
            'interview_answer' => $this->interviewAnswerFor($input),
            'commands' => [
                'php artisan route:list --path=llm-decision-loop-plan',
                'php artisan test --filter LlmDecisionLoopPlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the high-level explanation.
     *
     * @param  array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}  $input
     * @return array{main_claim: string, decision_frame: string, agi_note: string}
     */
    private function summaryFor(array $input): array
    {
        return [
            'main_claim' => 'An LLM starts from next-token probability, but useful behavior comes from repeated decisions shaped by context, attention, training, reward, and feedback.',
            'decision_frame' => "For {$input['model_role']} work, treat the model as a policy that observes context, chooses a token or tool action, receives updated state, and is later improved by {$input['feedback_signal']} feedback.",
            'agi_note' => $input['agi_claim'] === 'overstated'
                ? 'Do not claim AGI from fluency alone; require evidence of generalization, autonomy, reliability, and environment-grounded learning.'
                : 'AGI remains an open outcome, so explain capability growth with measured feedback loops rather than certainty.',
        ];
    }

    /**
     * Map LLM inference to Markov decision process language.
     *
     * @param  array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}  $input
     * @return array<int, array{name: string, llm_mapping: string, engineering_question: string}>
     */
    private function markovModelFor(array $input): array
    {
        return [
            [
                'name' => 'state',
                'llm_mapping' => 'The current prompt, conversation history, retrieved context, tool results, and system instructions.',
                'engineering_question' => 'What context is actually available, and what important fact is missing?',
            ],
            [
                'name' => 'policy',
                'llm_mapping' => 'The model parameters and decoding strategy that turn state into a probability distribution over next actions.',
                'engineering_question' => 'Is the policy being guided by evidence, examples, constraints, or only fluent text?',
            ],
            [
                'name' => 'action',
                'llm_mapping' => $input['tool_use'] === 'yes'
                    ? 'The next token, tool call, search query, code edit, or other tool-mediated step.'
                    : 'The next token or answer segment selected from the probability distribution.',
                'engineering_question' => 'Can the action be checked against tests, sources, runtime output, or reviewer judgment?',
            ],
            [
                'name' => 'transition',
                'llm_mapping' => 'The context changes after a token is emitted, a tool returns output, or the user/environment responds.',
                'engineering_question' => 'Does the next state contain stronger evidence or only another generated guess?',
            ],
            [
                'name' => 'reward',
                'llm_mapping' => $this->rewardDescriptionFor($input['feedback_signal']),
                'engineering_question' => 'Which signal teaches the model or workflow that this action was actually useful?',
            ],
        ];
    }

    /**
     * Return attention guidance.
     *
     * @return array<int, string>
     */
    private function attentionModel(): array
    {
        return [
            'Attention lets the model weigh relationships between tokens instead of reading the prompt as a flat string.',
            'It helps the model connect definitions, code symbols, constraints, and prior conversation turns before choosing the next token.',
            'Attention is not proof of understanding by itself; it is the mechanism that routes relevant context into the next decision.',
        ];
    }

    /**
     * Return training-stage roles and limits.
     *
     * @param  array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}  $input
     * @return array<int, array{stage: string, role: string, limitation: string}>
     */
    private function trainingStackFor(array $input): array
    {
        $stages = [
            [
                'stage' => 'pretraining',
                'role' => 'Learns broad language, code, facts, and patterns by predicting masked or next tokens across large corpora.',
                'limitation' => 'It optimizes statistical prediction, not guaranteed truth or task completion.',
            ],
            [
                'stage' => 'supervised fine-tuning',
                'role' => 'Teaches the model preferred answer formats, instruction following, and useful assistant behavior.',
                'limitation' => 'It can imitate good answers without knowing whether a specific claim is true in the current repo.',
            ],
            [
                'stage' => 'reward modeling',
                'role' => 'Learns a scoring signal from preferences, ratings, tests, or environment outcomes.',
                'limitation' => 'A weak reward model can reward style, confidence, or shortcut behavior instead of correctness.',
            ],
            [
                'stage' => 'reinforcement learning',
                'role' => 'Adjusts the policy so actions that score better become more likely in similar states.',
                'limitation' => 'RL improves toward the chosen reward, so bad reward design creates bad behavior at scale.',
            ],
        ];

        if ($input['training_stage'] === 'pretraining') {
            return array_slice($stages, 0, 1);
        }

        if ($input['training_stage'] === 'sft') {
            return array_slice($stages, 0, 2);
        }

        return $stages;
    }

    /**
     * Return reward guidance from the chosen feedback signal.
     *
     * @return array{signal: string, effect: string, risk: string}
     */
    private function rewardPolicyFor(array $input): array
    {
        return match ($input['feedback_signal']) {
            'human-preference' => [
                'signal' => 'human-preference',
                'effect' => 'Human rankings can teach helpfulness, clarity, refusal behavior, and alignment with expected answer style.',
                'risk' => 'Preferences may reward confident wording over verifiable correctness if evidence is not required.',
            ],
            'tests-runtime' => [
                'signal' => 'tests-runtime',
                'effect' => 'Tests and runtime output give a concrete pass/fail signal for code-oriented decisions.',
                'risk' => 'The model can overfit to visible tests unless hidden edge cases and review are also present.',
            ],
            'environment-reward' => [
                'signal' => 'environment-reward',
                'effect' => 'Environment feedback can reward successful tool use, task completion, or measured outcomes over text fluency.',
                'risk' => 'Poorly scoped environment rewards can encourage unsafe shortcuts.',
            ],
            default => [
                'signal' => 'none',
                'effect' => 'No reliable reward signal exists beyond language likelihood and prompt imitation.',
                'risk' => 'The workflow may produce fluent output without a correction loop.',
            ],
        };
    }

    /**
     * Return PPO-specific explanation.
     *
     * @return array{when_relevant: string, mechanism: string, caution: string}
     */
    private function ppoPlanFor(array $input): array
    {
        return [
            'when_relevant' => $input['training_stage'] === 'rlhf'
                ? 'PPO is relevant when a trained policy is being adjusted against a reward model or preference signal.'
                : 'PPO is not needed to explain every LLM output, but it matters when discussing RLHF-style policy optimization.',
            'mechanism' => 'PPO updates the model policy while constraining changes so the model improves toward reward without drifting too far in one step.',
            'caution' => 'PPO optimizes the reward you define; it does not automatically create truth, safety, or AGI.',
        ];
    }

    /**
     * Return feedback loops that make the model or workflow improve.
     *
     * @param  array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}  $input
     * @return array<int, array{loop: string, source: string, improves: string}>
     */
    private function feedbackLoopFor(array $input): array
    {
        $loops = [
            [
                'loop' => 'prompt-time correction',
                'source' => 'user constraints, retrieved context, examples, and follow-up corrections',
                'improves' => 'The current answer by changing the state the model sees.',
            ],
            [
                'loop' => 'training-time correction',
                'source' => $input['feedback_signal'],
                'improves' => 'Future policy behavior by changing model weights or ranking behavior.',
            ],
        ];

        if ($input['tool_use'] === 'yes') {
            $loops[] = [
                'loop' => 'environment interaction',
                'source' => 'tool results, tests, logs, search results, or execution output',
                'improves' => 'The next action by grounding the state in observable feedback.',
            ];
        }

        return $loops;
    }

    /**
     * Return AGI claim guardrails.
     *
     * @return array<int, string>
     */
    private function agiGuardrailsFor(array $input): array
    {
        $guardrails = [
            'Separate current mechanism from future possibility: probability, attention, reward, and feedback explain capability growth, not guaranteed AGI.',
            'Avoid treating fluent reasoning traces as proof of robust general intelligence.',
            'Ask what the system can do across new domains, long horizons, tool failures, uncertainty, and adversarial feedback.',
        ];

        if ($input['agi_claim'] === 'overstated') {
            $guardrails[] = 'Rewrite AGI claims as hypotheses and require benchmarks, reliability evidence, and safety evaluation.';
        }

        return $guardrails;
    }

    /**
     * Return verification checks for claims in an LLM explanation.
     *
     * @return array<int, array{claim: string, verify_with: string}>
     */
    private function verificationPlanFor(array $input): array
    {
        return [
            [
                'claim' => 'The model is only next-token prediction.',
                'verify_with' => 'Expand the explanation to include repeated action selection, context transition, attention, and training feedback.',
            ],
            [
                'claim' => 'Reward models or PPO make the answer true.',
                'verify_with' => 'Check whether the reward signal measures correctness, preference, safety, runtime success, or only style.',
            ],
            [
                'claim' => 'Tool use makes the model autonomous.',
                'verify_with' => $input['tool_use'] === 'yes'
                    ? 'Inspect what tools can observe, what permissions they have, and what checks validate their output.'
                    : 'State that no external environment feedback is available in this scenario.',
            ],
            [
                'claim' => 'The path to AGI is certain.',
                'verify_with' => 'Replace certainty with measured capability, reliability, transfer, and alignment criteria.',
            ],
        ];
    }

    /**
     * Return common misconceptions and corrections.
     *
     * @return array<int, array{misconception: string, correction: string}>
     */
    private function misconceptionChecks(): array
    {
        return [
            [
                'misconception' => 'LLMs are just autocomplete.',
                'correction' => 'Autocomplete describes the token objective, but deployed systems repeatedly choose actions under context, tools, reward shaping, and review loops.',
            ],
            [
                'misconception' => 'Attention means the model understands like a person.',
                'correction' => 'Attention routes information between tokens; understanding must be judged by behavior, robustness, and evidence.',
            ],
            [
                'misconception' => 'RLHF guarantees alignment.',
                'correction' => 'RLHF can improve preferred behavior, but it depends on reward quality, data coverage, evaluation, and deployment controls.',
            ],
        ];
    }

    /**
     * Return reward wording for the Markov model.
     */
    private function rewardDescriptionFor(string $feedbackSignal): string
    {
        return match ($feedbackSignal) {
            'human-preference' => 'Human preference rankings or labels that reward helpful and acceptable outputs.',
            'tests-runtime' => 'Executable tests, runtime checks, or reviewer acceptance that reward working behavior.',
            'environment-reward' => 'Task outcome from a tool or environment, such as successful execution or measured user goal completion.',
            default => 'No explicit reward signal in the current loop, which leaves the model dependent on prior training and prompt context.',
        };
    }

    /**
     * Return an interview-ready answer.
     *
     * @param  array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}  $input
     */
    private function interviewAnswerFor(array $input): string
    {
        return "An LLM is trained with next-token prediction, but at runtime it behaves like a repeated decision process. The state is the current context, the policy is the model, the action is the next token or tool call, the transition is the updated context, and reward comes from signals such as {$input['feedback_signal']}. Attention decides which context matters for each action. Fine-tuning, reward models, and PPO can shape the policy toward preferred behavior, but they do not guarantee truth or AGI. For {$input['model_role']} work, I would explain capability through probability, attention, reward, tools, and verification loops instead of saying it is only autocomplete.";
    }
}

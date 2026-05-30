<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanLlmDecisionLoopRequest extends FormRequest
{
    /**
     * Allow public LLM decision-loop planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for LLM decision-loop input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'model_role' => ['required', 'string', Rule::in(['coding-assistant', 'research-assistant', 'tool-agent', 'chat-assistant'])],
            'training_stage' => ['required', 'string', Rule::in(['pretraining', 'sft', 'rlhf'])],
            'feedback_signal' => ['required', 'string', Rule::in(['none', 'human-preference', 'tests-runtime', 'environment-reward'])],
            'tool_use' => ['required', 'string', Rule::in(['yes', 'no'])],
            'agi_claim' => ['required', 'string', Rule::in(['conservative', 'optimistic', 'overstated'])],
        ];
    }

    /**
     * Return normalized LLM decision-loop input.
     *
     * @return array{model_role: string, training_stage: string, feedback_signal: string, tool_use: string, agi_claim: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'model_role' => (string) $validated['model_role'],
            'training_stage' => (string) $validated['training_stage'],
            'feedback_signal' => (string) $validated['feedback_signal'],
            'tool_use' => (string) $validated['tool_use'],
            'agi_claim' => (string) $validated['agi_claim'],
        ];
    }
}

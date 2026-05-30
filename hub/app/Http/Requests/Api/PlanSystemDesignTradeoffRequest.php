<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanSystemDesignTradeoffRequest extends FormRequest
{
    /**
     * Allow public system-design planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for the tradeoff planner.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'scenario' => ['required', 'string', Rule::in(['notification-10m', 'payment-flow', 'legacy-scale', 'startup-microservices'])],
            'latency_requirement' => ['required', 'string', Rule::in(['real-time', 'near-real-time', 'delayed'])],
            'failure_impact' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'consistency_need' => ['required', 'string', Rule::in(['eventual', 'strong'])],
            'team_maturity' => ['required', 'string', Rule::in(['small', 'medium', 'platform'])],
            'operational_capacity' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'current_constraint' => ['required', 'string', Rule::in(['greenfield', 'legacy-database', 'fast-delivery', 'regulated'])],
            'target_level' => ['sometimes', 'string', Rule::in(['l4', 'l5', 'l6', 'l7'])],
            'candidate_answer' => ['sometimes', 'nullable', 'string', 'max:4000'],
        ];
    }

    /**
     * Return normalized planning input.
     *
     * @return array{scenario: string, latency_requirement: string, failure_impact: string, consistency_need: string, team_maturity: string, operational_capacity: string, current_constraint: string, target_level: string, candidate_answer: string|null}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'scenario' => (string) $validated['scenario'],
            'latency_requirement' => (string) $validated['latency_requirement'],
            'failure_impact' => (string) $validated['failure_impact'],
            'consistency_need' => (string) $validated['consistency_need'],
            'team_maturity' => (string) $validated['team_maturity'],
            'operational_capacity' => (string) $validated['operational_capacity'],
            'current_constraint' => (string) $validated['current_constraint'],
            'target_level' => (string) ($validated['target_level'] ?? 'l6'),
            'candidate_answer' => isset($validated['candidate_answer']) ? (string) $validated['candidate_answer'] : null,
        ];
    }
}

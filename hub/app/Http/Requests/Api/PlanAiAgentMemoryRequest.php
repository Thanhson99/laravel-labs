<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate input for the AI agent memory planning workbench.
 */
final class PlanAiAgentMemoryRequest extends FormRequest
{
    /**
     * Allow public AI agent memory planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for memory planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'agent_profile' => ['required', 'string', Rule::in(['developer-agent', 'support-agent', 'research-agent'])],
            'storage_scope' => ['required', 'string', Rule::in(['session-only', 'project-scoped', 'user-scoped'])],
            'retention_policy' => ['required', 'string', Rule::in(['short-lived', 'reviewed-durable', 'rolling-window'])],
            'privacy_mode' => ['required', 'string', Rule::in(['strict', 'balanced'])],
            'staleness_policy' => ['required', 'string', Rule::in(['block-stale', 'warn-on-stale', 'refresh-before-use'])],
        ];
    }

    /**
     * Return normalized planner input.
     *
     * @return array{agent_profile: string, storage_scope: string, retention_policy: string, privacy_mode: string, staleness_policy: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'agent_profile' => (string) $validated['agent_profile'],
            'storage_scope' => (string) $validated['storage_scope'],
            'retention_policy' => (string) $validated['retention_policy'],
            'privacy_mode' => (string) $validated['privacy_mode'],
            'staleness_policy' => (string) $validated['staleness_policy'],
        ];
    }
}

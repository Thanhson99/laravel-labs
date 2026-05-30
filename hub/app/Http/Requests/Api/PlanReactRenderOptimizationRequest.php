<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanReactRenderOptimizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'component_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'component_type' => ['required', 'string', Rule::in(['table', 'form', 'dashboard', 'search-panel', 'list-item'])],
            'render_issue' => ['required', 'string', Rule::in(['prop-churn', 'large-list', 'expensive-calculation', 'context-update', 'unknown'])],
            'state_shape' => ['required', 'string', Rule::in(['local-state', 'parent-state', 'context-state', 'global-state'])],
            'list_size' => ['required', 'integer', 'min:1', 'max:10000'],
            'profiler_signal' => ['required', 'string', Rule::in(['slow-commit', 'prop-churn', 'many-children', 'not-measured'])],
        ];
    }

    /**
     * @return array{component_name: string, component_type: string, render_issue: string, state_shape: string, list_size: int, profiler_signal: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'component_name' => trim((string) $validated['component_name']),
            'component_type' => (string) $validated['component_type'],
            'render_issue' => (string) $validated['render_issue'],
            'state_shape' => (string) $validated['state_shape'],
            'list_size' => (int) $validated['list_size'],
            'profiler_signal' => (string) $validated['profiler_signal'],
        ];
    }
}

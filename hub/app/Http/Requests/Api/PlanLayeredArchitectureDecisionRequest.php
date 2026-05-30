<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanLayeredArchitectureDecisionRequest extends FormRequest
{
    /**
     * Allow public layered-architecture decision requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for layered architecture decision input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'feature_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'feature_type' => ['required', 'string', Rule::in(['crud', 'workflow', 'integration'])],
            'business_rule_count' => ['required', 'integer', 'min:0', 'max:12'],
            'integration_count' => ['required', 'integer', 'min:0', 'max:8'],
            'persistence_complexity' => ['required', 'string', Rule::in(['none', 'simple', 'complex'])],
            'requires_async_work' => ['required', 'boolean'],
            'requires_policy' => ['required', 'boolean'],
        ];
    }

    /**
     * Return normalized layered architecture decision input.
     *
     * @return array{feature_name: string, feature_type: string, business_rule_count: int, integration_count: int, persistence_complexity: string, requires_async_work: bool, requires_policy: bool}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'feature_name' => trim((string) $validated['feature_name']),
            'feature_type' => (string) $validated['feature_type'],
            'business_rule_count' => (int) $validated['business_rule_count'],
            'integration_count' => (int) $validated['integration_count'],
            'persistence_complexity' => (string) $validated['persistence_complexity'],
            'requires_async_work' => $this->boolean('requires_async_work'),
            'requires_policy' => $this->boolean('requires_policy'),
        ];
    }
}

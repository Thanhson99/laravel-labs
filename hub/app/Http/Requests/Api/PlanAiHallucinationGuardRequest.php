<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanAiHallucinationGuardRequest extends FormRequest
{
    /**
     * Allow public AI hallucination guard planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for hallucination guard planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'ai_task' => ['required', 'string', Rule::in(['code-generation', 'code-review', 'data-answer', 'refactor', 'debugging'])],
            'risk_level' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'evidence_sources' => ['required', 'string', Rule::in(['none', 'partial', 'repo-context', 'tests-docs'])],
            'runtime_checks' => ['required', 'string', Rule::in(['yes', 'no'])],
            'human_review' => ['required', 'string', Rule::in(['yes', 'no'])],
        ];
    }

    /**
     * Return normalized hallucination guard planning input.
     *
     * @return array{ai_task: string, risk_level: string, evidence_sources: string, runtime_checks: string, human_review: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'ai_task' => (string) $validated['ai_task'],
            'risk_level' => (string) $validated['risk_level'],
            'evidence_sources' => (string) $validated['evidence_sources'],
            'runtime_checks' => (string) $validated['runtime_checks'],
            'human_review' => (string) $validated['human_review'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanOopAbstractionDecisionRequest extends FormRequest
{
    /**
     * Allow public OOP abstraction decision requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for OOP abstraction decision input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'scenario' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'relationship' => ['required', 'string', Rule::in(['unrelated', 'same-family', 'single-class'])],
            'shared_behavior' => ['nullable', 'string', 'max:140', 'regex:/^[A-Za-z0-9 _.,-]+$/'],
            'needs_multiple_implementations' => ['required', 'boolean'],
            'has_shared_state' => ['required', 'boolean'],
        ];
    }

    /**
     * Return normalized OOP abstraction decision input.
     *
     * @return array{scenario: string, relationship: string, shared_behavior: string, needs_multiple_implementations: bool, has_shared_state: bool}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'scenario' => trim((string) $validated['scenario']),
            'relationship' => (string) $validated['relationship'],
            'shared_behavior' => trim((string) ($validated['shared_behavior'] ?? '')),
            'needs_multiple_implementations' => $this->boolean('needs_multiple_implementations'),
            'has_shared_state' => $this->boolean('has_shared_state'),
        ];
    }
}

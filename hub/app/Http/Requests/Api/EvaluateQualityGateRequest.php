<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class EvaluateQualityGateRequest extends FormRequest
{
    /**
     * Allow public practice quality-gate requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for quality-gate input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'tests' => ['required', 'integer', 'min:0', 'max:10000'],
            'assertions' => ['required', 'integer', 'min:0', 'max:100000'],
            'failures' => ['required', 'integer', 'min:0', 'max:10000'],
            'pint' => ['required', 'boolean'],
        ];
    }

    /**
     * Return normalized verification results.
     *
     * @return array{tests: int, assertions: int, failures: int, pint: bool}
     */
    public function results(): array
    {
        $validated = $this->validated();

        return [
            'tests' => (int) $validated['tests'],
            'assertions' => (int) $validated['assertions'],
            'failures' => (int) $validated['failures'],
            'pint' => (bool) $validated['pint'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanContainerBindingRequest extends FormRequest
{
    /**
     * Allow public container binding planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for container binding planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'contract_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'implementation_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'lifetime' => ['required', 'string', Rule::in(['bind', 'singleton'])],
            'injection_target' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
        ];
    }

    /**
     * Return normalized container binding planning input.
     *
     * @return array{contract_name: string, implementation_name: string, lifetime: string, injection_target: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'contract_name' => trim((string) $validated['contract_name']),
            'implementation_name' => trim((string) $validated['implementation_name']),
            'lifetime' => (string) $validated['lifetime'],
            'injection_target' => trim((string) $validated['injection_target']),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanRateLimitRequest extends FormRequest
{
    /**
     * Allow public rate-limit planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for rate-limit planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'endpoint_name' => ['required', 'string', 'min:3', 'max:80'],
            'actor_type' => ['required', 'string', Rule::in(['ip', 'user', 'team', 'api-token'])],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:1000'],
            'decay_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'sensitivity' => ['required', 'string', Rule::in(['normal', 'sensitive', 'bulk'])],
        ];
    }

    /**
     * Return normalized rate-limit planning input.
     *
     * @return array{endpoint_name: string, actor_type: string, max_attempts: int, decay_minutes: int, sensitivity: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'endpoint_name' => trim((string) $validated['endpoint_name']),
            'actor_type' => (string) $validated['actor_type'],
            'max_attempts' => (int) $validated['max_attempts'],
            'decay_minutes' => (int) $validated['decay_minutes'],
            'sensitivity' => (string) $validated['sensitivity'],
        ];
    }
}

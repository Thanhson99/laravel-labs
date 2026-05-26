<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class PlanCacheStrategyRequest extends FormRequest
{
    /**
     * Allow public cache strategy planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for cache strategy planning input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'resource_name' => ['required', 'string', 'min:3', 'max:80'],
            'scope' => ['required', 'string', 'min:3', 'max:80'],
            'ttl_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'invalidation_trigger' => ['required', 'string', 'min:5', 'max:160'],
        ];
    }

    /**
     * Return normalized cache strategy planning input.
     *
     * @return array{resource_name: string, scope: string, ttl_minutes: int, invalidation_trigger: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'resource_name' => trim((string) $validated['resource_name']),
            'scope' => trim((string) $validated['scope']),
            'ttl_minutes' => (int) $validated['ttl_minutes'],
            'invalidation_trigger' => trim((string) $validated['invalidation_trigger']),
        ];
    }
}

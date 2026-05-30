<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ReviewIdorAccessRequest extends FormRequest
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
            'resource_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'route_pattern' => ['required', 'string', 'min:5', 'max:120'],
            'access_model' => ['required', 'string', Rule::in(['owner', 'tenant', 'team', 'role', 'public'])],
            'uses_policy' => ['required', 'boolean'],
            'query_scoped' => ['required', 'boolean'],
            'attacker_changes_id' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array{resource_name: string, route_pattern: string, access_model: string, uses_policy: bool, query_scoped: bool, attacker_changes_id: bool}
     */
    public function reviewData(): array
    {
        $validated = $this->validated();

        return [
            'resource_name' => trim((string) $validated['resource_name']),
            'route_pattern' => trim((string) $validated['route_pattern']),
            'access_model' => (string) $validated['access_model'],
            'uses_policy' => (bool) $validated['uses_policy'],
            'query_scoped' => (bool) $validated['query_scoped'],
            'attacker_changes_id' => (bool) $validated['attacker_changes_id'],
        ];
    }
}

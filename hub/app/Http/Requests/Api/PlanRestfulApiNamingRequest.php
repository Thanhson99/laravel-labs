<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanRestfulApiNamingRequest extends FormRequest
{
    /**
     * Allow public workbench planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for RESTful naming planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'resource_name' => ['required', 'string', 'min:2', 'max:60'],
            'parent_resource' => ['nullable', 'string', 'min:2', 'max:60'],
            'current_endpoint' => ['nullable', 'string', 'max:120'],
            'operation_type' => ['required', 'string', Rule::in(['read', 'create', 'update', 'delete', 'action'])],
            'needs_filtering' => ['required', 'boolean'],
            'needs_business_action' => ['required', 'boolean'],
            'version' => ['required', 'string', Rule::in(['v1', 'v2'])],
        ];
    }

    /**
     * Return normalized RESTful naming plan input.
     *
     * @return array{resource_name: string, parent_resource: string|null, current_endpoint: string|null, operation_type: string, needs_filtering: bool, needs_business_action: bool, version: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'resource_name' => trim((string) $validated['resource_name']),
            'parent_resource' => filled($validated['parent_resource'] ?? null)
                ? trim((string) $validated['parent_resource'])
                : null,
            'current_endpoint' => filled($validated['current_endpoint'] ?? null)
                ? trim((string) $validated['current_endpoint'])
                : null,
            'operation_type' => (string) $validated['operation_type'],
            'needs_filtering' => $this->boolean('needs_filtering'),
            'needs_business_action' => $this->boolean('needs_business_action'),
            'version' => (string) $validated['version'],
        ];
    }
}

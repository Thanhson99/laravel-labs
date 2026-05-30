<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanSqlInjectionDefenseRequest extends FormRequest
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
            'query_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'query_style' => ['required', 'string', Rule::in(['eloquent', 'query-builder', 'raw-sql'])],
            'input_surface' => ['required', 'string', Rule::in(['search-box', 'login-form', 'filter-api', 'admin-report'])],
            'dynamic_parts' => ['required', 'string', Rule::in(['where-value', 'order-by', 'table-name', 'column-name'])],
            'uses_bindings' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array{query_name: string, query_style: string, input_surface: string, dynamic_parts: string, uses_bindings: bool}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'query_name' => trim((string) $validated['query_name']),
            'query_style' => (string) $validated['query_style'],
            'input_surface' => (string) $validated['input_surface'],
            'dynamic_parts' => (string) $validated['dynamic_parts'],
            'uses_bindings' => (bool) $validated['uses_bindings'],
        ];
    }
}

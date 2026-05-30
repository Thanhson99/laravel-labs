<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanCsrfProtectionRequest extends FormRequest
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
            'flow_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'client_type' => ['required', 'string', Rule::in(['blade-web', 'spa-stateful', 'api-bearer'])],
            'state_changing_method' => ['required', 'string', Rule::in(['post', 'put', 'patch', 'delete', 'get'])],
            'has_csrf_token' => ['required', 'boolean'],
            'same_site' => ['required', 'string', Rule::in(['lax', 'strict', 'none', 'missing'])],
            'uses_cookie_auth' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array{flow_name: string, client_type: string, state_changing_method: string, has_csrf_token: bool, same_site: string, uses_cookie_auth: bool}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'flow_name' => trim((string) $validated['flow_name']),
            'client_type' => (string) $validated['client_type'],
            'state_changing_method' => (string) $validated['state_changing_method'],
            'has_csrf_token' => (bool) $validated['has_csrf_token'],
            'same_site' => (string) $validated['same_site'],
            'uses_cookie_auth' => (bool) $validated['uses_cookie_auth'],
        ];
    }
}

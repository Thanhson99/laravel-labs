<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class PlanAuthorizationPolicyRequest extends FormRequest
{
    /**
     * Allow public authorization policy planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for authorization policy planning input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'model_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'ability' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'actor_role' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'rule' => ['required', 'string', 'min:8', 'max:160'],
        ];
    }

    /**
     * Return normalized authorization policy planning input.
     *
     * @return array{model_name: string, ability: string, actor_role: string, rule: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'model_name' => trim((string) $validated['model_name']),
            'ability' => trim((string) $validated['ability']),
            'actor_role' => trim((string) $validated['actor_role']),
            'rule' => trim((string) $validated['rule']),
        ];
    }
}

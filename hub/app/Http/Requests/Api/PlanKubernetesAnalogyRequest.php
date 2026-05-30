<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanKubernetesAnalogyRequest extends FormRequest
{
    /**
     * Allow public Kubernetes analogy planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for Kubernetes analogy input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'learning_goal' => ['required', 'string', Rule::in(['one-minute', 'interview', 'deployment-debug'])],
            'app_type' => ['required', 'string', Rule::in(['web-api', 'worker', 'scheduled-job'])],
            'replicas' => ['required', 'integer', 'min:1', 'max:20'],
            'needs_external_access' => ['required', 'boolean'],
            'has_stateful_data' => ['required', 'boolean'],
        ];
    }

    /**
     * Return normalized Kubernetes analogy input.
     *
     * @return array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'learning_goal' => (string) $validated['learning_goal'],
            'app_type' => (string) $validated['app_type'],
            'replicas' => (int) $validated['replicas'],
            'needs_external_access' => $this->boolean('needs_external_access'),
            'has_stateful_data' => $this->boolean('has_stateful_data'),
        ];
    }
}

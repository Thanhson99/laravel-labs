<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanSiemElkRequest extends FormRequest
{
    /**
     * Allow public SIEM and ELK planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for SIEM and ELK planning input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'environment_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'log_sources' => ['required', 'string', Rule::in(['laravel-app', 'cloud-auth', 'linux-nginx', 'kubernetes', 'mixed-enterprise'])],
            'detection_goal' => ['required', 'string', Rule::in(['auth-abuse', 'web-attack', 'privilege-change', 'incident-investigation', 'compliance'])],
            'retention_need' => ['required', 'string', Rule::in(['short', 'medium', 'long'])],
            'alert_maturity' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'data_sensitivity' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'team_size' => ['required', 'string', Rule::in(['solo', 'small', 'soc'])],
        ];
    }

    /**
     * Return normalized SIEM and ELK planning input.
     *
     * @return array{environment_name: string, log_sources: string, detection_goal: string, retention_need: string, alert_maturity: string, data_sensitivity: string, team_size: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'environment_name' => trim((string) $validated['environment_name']),
            'log_sources' => (string) $validated['log_sources'],
            'detection_goal' => (string) $validated['detection_goal'],
            'retention_need' => (string) $validated['retention_need'],
            'alert_maturity' => (string) $validated['alert_maturity'],
            'data_sensitivity' => (string) $validated['data_sensitivity'],
            'team_size' => (string) $validated['team_size'],
        ];
    }
}

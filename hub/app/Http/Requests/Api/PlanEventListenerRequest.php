<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class PlanEventListenerRequest extends FormRequest
{
    /**
     * Allow public event/listener planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for event/listener planning input.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'listener_name' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'side_effect' => ['required', 'string', 'min:3', 'max:120'],
            'queued' => ['required', 'boolean'],
        ];
    }

    /**
     * Return normalized event/listener planning input.
     *
     * @return array{event_name: string, listener_name: string, side_effect: string, queued: bool}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'event_name' => trim((string) $validated['event_name']),
            'listener_name' => trim((string) $validated['listener_name']),
            'side_effect' => trim((string) $validated['side_effect']),
            'queued' => (bool) $validated['queued'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePracticeTopicRequest extends FormRequest
{
    /**
     * Allow public practice API requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for a practice topic submission.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'track' => [
                'required',
                'string',
                Rule::in([
                    'php-foundation',
                    'laravel-http',
                    'api-validation',
                    'testing-quality',
                    'docker-runtime',
                ]),
            ],
            'difficulty' => ['sometimes', 'string', Rule::in(['beginner', 'intermediate'])],
        ];
    }

    /**
     * Return normalized topic data for the controller.
     *
     * @return array{title: string, track: string, difficulty: string}
     */
    public function topicData(): array
    {
        $validated = $this->validated();

        return [
            'title' => trim((string) $validated['title']),
            'track' => (string) $validated['track'],
            'difficulty' => (string) ($validated['difficulty'] ?? 'beginner'),
        ];
    }
}

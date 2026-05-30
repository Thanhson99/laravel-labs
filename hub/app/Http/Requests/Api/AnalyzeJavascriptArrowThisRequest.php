<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AnalyzeJavascriptArrowThisRequest extends FormRequest
{
    /**
     * Allow public workbench analysis requests.
     */
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
            'snippet' => ['required', 'string', 'min:10', 'max:2000'],
            'scenario' => ['required', 'string', Rule::in(['object-method', 'callback', 'class-method', 'mixed'])],
            'interview_level' => ['required', 'string', Rule::in(['junior', 'middle', 'senior'])],
        ];
    }

    /**
     * @return array{snippet: string, scenario: string, interview_level: string}
     */
    public function analysisData(): array
    {
        $validated = $this->validated();

        return [
            'snippet' => trim((string) $validated['snippet']),
            'scenario' => (string) $validated['scenario'],
            'interview_level' => (string) $validated['interview_level'],
        ];
    }
}

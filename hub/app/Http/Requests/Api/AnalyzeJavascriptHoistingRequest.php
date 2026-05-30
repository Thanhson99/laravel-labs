<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AnalyzeJavascriptHoistingRequest extends FormRequest
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
            'focus' => ['required', 'string', Rule::in(['mixed', 'var-let-const', 'function-declaration', 'function-expression', 'temporal-dead-zone'])],
            'interview_level' => ['required', 'string', Rule::in(['junior', 'middle', 'senior'])],
        ];
    }

    /**
     * @return array{snippet: string, focus: string, interview_level: string}
     */
    public function analysisData(): array
    {
        $validated = $this->validated();

        return [
            'snippet' => trim((string) $validated['snippet']),
            'focus' => (string) $validated['focus'],
            'interview_level' => (string) $validated['interview_level'],
        ];
    }
}

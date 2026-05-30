<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PlanRagStrategyRequest extends FormRequest
{
    /**
     * Allow public RAG strategy planning requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for RAG strategy input.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'knowledge_shape' => ['required', 'string', Rule::in(['documents', 'entities', 'workflows'])],
            'relationship_need' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'tool_use' => ['required', 'string', Rule::in(['none', 'retrieval-tools', 'multi-step-agent'])],
            'freshness' => ['required', 'string', Rule::in(['static', 'periodic', 'real-time'])],
            'risk_level' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'answer_style' => ['required', 'string', Rule::in(['summary', 'citations', 'actions'])],
            'context_strategy' => ['sometimes', 'string', Rule::in(['auto', 'rag', 'long-context', 'cag', 'hybrid'])],
        ];
    }

    /**
     * Return normalized RAG strategy input.
     *
     * @return array{knowledge_shape: string, relationship_need: string, tool_use: string, freshness: string, risk_level: string, answer_style: string, context_strategy: string}
     */
    public function planData(): array
    {
        $validated = $this->validated();

        return [
            'knowledge_shape' => (string) $validated['knowledge_shape'],
            'relationship_need' => (string) $validated['relationship_need'],
            'tool_use' => (string) $validated['tool_use'],
            'freshness' => (string) $validated['freshness'],
            'risk_level' => (string) $validated['risk_level'],
            'answer_style' => (string) $validated['answer_style'],
            'context_strategy' => (string) ($validated['context_strategy'] ?? 'auto'),
        ];
    }
}

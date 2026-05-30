<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class JavascriptArrowThisLabService
{
    /**
     * Analyze JavaScript arrow-function `this` behavior for interview practice.
     *
     * @param  array{snippet: string, scenario: string, interview_level: string}  $input
     * @return array<string, mixed>
     */
    public function analyze(array $input): array
    {
        $snippet = trim($input['snippet']);
        $signals = $this->signalsFor($snippet);

        return [
            'topic' => 'javascript-arrow-this',
            'scenario' => $input['scenario'],
            'interview_level' => $input['interview_level'],
            'detected_signals' => $signals,
            'plain_explanation' => $this->plainExplanationFor($signals),
            'comparison_trace' => $this->comparisonTraceFor($signals),
            'trap_checklist' => $this->trapChecklistFor($signals),
            'safe_rewrite' => $this->safeRewriteFor($signals),
            'interview_answer' => $this->interviewAnswerFor($input['interview_level']),
            'knowledge_check' => $this->knowledgeCheck(),
            'reviewer_notes' => $this->reviewerNotesFor($signals),
            'practice_tasks' => [
                'Predict what `obj.normal()` returns and why.',
                'Predict what `obj.arrow()` returns and which scope supplied `this`.',
                'Explain why call/apply/bind cannot change arrow-function `this`.',
                'Name one callback case where an arrow is useful and one object method case where it is risky.',
            ],
            'commands' => [
                'php artisan route:list --path=javascript-arrow-this-lab',
                'php artisan test --filter JavascriptArrowThisLabWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * @return array<string, bool>
     */
    private function signalsFor(string $snippet): array
    {
        $lower = strtolower($snippet);

        return [
            'has_arrow_function' => str_contains($snippet, '=>'),
            'has_normal_method' => preg_match('/\w+\s*\([^)]*\)\s*\{/m', $snippet) === 1,
            'has_object_arrow_property' => preg_match('/\w+\s*:\s*\([^)]*\)\s*=>|\w+\s*:\s*[^,\n]+=>/m', $snippet) === 1,
            'has_call_apply_bind' => str_contains($lower, '.call(') || str_contains($lower, '.apply(') || str_contains($lower, '.bind('),
            'has_callback_use' => str_contains($lower, 'settimeout') || str_contains($lower, 'setinterval') || str_contains($lower, 'addeventlistener') || str_contains($lower, '.map(') || str_contains($lower, '.foreach('),
            'has_class_or_constructor' => str_contains($lower, 'class ') || str_contains($lower, 'constructor(') || str_contains($lower, 'function '),
        ];
    }

    /**
     * @param  array<string, bool>  $signals
     */
    private function plainExplanationFor(array $signals): string
    {
        if ($signals['has_object_arrow_property']) {
            return 'An arrow function used as an object property does not receive the object as `this`. It keeps lexical `this` from the surrounding scope where the object was created.';
        }

        if ($signals['has_callback_use']) {
            return 'An arrow callback is useful when the callback should keep the outer `this`, such as timer, event, array, or class callback code.';
        }

        return 'Arrow functions do not create their own `this`; normal functions receive `this` from how they are called.';
    }

    /**
     * @param  array<string, bool>  $signals
     * @return array<int, array{case: string, expected_this: string, explanation: string}>
     */
    private function comparisonTraceFor(array $signals): array
    {
        $trace = [
            [
                'case' => 'normal object method',
                'expected_this' => 'call-site object',
                'explanation' => '`obj.normal()` sets `this` from the call site, so `this` is usually `obj`.',
            ],
            [
                'case' => 'arrow object property',
                'expected_this' => 'lexical outer scope',
                'explanation' => '`obj.arrow()` does not rebind `this`; the arrow already captured outer `this`.',
            ],
        ];

        if ($signals['has_call_apply_bind']) {
            $trace[] = [
                'case' => 'call/apply/bind on arrow',
                'expected_this' => 'unchanged lexical this',
                'explanation' => '`call`, `apply`, and `bind` can pass arguments, but they cannot replace arrow-function `this`.',
            ];
        }

        return $trace;
    }

    /**
     * @param  array<string, bool>  $signals
     * @return array<int, string>
     */
    private function trapChecklistFor(array $signals): array
    {
        $items = [
            'Do not use an arrow property when the function must behave like an object method.',
            'Do not claim `obj.arrow()` makes `this` equal to `obj`.',
            'Do not use call/apply/bind as a fix for arrow-function `this`.',
        ];

        if ($signals['has_callback_use']) {
            $items[] = 'Use arrows deliberately for callbacks that should keep outer `this`.';
        }

        return $items;
    }

    /**
     * @param  array<string, bool>  $signals
     */
    private function safeRewriteFor(array $signals): string
    {
        if ($signals['has_object_arrow_property']) {
            return <<<'JS'
const user = {
  name: 'Son',
  normal() {
    return this.name;
  },
};
JS;
        }

        return <<<'JS'
class Timer {
  seconds = 0;

  start() {
    setInterval(() => {
      this.seconds += 1;
    }, 1000);
  }
}
JS;
    }

    /**
     * @return array<int, array{prompt: string, answer: string}>
     */
    private function knowledgeCheck(): array
    {
        return [
            [
                'prompt' => 'Does an arrow function create its own `this`?',
                'answer' => 'No. It reads `this` lexically from the scope where it was created.',
            ],
            [
                'prompt' => 'Does `obj.arrow()` make `this` equal to `obj`?',
                'answer' => 'No. The object call does not rebind arrow-function `this`.',
            ],
            [
                'prompt' => 'Can call/apply/bind replace arrow-function `this`?',
                'answer' => 'No. They cannot rebind arrow-function `this`.',
            ],
        ];
    }

    private function interviewAnswerFor(string $level): string
    {
        $base = 'Arrow functions do not have their own `this`; they capture lexical `this` from the surrounding scope. Normal functions resolve `this` from the call site, such as `obj.method()`, `call`, `apply`, `bind`, or constructor calls.';

        if ($level === 'senior') {
            return $base.' In production code I avoid arrow properties for object methods that need dynamic `this`, but I use arrows for callbacks when preserving the outer instance is intentional.';
        }

        return $base.' The common trap is thinking `obj.arrow()` binds `this` to `obj`; it does not.';
    }

    /**
     * @param  array<string, bool>  $signals
     * @return array{summary: string, blocking_comments: array<int, string>, follow_up_questions: array<int, string>}
     */
    private function reviewerNotesFor(array $signals): array
    {
        $blocking = [];

        if ($signals['has_object_arrow_property']) {
            $blocking[] = 'Replace arrow object methods with normal method syntax when the method needs object state from `this`.';
        }

        if ($signals['has_call_apply_bind']) {
            $blocking[] = 'Remove any claim that call/apply/bind can repair arrow-function `this`.';
        }

        return [
            'summary' => 'Review every arrow function by asking whether lexical `this` is intentional.',
            'blocking_comments' => $blocking,
            'follow_up_questions' => [
                'Which scope creates this arrow function?',
                'Should this function use call-site `this` or lexical `this`?',
                'Would a normal method make the intent clearer?',
            ],
        ];
    }
}

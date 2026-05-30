<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class JavascriptHoistingLabService
{
    /**
     * Analyze a JavaScript snippet for hoisting interview practice.
     *
     * @param  array{snippet: string, focus: string, interview_level: string}  $input
     * @return array<string, mixed>
     */
    public function analyze(array $input): array
    {
        $snippet = trim($input['snippet']);
        $signals = $this->signalsFor($snippet);
        $riskScore = $this->riskScoreFor($signals);

        return [
            'topic' => 'javascript-hoisting',
            'focus' => $input['focus'],
            'interview_level' => $input['interview_level'],
            'detected_signals' => $signals,
            'risk_score' => $riskScore,
            'plain_explanation' => $this->plainExplanationFor($signals),
            'execution_trace' => $this->executionTraceFor($signals),
            'prediction_quiz' => $this->predictionQuizFor($signals),
            'knowledge_check' => $this->knowledgeCheckFor($signals),
            'debug_checklist' => $this->debugChecklistFor($signals),
            'bug_report_template' => $this->bugReportTemplateFor($signals, $riskScore),
            'flashcards' => $this->flashcardsFor($signals),
            'fixed_example' => $this->fixedExampleFor($snippet),
            'study_memo_markdown' => $this->studyMemoFor($signals, $riskScore, $input),
            'interview_rubric' => $this->interviewRubricFor($signals),
            'interview_drill_plan' => $this->interviewDrillPlanFor($signals, $input['interview_level']),
            'anti_patterns' => $this->antiPatternsFor($signals),
            'test_cases' => $this->testCasesFor($signals),
            'runtime_examples' => $this->runtimeExamplesFor($signals),
            'module_notes' => $this->moduleNotesFor($signals),
            'refactor_plan' => $this->refactorPlanFor($signals),
            'team_review_prompts' => $this->teamReviewPromptsFor($signals),
            'reviewer_notes' => $this->reviewerNotesFor($signals, $riskScore),
            'eslint_suggestions' => $this->eslintSuggestionsFor($signals),
            'commit_plan' => $this->commitPlanFor($signals),
            'interview_answer' => $this->interviewAnswerFor($signals, $input['interview_level']),
            'project_guidelines' => [
                'Declare variables before use even when the language would technically allow earlier references.',
                'Prefer `const` first and `let` when reassignment is needed; avoid `var` in new project code.',
                'Use function declarations for named reusable helpers and function expressions only after assignment.',
                'Keep hoisting examples in tests or notes so reviewers can separate language behavior from project style.',
            ],
            'practice_checks' => [
                'Can you explain why a function declaration works before its source line?',
                'Can you predict `undefined` for early `var` reads without calling it a ReferenceError?',
                'Can you explain temporal dead zone for `let` and `const`?',
                'Can you explain why a `var` function expression is not callable before assignment?',
            ],
            'commands' => [
                'php artisan route:list --path=javascript-hoisting-lab',
                'php artisan test --filter JavascriptHoistingLabWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return short self-check questions for hoisting study.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{prompt: string, options: array<int, string>, answer: string, explanation: string, tag: string}>
     */
    private function knowledgeCheckFor(array $signals): array
    {
        $questions = [
            [
                'prompt' => 'Which statement best defines hoisting?',
                'options' => [
                    'JavaScript physically moves all code to the top of the file.',
                    'JavaScript registers declarations before executing statements.',
                    'JavaScript runs assignments before imports.',
                ],
                'answer' => 'JavaScript registers declarations before executing statements.',
                'explanation' => 'Hoisting is about declaration setup, not source-code movement.',
                'tag' => 'definition',
            ],
        ];

        if ($signals['has_function_declaration']) {
            $questions[] = [
                'prompt' => 'Why can a function declaration run before its source line?',
                'options' => [
                    'The function object is initialized during the creation phase.',
                    'The browser rewrites the file before execution.',
                    'Function calls always run after variable assignments.',
                ],
                'answer' => 'The function object is initialized during the creation phase.',
                'explanation' => 'A declaration creates a callable binding before statement execution begins.',
                'tag' => 'function-declaration',
            ];
        }

        if ($signals['has_var']) {
            $questions[] = [
                'prompt' => 'What does an early read of a `var` binding usually return?',
                'options' => [
                    'ReferenceError',
                    'undefined',
                    'The assigned value',
                ],
                'answer' => 'undefined',
                'explanation' => '`var` is hoisted and initialized as undefined before its assignment statement runs.',
                'tag' => 'var',
            ];
        }

        if ($signals['has_let_or_const']) {
            $questions[] = [
                'prompt' => 'What happens when code reads `let` or `const` before initialization?',
                'options' => [
                    'It returns undefined.',
                    'It throws ReferenceError.',
                    'It silently skips the read.',
                ],
                'answer' => 'It throws ReferenceError.',
                'explanation' => 'The binding exists but remains unavailable inside the temporal dead zone.',
                'tag' => 'tdz',
            ];
        }

        if ($signals['has_function_expression']) {
            $questions[] = [
                'prompt' => 'Why can calling a `var` function expression early fail?',
                'options' => [
                    'Only the variable exists early; the function value is assigned later.',
                    'Function expressions are never callable.',
                    'The call is converted to a function declaration.',
                ],
                'answer' => 'Only the variable exists early; the function value is assigned later.',
                'explanation' => 'The early value is undefined, so calling it can produce TypeError.',
                'tag' => 'function-expression',
            ];
        }

        return $questions;
    }

    /**
     * Return practical code-review notes for hoisting-prone JavaScript.
     *
     * @param  array<string, bool>  $signals
     * @return array{decision: string, priority: string, summary_comment: string, blocking_comments: array<int, string>, author_questions: array<int, string>, merge_requirements: array<int, string>}
     */
    private function reviewerNotesFor(array $signals, int $riskScore): array
    {
        $blockingComments = [
            'Please reorder declarations so the file can be read top to bottom without relying on hoisting behavior.',
        ];

        if ($signals['has_var']) {
            $blockingComments[] = 'Please replace `var` with `const` or `let` unless this is intentionally legacy-compatible code.';
        }

        if ($signals['has_function_expression']) {
            $blockingComments[] = 'Please move the function expression assignment before any possible call site or convert it to a named declaration.';
        }

        $authorQuestions = [
            'Which statement executes first at runtime, and what value does it read?',
            'Is any import, callback, or test setup able to read this binding before initialization?',
        ];

        if ($signals['has_let_or_const']) {
            $authorQuestions[] = 'Can this path throw a temporal dead zone ReferenceError during module startup?';
        }

        return [
            'decision' => $riskScore >= 80 ? 'request changes' : 'comment',
            'priority' => $riskScore >= 80 ? 'p1' : 'p2',
            'summary_comment' => 'The code is valid JavaScript in parts, but the ordering makes behavior hard to predict and risky to maintain.',
            'blocking_comments' => $blockingComments,
            'author_questions' => $authorQuestions,
            'merge_requirements' => [
                'Declarations appear before their first read or call site.',
                'Tests cover the previous risky ordering and the safer rewrite.',
                'Lint suggestions are captured for follow-up if the project does not enforce them yet.',
            ],
        ];
    }

    /**
     * Return a structured bug report learners can use for PR or issue writing.
     *
     * @param  array<string, bool>  $signals
     * @return array{title: string, severity: string, summary: string, suspected_cause: string, user_impact: string, reproduction_steps: array<int, string>, proposed_fix: array<int, string>, verification: array<int, string>}
     */
    private function bugReportTemplateFor(array $signals, int $riskScore): array
    {
        $reproductionSteps = [
            'Run the snippet in a clean JavaScript runtime.',
            'Predict each output before executing the next statement.',
            'Compare the actual output with the expected creation, initialization, and execution phases.',
        ];

        if ($signals['has_function_expression']) {
            $reproductionSteps[] = 'Call the function expression before its assignment to confirm the callable value is not ready.';
        }

        $proposedFix = [
            'Move declarations and assignments before the first read or call site.',
            'Replace `var` with `const` or `let` unless a legacy compatibility reason is documented.',
            'Keep the final code readable from top to bottom instead of relying on hoisting knowledge.',
        ];

        if ($signals['has_let_or_const']) {
            $proposedFix[] = 'Move `let` and `const` initialization before any branch, callback, or module side effect can read the binding.';
        }

        return [
            'title' => 'Hoisting-prone JavaScript ordering can produce confusing runtime behavior',
            'severity' => $riskScore >= 80 ? 'high' : 'medium',
            'summary' => 'The snippet relies on declaration setup behavior, so readers may mispredict output or miss a runtime failure during review.',
            'suspected_cause' => $this->bugCauseFor($signals),
            'user_impact' => 'A startup path, event handler, or test setup can fail before the intended value has been assigned.',
            'reproduction_steps' => $reproductionSteps,
            'proposed_fix' => $proposedFix,
            'verification' => [
                'Add or update an output-prediction test for the risky ordering.',
                'Run the focused JavaScript test file after the rewrite.',
                'Confirm lint rules catch future use-before-define and `var` usage where applicable.',
            ],
        ];
    }

    /**
     * Summarize the likely cause of a hoisting-prone bug.
     *
     * @param  array<string, bool>  $signals
     */
    private function bugCauseFor(array $signals): string
    {
        if ($signals['has_function_expression']) {
            return 'A function expression is assigned after a possible call site, so the variable can exist before the callable value exists.';
        }

        if ($signals['has_let_or_const']) {
            return '`let` or `const` is read before initialization, which triggers temporal dead zone behavior.';
        }

        if ($signals['has_var']) {
            return '`var` is read before assignment, so JavaScript returns undefined instead of failing loudly.';
        }

        return 'The snippet is easier to misunderstand because declaration setup and statement execution are not separated clearly.';
    }

    /**
     * Return a short interview drill sequence for hoisting practice.
     *
     * @param  array<string, bool>  $signals
     * @return array{timebox: string, rounds: array<int, array{round: string, goal: string, exercise: string, pass_condition: string}>, follow_up: array<int, string>}
     */
    private function interviewDrillPlanFor(array $signals, string $level): array
    {
        $rounds = [
            [
                'round' => 'round 1: prediction',
                'goal' => 'Predict the output before reading the explanation.',
                'exercise' => 'Walk through creation, initialization, and execution phases out loud.',
                'pass_condition' => 'Answer separates function declarations, `var`, and TDZ behavior without guessing.',
            ],
            [
                'round' => 'round 2: rewrite',
                'goal' => 'Turn the snippet into project-style code.',
                'exercise' => 'Move declarations before use, replace `var`, and make callable values available before call sites.',
                'pass_condition' => 'The safer rewrite is readable top to bottom and preserves the intended behavior.',
            ],
            [
                'round' => 'round 3: interview answer',
                'goal' => 'Give a concise answer suitable for the selected level.',
                'exercise' => 'Explain the rule in 45 seconds, then connect it to one project guideline.',
                'pass_condition' => 'The answer avoids saying JavaScript physically moves code to the top.',
            ],
        ];

        if ($level === 'senior') {
            $rounds[] = [
                'round' => 'round 4: system context',
                'goal' => 'Connect hoisting to module loading and code review.',
                'exercise' => 'Name one ES module or circular-import scenario where TDZ can appear during startup.',
                'pass_condition' => 'The answer links language behavior to maintainable ordering and reviewable PRs.',
            ];
        }

        $followUp = [
            'Record one wrong prediction and rewrite it as a flashcard.',
            'Add one lint rule that would have made the risky pattern visible earlier.',
        ];

        if ($signals['has_function_expression']) {
            $followUp[] = 'Practice converting a function expression to a named declaration and explain the tradeoff.';
        }

        if ($signals['has_let_or_const']) {
            $followUp[] = 'Practice explaining temporal dead zone without calling it the same as `undefined`.';
        }

        return [
            'timebox' => $level === 'senior' ? '20 minutes' : '15 minutes',
            'rounds' => $rounds,
            'follow_up' => $followUp,
        ];
    }

    /**
     * Return a small PR plan for fixing hoisting-prone code.
     *
     * @param  array<string, bool>  $signals
     * @return array{branch: string, changed_files: array<int, string>, commits: array<int, array{message: string, verification: string}>, pull_request_checklist: array<int, string>}
     */
    private function commitPlanFor(array $signals): array
    {
        $changedFiles = [
            'resources/js/hoisting-trace.js',
            'resources/js/hoisting-interview-checklist.js',
        ];

        if ($signals['has_var'] || $signals['has_function_expression'] || $signals['has_let_or_const']) {
            $changedFiles[] = 'tests/js/hoisting-behavior.test.js';
        }

        return [
            'branch' => 'practice/javascript-hoisting-lab',
            'changed_files' => array_values(array_unique($changedFiles)),
            'commits' => [
                [
                    'message' => 'Add JavaScript hoisting trace examples',
                    'verification' => 'Snippet explains function declarations, var, TDZ, and function expressions.',
                ],
                [
                    'message' => 'Add hoisting interview checklist and tests',
                    'verification' => 'Prediction quiz, test cases, and lint suggestions cover the risky behavior.',
                ],
            ],
            'pull_request_checklist' => [
                'Hoisting explanation does not claim code is physically moved.',
                'Examples cover function declarations, var, let/const TDZ, and function expressions.',
                'Refactor plan removes unclear ordering before adding comments.',
                'ESLint suggestions are documented for project follow-up.',
            ],
        ];
    }

    /**
     * Return lint-rule suggestions that prevent hoisting-prone code.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{rule: string, level: string, reason: string}>
     */
    private function eslintSuggestionsFor(array $signals): array
    {
        $rules = [
            [
                'rule' => 'no-use-before-define',
                'level' => 'error',
                'reason' => 'Prevents most confusing reads and calls before declarations.',
            ],
            [
                'rule' => 'prefer-const',
                'level' => 'warn',
                'reason' => 'Encourages stable bindings and reduces accidental reassignment.',
            ],
        ];

        if ($signals['has_var']) {
            $rules[] = [
                'rule' => 'no-var',
                'level' => 'error',
                'reason' => 'Blocks function-scoped `var` and pushes code toward `let` or `const`.',
            ];
        }

        if ($signals['has_function_expression']) {
            $rules[] = [
                'rule' => 'func-style',
                'level' => 'warn',
                'reason' => 'Can be configured to prefer declarations for stable helpers or expressions for deliberate local values.',
            ];
        }

        return $rules;
    }

    /**
     * Return PR review prompts for hoisting-prone JavaScript.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{prompt: string, why: string}>
     */
    private function teamReviewPromptsFor(array $signals): array
    {
        $prompts = [
            [
                'prompt' => 'Can a reader predict this file top to bottom without knowing hoisting edge cases?',
                'why' => 'Readable ordering is usually better than relying on language setup behavior.',
            ],
            [
                'prompt' => 'Are declarations close to their first use?',
                'why' => 'Distance between setup and use makes early reads, stale assumptions, and refactor bugs more likely.',
            ],
        ];

        if ($signals['has_var']) {
            $prompts[] = [
                'prompt' => 'Why is `var` needed instead of `const` or `let`?',
                'why' => '`var` can hide early-read bugs by returning undefined instead of failing loudly.',
            ];
        }

        if ($signals['has_function_expression']) {
            $prompts[] = [
                'prompt' => 'Can this function expression be moved above the call site or converted to a named function declaration?',
                'why' => 'A callable value assigned later can fail when control flow reaches the call before assignment.',
            ];
        }

        if ($signals['has_let_or_const']) {
            $prompts[] = [
                'prompt' => 'Can any branch, callback, import cycle, or test setup read this binding before initialization?',
                'why' => 'TDZ errors often appear during startup or less common execution paths.',
            ];
        }

        return $prompts;
    }

    /**
     * Return a safe refactor sequence for hoisting-prone JavaScript.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{step: string, change: string, verification: string}>
     */
    private function refactorPlanFor(array $signals): array
    {
        $steps = [
            [
                'step' => 'map reads before writes',
                'change' => 'List every variable or function used before its declaration or assignment.',
                'verification' => 'Each early read has an intentional reason or a planned rewrite.',
            ],
        ];

        if ($signals['has_var']) {
            $steps[] = [
                'step' => 'replace var',
                'change' => 'Convert `var` to `const` unless reassignment is required, then use `let`.',
                'verification' => 'Tests still pass and early reads now fail clearly instead of returning undefined.',
            ];
        }

        if ($signals['has_function_expression']) {
            $steps[] = [
                'step' => 'stabilize callable helpers',
                'change' => 'Move function-expression assignments above call sites or convert stable helpers to named function declarations.',
                'verification' => 'No call site can execute while the callable value is still undefined.',
            ];
        }

        if ($signals['has_let_or_const']) {
            $steps[] = [
                'step' => 'remove tdz hazards',
                'change' => 'Move `let` and `const` declarations before branches, callbacks, or module top-level code that reads them.',
                'verification' => 'No ReferenceError appears during module load, test setup, or first render.',
            ];
        }

        $steps[] = [
            'step' => 'document the rule',
            'change' => 'Add a short comment only if ordering remains non-obvious after refactor.',
            'verification' => 'A reviewer can predict output without relying on hidden hoisting behavior.',
        ];

        return $steps;
    }

    /**
     * Return notes that connect hoisting rules to modern JavaScript modules.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{topic: string, note: string, project_check: string}>
     */
    private function moduleNotesFor(array $signals): array
    {
        $notes = [
            [
                'topic' => 'module evaluation order',
                'note' => 'ES modules still have declaration setup, but imports are resolved and modules are evaluated through the dependency graph before your importing module continues.',
                'project_check' => 'When a value is undefined during startup, inspect import cycles and module side effects, not only local hoisting.',
            ],
            [
                'topic' => 'top-level side effects',
                'note' => 'Top-level code runs when the module is evaluated, so early reads can happen before an imported module has finished its own setup in circular dependencies.',
                'project_check' => 'Move side effects into explicit functions or initialization steps when module order becomes hard to reason about.',
            ],
        ];

        if ($signals['has_function_declaration']) {
            $notes[] = [
                'topic' => 'exported function declarations',
                'note' => 'Named exported function declarations are stable helpers, but relying on call-before-definition still makes code harder to scan.',
                'project_check' => 'Keep exported helpers near the top or group exports clearly so reviewers do not need hoisting knowledge to read the file.',
            ];
        }

        if ($signals['has_let_or_const']) {
            $notes[] = [
                'topic' => 'imported live bindings',
                'note' => 'Module imports are live bindings, but local `let` and `const` still obey temporal dead zone rules inside the module.',
                'project_check' => 'If a module throws during initialization, check TDZ reads and circular imports before blaming the bundler.',
            ];
        }

        return $notes;
    }

    /**
     * Return small runnable examples that isolate one hoisting behavior at a time.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{label: string, code: string, expected: string}>
     */
    private function runtimeExamplesFor(array $signals): array
    {
        $examples = [
            [
                'label' => 'function declaration',
                'code' => "sayHi();\n\nfunction sayHi() {\n  console.log('hi');\n}",
                'expected' => 'Logs `hi` because function declarations are initialized before execution.',
            ],
        ];

        if ($signals['has_var']) {
            $examples[] = [
                'label' => 'var early read',
                'code' => "console.log(userName);\nvar userName = 'Son';",
                'expected' => 'Logs `undefined` because `var` is initialized before assignment.',
            ];
        }

        if ($signals['has_let_or_const']) {
            $examples[] = [
                'label' => 'temporal dead zone',
                'code' => "console.log(score);\nlet score = 10;",
                'expected' => 'Throws `ReferenceError` because `score` is read inside the temporal dead zone.',
            ];
        }

        if ($signals['has_function_expression']) {
            $examples[] = [
                'label' => 'function expression',
                'code' => "run();\nvar run = function () {\n  console.log('run');\n};",
                'expected' => 'Throws `TypeError` because `run` is `undefined` before assignment.',
            ];
        }

        return $examples;
    }

    /**
     * Return concrete assertions for testing or live-coding practice.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{name: string, assertion: string, expected: string}>
     */
    private function testCasesFor(array $signals): array
    {
        $cases = [
            [
                'name' => 'definition',
                'assertion' => 'Answer says declarations are registered before execution.',
                'expected' => 'setup phase, creation phase, or declaration registration',
            ],
        ];

        if ($signals['has_function_declaration']) {
            $cases[] = [
                'name' => 'function declaration call',
                'assertion' => 'Calling a function declaration before its source line succeeds.',
                'expected' => 'callable function',
            ];
        }

        if ($signals['has_var']) {
            $cases[] = [
                'name' => 'early var read',
                'assertion' => 'Reading `var userName` before assignment returns undefined.',
                'expected' => 'undefined',
            ];
        }

        if ($signals['has_let_or_const']) {
            $cases[] = [
                'name' => 'tdz read',
                'assertion' => 'Reading `let` or `const` before declaration throws.',
                'expected' => 'ReferenceError',
            ];
        }

        if ($signals['has_function_expression']) {
            $cases[] = [
                'name' => 'function expression early call',
                'assertion' => 'Calling a `var` function expression before assignment throws.',
                'expected' => 'TypeError',
            ];
        }

        return $cases;
    }

    /**
     * Return common mistakes to avoid when explaining or using hoisting.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{mistake: string, correction: string}>
     */
    private function antiPatternsFor(array $signals): array
    {
        $items = [
            [
                'mistake' => 'Saying JavaScript physically moves code to the top of the file.',
                'correction' => 'Say declarations are registered during the creation phase before execution.',
            ],
            [
                'mistake' => 'Treating `var`, `let`, and `const` as if they hoist the same way.',
                'correction' => '`var` starts as undefined; `let` and `const` are blocked by the temporal dead zone.',
            ],
        ];

        if ($signals['has_function_expression']) {
            $items[] = [
                'mistake' => 'Calling a function expression before assignment because it looks like a function declaration.',
                'correction' => 'Only function declarations are initialized early; function expressions become callable after assignment.',
            ];
        }

        if ($signals['has_var']) {
            $items[] = [
                'mistake' => 'Keeping `var` in new project code because early reads do not throw.',
                'correction' => 'Use `const` or `let` and let TDZ reveal ordering mistakes earlier.',
            ];
        }

        return $items;
    }

    /**
     * Return a scoring rubric for hoisting interview answers.
     *
     * @param  array<string, bool>  $signals
     * @return array{max_score: int, passing_score: int, criteria: array<int, array{name: string, points: int, evidence: string}>}
     */
    private function interviewRubricFor(array $signals): array
    {
        $criteria = [
            [
                'name' => 'Defines hoisting without saying code is physically moved.',
                'points' => 20,
                'evidence' => 'Mentions declaration registration during the setup or creation phase.',
            ],
            [
                'name' => 'Separates function declarations from function expressions.',
                'points' => 20,
                'evidence' => $signals['has_function_declaration'] || $signals['has_function_expression']
                    ? 'Snippet contains function declaration or function expression behavior to explain.'
                    : 'Answer should still name the distinction even if the snippet is simpler.',
            ],
            [
                'name' => 'Explains `var` as initialized to undefined.',
                'points' => 20,
                'evidence' => $signals['has_var']
                    ? 'Snippet includes `var`, so the answer must predict early undefined reads.'
                    : 'Answer should avoid over-focusing on `var` if no `var` appears.',
            ],
            [
                'name' => 'Explains `let` and `const` temporal dead zone.',
                'points' => 20,
                'evidence' => $signals['has_let_or_const']
                    ? 'Snippet includes `let` or `const`, so TDZ must be explained.'
                    : 'Answer can mention TDZ as a contrast with `var`.',
            ],
            [
                'name' => 'Connects the rule to project practice.',
                'points' => 20,
                'evidence' => 'A strong answer recommends declaring before use and avoiding `var` in modern code.',
            ],
        ];

        return [
            'max_score' => collect($criteria)->sum('points'),
            'passing_score' => 80,
            'criteria' => $criteria,
        ];
    }

    /**
     * Build a markdown memo learners can save with their interview notes.
     *
     * @param  array<string, bool>  $signals
     */
    private function studyMemoFor(array $signals, int $riskScore, array $input): string
    {
        $signalLines = collect($signals)
            ->map(fn (bool $enabled, string $signal): string => '- '.$signal.': '.($enabled ? 'yes' : 'no'))
            ->implode("\n");

        return <<<MARKDOWN
# JavaScript Hoisting Study Memo

Focus: {$input['focus']}
Interview level: {$input['interview_level']}
Risk score: {$riskScore}/100

## Key Rule
Hoisting means JavaScript registers declarations before executing statements. Function declarations are initialized early, `var` starts as `undefined`, and `let`/`const` cannot be read before their declaration line because of the temporal dead zone.

## Detected Signals
{$signalLines}

## Project Habit
Declare names before use, prefer `const`/`let` over `var`, and use named function declarations only when early callable helpers are intentional.
MARKDOWN;
    }

    /**
     * Return compact flashcards for spaced review.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{front: string, back: string, tag: string}>
     */
    private function flashcardsFor(array $signals): array
    {
        $cards = [
            [
                'front' => 'What does hoisting mean in JavaScript?',
                'back' => 'JavaScript registers declarations during setup before executing statements.',
                'tag' => 'definition',
            ],
        ];

        if ($signals['has_function_declaration']) {
            $cards[] = [
                'front' => 'Why can a function declaration be called before its line?',
                'back' => 'The function object is initialized during the creation phase.',
                'tag' => 'function-declaration',
            ];
        }

        if ($signals['has_var']) {
            $cards[] = [
                'front' => 'What is the value of a `var` variable before assignment?',
                'back' => '`undefined`, because `var` is hoisted and initialized.',
                'tag' => 'var',
            ];
        }

        if ($signals['has_let_or_const']) {
            $cards[] = [
                'front' => 'What is the temporal dead zone?',
                'back' => 'The time between scope creation and the `let` or `const` declaration executing, where reading the binding throws ReferenceError.',
                'tag' => 'tdz',
            ];
        }

        if ($signals['has_function_expression']) {
            $cards[] = [
                'front' => 'Why is a function expression assigned to `var` not callable before assignment?',
                'back' => 'Only the variable is hoisted; the function value is assigned later during execution.',
                'tag' => 'function-expression',
            ];
        }

        return $cards;
    }

    /**
     * Return a project-review checklist for hoisting-related bugs.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{check: string, reason: string, action: string}>
     */
    private function debugChecklistFor(array $signals): array
    {
        $checks = [
            [
                'check' => 'Scan for reads before declarations.',
                'reason' => 'Hoisting bugs usually appear when a name is used before the line that makes the value clear.',
                'action' => 'Move declarations above first use or split setup code from execution code.',
            ],
        ];

        if ($signals['has_var']) {
            $checks[] = [
                'check' => 'Replace `var` with `const` or `let` where possible.',
                'reason' => '`var` starts as undefined and is function-scoped, which makes early reads and loop bugs harder to see.',
                'action' => 'Use `const` for stable values and `let` for reassignment; rerun tests after each replacement.',
            ];
        }

        if ($signals['has_let_or_const']) {
            $checks[] = [
                'check' => 'Confirm no `let` or `const` value is read inside the temporal dead zone.',
                'reason' => 'TDZ failures throw at runtime and often hide in conditional branches or module initialization order.',
                'action' => 'Move initialization earlier or delay the read until after the declaration executes.',
            ];
        }

        if ($signals['has_function_expression']) {
            $checks[] = [
                'check' => 'Check function expressions assigned to variables before call sites.',
                'reason' => 'The variable may exist before assignment, but the callable function value does not.',
                'action' => 'Use a named function declaration for helpers that must be callable before their definition.',
            ];
        }

        return $checks;
    }

    /**
     * Return short prediction prompts for interview-style output tracing.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{prompt: string, expected: string, explanation: string}>
     */
    private function predictionQuizFor(array $signals): array
    {
        $quiz = [];

        if ($signals['has_function_declaration']) {
            $quiz[] = [
                'prompt' => 'What happens when a function declaration is called before its source line?',
                'expected' => 'It runs successfully.',
                'explanation' => 'Function declarations are initialized during the creation phase before statements execute.',
            ];
        }

        if ($signals['has_var']) {
            $quiz[] = [
                'prompt' => 'What does an early read of a `var` variable produce before assignment?',
                'expected' => 'undefined',
                'explanation' => '`var` is hoisted and initialized as undefined, so the read does not throw.',
            ];
        }

        if ($signals['has_let_or_const']) {
            $quiz[] = [
                'prompt' => 'What happens if code reads `let` or `const` before its declaration line?',
                'expected' => 'ReferenceError',
                'explanation' => 'The binding exists but stays unavailable inside the temporal dead zone until declaration execution.',
            ];
        }

        if ($signals['has_function_expression']) {
            $quiz[] = [
                'prompt' => 'What happens when a `var` function expression is called before assignment?',
                'expected' => 'TypeError',
                'explanation' => 'The variable exists as undefined, but undefined is not callable until the function expression assignment runs.',
            ];
        }

        return $quiz;
    }

    /**
     * Detect hoisting signals in the submitted snippet.
     *
     * @return array{has_var: bool, has_let_or_const: bool, has_function_declaration: bool, has_function_expression: bool, has_early_call: bool, has_temporal_dead_zone_risk: bool}
     */
    private function signalsFor(string $snippet): array
    {
        $lower = Str::lower($snippet);

        return [
            'has_var' => preg_match('/\bvar\s+[a-z_$][a-z0-9_$]*/i', $snippet) === 1,
            'has_let_or_const' => preg_match('/\b(let|const)\s+[a-z_$][a-z0-9_$]*/i', $snippet) === 1,
            'has_function_declaration' => preg_match('/\bfunction\s+[a-z_$][a-z0-9_$]*\s*\(/i', $snippet) === 1,
            'has_function_expression' => preg_match('/\b(var|let|const)\s+[a-z_$][a-z0-9_$]*\s*=\s*function\b/i', $snippet) === 1,
            'has_early_call' => preg_match('/^[\s;]*[a-z_$][a-z0-9_$]*\s*\(/im', $snippet) === 1,
            'has_temporal_dead_zone_risk' => str_contains($lower, 'console.log(score)') || str_contains($lower, 'referenceerror') || str_contains($lower, 'tdz'),
        ];
    }

    /**
     * Score how risky the snippet is for misunderstood hoisting behavior.
     *
     * @param  array<string, bool>  $signals
     */
    private function riskScoreFor(array $signals): int
    {
        $score = 0;
        $score += $signals['has_var'] ? 20 : 0;
        $score += $signals['has_function_expression'] ? 25 : 0;
        $score += $signals['has_temporal_dead_zone_risk'] ? 30 : 0;
        $score += $signals['has_early_call'] ? 15 : 0;

        return min(100, $score);
    }

    /**
     * Build a beginner-friendly explanation from detected signals.
     *
     * @param  array<string, bool>  $signals
     */
    private function plainExplanationFor(array $signals): string
    {
        $parts = ['JavaScript prepares declarations before executing statements.'];

        if ($signals['has_function_declaration']) {
            $parts[] = 'Function declarations are initialized during that setup phase, so they can be called before their source line.';
        }

        if ($signals['has_var']) {
            $parts[] = '`var` declarations are initialized as `undefined`, which can hide bugs when read before assignment.';
        }

        if ($signals['has_let_or_const']) {
            $parts[] = '`let` and `const` are known during setup but cannot be read before their declaration line because of the temporal dead zone.';
        }

        if ($signals['has_function_expression']) {
            $parts[] = 'A function expression assigned to a variable is not callable until the assignment statement runs.';
        }

        return implode(' ', $parts);
    }

    /**
     * Return a compact execution trace learners can use in interviews.
     *
     * @param  array<string, bool>  $signals
     * @return array<int, array{phase: string, detail: string}>
     */
    private function executionTraceFor(array $signals): array
    {
        return [
            [
                'phase' => 'creation',
                'detail' => $signals['has_var']
                    ? 'Register `var` names and initialize them as undefined.'
                    : 'Register declarations before statement execution.',
            ],
            [
                'phase' => 'initialization',
                'detail' => $signals['has_function_declaration']
                    ? 'Initialize function declarations with callable function objects.'
                    : 'Wait until assignment statements run before variable values become useful.',
            ],
            [
                'phase' => 'execution',
                'detail' => 'Run statements top to bottom; early reads now reveal undefined, ReferenceError, or callable functions depending on declaration type.',
            ],
        ];
    }

    /**
     * Return a safer project-style example.
     */
    private function fixedExampleFor(string $snippet): string
    {
        if (trim($snippet) === '') {
            return '';
        }

        return <<<'JS'
function sayHi() {
  return 'Hello from a clearly declared helper';
}

const userName = 'Son';
const score = 10;
const run = () => 'Function value assigned before use';

console.log(sayHi());
console.log(userName);
console.log(score);
console.log(run());
JS;
    }

    /**
     * Return an interview-ready answer at the requested depth.
     *
     * @param  array<string, bool>  $signals
     */
    private function interviewAnswerFor(array $signals, string $level): string
    {
        $base = 'Hoisting means JavaScript registers declarations before executing code. Function declarations are initialized early, `var` starts as `undefined`, and `let` or `const` are unavailable before their declaration line because of the temporal dead zone.';

        if ($level === 'senior') {
            return $base.' In real projects I still declare before use, avoid `var`, and explain these rules through execution-context creation versus statement execution so the team does not rely on confusing language quirks.';
        }

        if ($signals['has_function_expression']) {
            return $base.' A function expression assigned to a variable follows the variable rule, so it cannot be safely called until the assignment has executed.';
        }

        return $base;
    }
}

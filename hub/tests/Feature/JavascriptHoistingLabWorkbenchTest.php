<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class JavascriptHoistingLabWorkbenchTest extends TestCase
{
    /**
     * The JavaScript hoisting lab renders the analysis form.
     */
    public function test_javascript_hoisting_lab_renders(): void
    {
        $response = $this->get('/workbench/javascript-hoisting-lab');

        $response
            ->assertOk()
            ->assertSee('JavaScript Hoisting Lab')
            ->assertSee('POST /api/practice/javascript-hoisting-lab')
            ->assertSee('Analyze hoisting')
            ->assertSee('Execution Trace')
            ->assertSee('Interview Answer')
            ->assertSee('Interview Rubric')
            ->assertSee('Interview Drill Plan')
            ->assertSee('Prediction Quiz')
            ->assertSee('Knowledge Check')
            ->assertSee('Debug Checklist')
            ->assertSee('Bug Report Template')
            ->assertSee('Anti-patterns')
            ->assertSee('Test Cases')
            ->assertSee('Runtime Examples')
            ->assertSee('Module Notes')
            ->assertSee('Refactor Plan')
            ->assertSee('Team Review Prompts')
            ->assertSee('Reviewer Notes')
            ->assertSee('ESLint Suggestions')
            ->assertSee('Commit Plan')
            ->assertSee('Flashcards')
            ->assertSee('Safer Rewrite')
            ->assertSee('Study Memo')
            ->assertSee('Copy memo')
            ->assertSee('renderHoistingTrace')
            ->assertSee('renderHoistingRubric')
            ->assertSee('renderHoistingDrillPlan')
            ->assertSee('renderHoistingQuiz')
            ->assertSee('renderHoistingKnowledgeCheck')
            ->assertSee('renderHoistingDebugChecklist')
            ->assertSee('renderHoistingBugReport')
            ->assertSee('renderHoistingAntiPatterns')
            ->assertSee('renderHoistingTestCases')
            ->assertSee('renderHoistingRuntimeExamples')
            ->assertSee('renderHoistingModuleNotes')
            ->assertSee('renderHoistingRefactorPlan')
            ->assertSee('renderHoistingTeamPrompts')
            ->assertSee('renderHoistingReviewerNotes')
            ->assertSee('renderHoistingEslint')
            ->assertSee('renderHoistingCommitPlan')
            ->assertSee('renderHoistingFlashcards')
            ->assertSee('escapeHoistingHtml')
            ->assertSee('replaceAll', false);
    }

    /**
     * The API returns hoisting signals, trace, rewrite, and interview guidance.
     */
    public function test_javascript_hoisting_lab_api_returns_analysis(): void
    {
        $response = $this->postJson('/api/practice/javascript-hoisting-lab', [
            'snippet' => <<<'JS'
sayHi();
function sayHi() {
  console.log('Hello');
}
console.log(userName);
var userName = 'Son';
// console.log(score);
let score = 10;
// run();
var run = function () {
  console.log('Assigned later');
};
JS,
            'focus' => 'mixed',
            'interview_level' => 'senior',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.topic', 'javascript-hoisting')
            ->assertJsonPath('data.detected_signals.has_var', true)
            ->assertJsonPath('data.detected_signals.has_let_or_const', true)
            ->assertJsonPath('data.detected_signals.has_function_declaration', true)
            ->assertJsonPath('data.detected_signals.has_function_expression', true)
            ->assertJsonPath('data.detected_signals.has_early_call', true)
            ->assertJsonPath('data.risk_score', 90)
            ->assertJsonPath('data.execution_trace.0.phase', 'creation')
            ->assertJsonPath('data.execution_trace.2.phase', 'execution')
            ->assertJsonPath('data.prediction_quiz.0.expected', 'It runs successfully.')
            ->assertJsonPath('data.prediction_quiz.1.expected', 'undefined')
            ->assertJsonPath('data.prediction_quiz.2.expected', 'ReferenceError')
            ->assertJsonPath('data.prediction_quiz.3.expected', 'TypeError')
            ->assertJsonPath('data.knowledge_check.0.answer', 'JavaScript registers declarations before executing statements.')
            ->assertJsonPath('data.knowledge_check.1.tag', 'function-declaration')
            ->assertJsonPath('data.knowledge_check.2.answer', 'undefined')
            ->assertJsonPath('data.knowledge_check.3.answer', 'It throws ReferenceError.')
            ->assertJsonPath('data.knowledge_check.4.explanation', 'The early value is undefined, so calling it can produce TypeError.')
            ->assertJsonPath('data.debug_checklist.0.check', 'Scan for reads before declarations.')
            ->assertJsonPath('data.debug_checklist.1.check', 'Replace `var` with `const` or `let` where possible.')
            ->assertJsonPath('data.debug_checklist.2.check', 'Confirm no `let` or `const` value is read inside the temporal dead zone.')
            ->assertJsonPath('data.debug_checklist.3.check', 'Check function expressions assigned to variables before call sites.')
            ->assertJsonPath('data.bug_report_template.severity', 'high')
            ->assertJsonPath('data.bug_report_template.title', 'Hoisting-prone JavaScript ordering can produce confusing runtime behavior')
            ->assertJsonPath('data.bug_report_template.suspected_cause', 'A function expression is assigned after a possible call site, so the variable can exist before the callable value exists.')
            ->assertJsonPath('data.bug_report_template.reproduction_steps.3', 'Call the function expression before its assignment to confirm the callable value is not ready.')
            ->assertJsonPath('data.bug_report_template.proposed_fix.3', 'Move `let` and `const` initialization before any branch, callback, or module side effect can read the binding.')
            ->assertJsonPath('data.bug_report_template.verification.2', 'Confirm lint rules catch future use-before-define and `var` usage where applicable.')
            ->assertJsonPath('data.flashcards.0.tag', 'definition')
            ->assertJsonPath('data.flashcards.2.back', '`undefined`, because `var` is hoisted and initialized.')
            ->assertJsonPath('data.flashcards.3.tag', 'tdz')
            ->assertJsonPath('data.flashcards.4.tag', 'function-expression')
            ->assertJsonPath('data.study_memo_markdown', fn (string $memo): bool => str_contains($memo, '# JavaScript Hoisting Study Memo')
                && str_contains($memo, 'Risk score: 90/100')
                && str_contains($memo, 'has_function_expression: yes'))
            ->assertJsonPath('data.fixed_example', fn (string $example): bool => str_contains($example, 'const userName')
                && str_contains($example, 'const run = ()'))
            ->assertJsonPath('data.interview_answer', fn (string $answer): bool => str_contains($answer, 'temporal dead zone')
                && str_contains($answer, 'execution-context creation'))
            ->assertJsonPath('data.interview_rubric.max_score', 100)
            ->assertJsonPath('data.interview_rubric.passing_score', 80)
            ->assertJsonPath('data.interview_rubric.criteria.1.name', 'Separates function declarations from function expressions.')
            ->assertJsonPath('data.interview_rubric.criteria.3.evidence', 'Snippet includes `let` or `const`, so TDZ must be explained.')
            ->assertJsonPath('data.interview_drill_plan.timebox', '20 minutes')
            ->assertJsonPath('data.interview_drill_plan.rounds.0.round', 'round 1: prediction')
            ->assertJsonPath('data.interview_drill_plan.rounds.1.pass_condition', 'The safer rewrite is readable top to bottom and preserves the intended behavior.')
            ->assertJsonPath('data.interview_drill_plan.rounds.3.goal', 'Connect hoisting to module loading and code review.')
            ->assertJsonPath('data.interview_drill_plan.follow_up.2', 'Practice converting a function expression to a named declaration and explain the tradeoff.')
            ->assertJsonPath('data.interview_drill_plan.follow_up.3', 'Practice explaining temporal dead zone without calling it the same as `undefined`.')
            ->assertJsonPath('data.anti_patterns.0.mistake', 'Saying JavaScript physically moves code to the top of the file.')
            ->assertJsonPath('data.anti_patterns.2.correction', 'Only function declarations are initialized early; function expressions become callable after assignment.')
            ->assertJsonPath('data.anti_patterns.3.correction', 'Use `const` or `let` and let TDZ reveal ordering mistakes earlier.')
            ->assertJsonPath('data.test_cases.0.expected', 'setup phase, creation phase, or declaration registration')
            ->assertJsonPath('data.test_cases.2.expected', 'undefined')
            ->assertJsonPath('data.test_cases.3.expected', 'ReferenceError')
            ->assertJsonPath('data.test_cases.4.expected', 'TypeError')
            ->assertJsonPath('data.runtime_examples.0.label', 'function declaration')
            ->assertJsonPath('data.runtime_examples.1.expected', 'Logs `undefined` because `var` is initialized before assignment.')
            ->assertJsonPath('data.runtime_examples.2.expected', 'Throws `ReferenceError` because `score` is read inside the temporal dead zone.')
            ->assertJsonPath('data.runtime_examples.3.expected', 'Throws `TypeError` because `run` is `undefined` before assignment.')
            ->assertJsonPath('data.module_notes.0.topic', 'module evaluation order')
            ->assertJsonPath('data.module_notes.1.topic', 'top-level side effects')
            ->assertJsonPath('data.module_notes.2.topic', 'exported function declarations')
            ->assertJsonPath('data.module_notes.3.project_check', 'If a module throws during initialization, check TDZ reads and circular imports before blaming the bundler.')
            ->assertJsonPath('data.refactor_plan.0.step', 'map reads before writes')
            ->assertJsonPath('data.refactor_plan.1.step', 'replace var')
            ->assertJsonPath('data.refactor_plan.2.step', 'stabilize callable helpers')
            ->assertJsonPath('data.refactor_plan.3.step', 'remove tdz hazards')
            ->assertJsonPath('data.refactor_plan.4.verification', 'A reviewer can predict output without relying on hidden hoisting behavior.')
            ->assertJsonPath('data.team_review_prompts.0.prompt', 'Can a reader predict this file top to bottom without knowing hoisting edge cases?')
            ->assertJsonPath('data.team_review_prompts.2.prompt', 'Why is `var` needed instead of `const` or `let`?')
            ->assertJsonPath('data.team_review_prompts.3.prompt', 'Can this function expression be moved above the call site or converted to a named function declaration?')
            ->assertJsonPath('data.team_review_prompts.4.why', 'TDZ errors often appear during startup or less common execution paths.')
            ->assertJsonPath('data.reviewer_notes.decision', 'request changes')
            ->assertJsonPath('data.reviewer_notes.priority', 'p1')
            ->assertJsonPath('data.reviewer_notes.blocking_comments.1', 'Please replace `var` with `const` or `let` unless this is intentionally legacy-compatible code.')
            ->assertJsonPath('data.reviewer_notes.blocking_comments.2', 'Please move the function expression assignment before any possible call site or convert it to a named declaration.')
            ->assertJsonPath('data.reviewer_notes.author_questions.2', 'Can this path throw a temporal dead zone ReferenceError during module startup?')
            ->assertJsonPath('data.reviewer_notes.merge_requirements.0', 'Declarations appear before their first read or call site.')
            ->assertJsonPath('data.eslint_suggestions.0.rule', 'no-use-before-define')
            ->assertJsonPath('data.eslint_suggestions.2.rule', 'no-var')
            ->assertJsonPath('data.eslint_suggestions.3.rule', 'func-style')
            ->assertJsonPath('data.commit_plan.branch', 'practice/javascript-hoisting-lab')
            ->assertJsonPath('data.commit_plan.changed_files.2', 'tests/js/hoisting-behavior.test.js')
            ->assertJsonPath('data.commit_plan.commits.0.message', 'Add JavaScript hoisting trace examples')
            ->assertJsonPath('data.commit_plan.commits.1.verification', 'Prediction quiz, test cases, and lint suggestions cover the risky behavior.')
            ->assertJsonPath('data.commit_plan.pull_request_checklist.0', 'Hoisting explanation does not claim code is physically moved.')
            ->assertJsonPath('data.practice_checks.2', 'Can you explain temporal dead zone for `let` and `const`?')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter JavascriptHoistingLabWorkbenchTest');
    }

    /**
     * Invalid hoisting lab payloads return validation errors.
     */
    public function test_javascript_hoisting_lab_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/javascript-hoisting-lab', [
            'snippet' => 'short',
            'focus' => 'magic',
            'interview_level' => 'principal',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'snippet',
                'focus',
                'interview_level',
            ]);
    }
}

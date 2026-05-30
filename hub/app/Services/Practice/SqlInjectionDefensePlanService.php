<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class SqlInjectionDefensePlanService
{
    /**
     * Build an interview-ready SQL Injection prevention plan.
     *
     * @param  array{query_name: string, query_style: string, input_surface: string, dynamic_parts: string, uses_bindings: bool}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $query = Str::studly($input['query_name']);
        $risk = $this->riskScore($input);
        $readinessScore = $this->readinessScore($input, $risk);
        $mergeGate = $this->mergeGate($input, $risk, $readinessScore);
        $recommendation = $this->recommendation($input, $risk);
        $attackExplanation = $this->attackExplanation($input);
        $safeQueryPatterns = $this->safeQueryPatterns($input);
        $defenseTaxonomy = $this->defenseTaxonomy($input);
        $fixExamples = $this->fixExamples($input);
        $allowlistReview = $this->allowlistReview($input);
        $testPayloads = $this->testPayloads();
        $testMatrix = $this->testMatrix($input);
        $featureTestSnippet = $this->featureTestSnippet($input, $query);
        $threatModel = $this->threatModel($input);
        $reviewQuestions = $this->reviewQuestions($input);
        $rolloutSteps = $this->rolloutSteps($input, $risk);
        $laravelReviewChecklist = $this->laravelReviewChecklist();
        $interviewAnswer = $this->interviewAnswer();

        return [
            'query' => $query,
            'risk_score' => $risk,
            'readiness_score' => $readinessScore,
            'merge_gate' => $mergeGate,
            'recommendation' => $recommendation,
            'attack_explanation' => $attackExplanation,
            'safe_query_patterns' => $safeQueryPatterns,
            'defense_taxonomy' => $defenseTaxonomy,
            'fix_examples' => $fixExamples,
            'unsafe_patterns' => $this->unsafePatterns(),
            'allowlist_review' => $allowlistReview,
            'test_payloads' => $testPayloads,
            'test_matrix' => $testMatrix,
            'feature_test_snippet' => $featureTestSnippet,
            'threat_model' => $threatModel,
            'review_questions' => $reviewQuestions,
            'rollout_steps' => $rolloutSteps,
            'laravel_review_checklist' => $laravelReviewChecklist,
            'search_terms' => $this->searchTerms($input, $risk, $readinessScore),
            'interview_answer' => $interviewAnswer,
            'review_packet_markdown' => $this->reviewPacketMarkdown(
                $query,
                $risk,
                $readinessScore,
                $mergeGate,
                $recommendation,
                $attackExplanation,
                $safeQueryPatterns,
                $defenseTaxonomy,
                $fixExamples,
                $allowlistReview,
                $testPayloads,
                $testMatrix,
                $featureTestSnippet,
                $threatModel,
                $reviewQuestions,
                $rolloutSteps,
                $laravelReviewChecklist,
                $this->searchTerms($input, $risk, $readinessScore),
                $interviewAnswer,
            ),
            'commands' => [
                'php artisan test --filter SqlInjectionDefensePlan',
                'php artisan route:list --path=sql-injection-defense-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * @param  array{query_style: string, dynamic_parts: string, uses_bindings: bool}  $input
     * @return array{score: int, label: string, reasons: array<int, string>}
     */
    private function riskScore(array $input): array
    {
        $score = 20;
        $reasons = [];

        if ($input['query_style'] === 'raw-sql') {
            $score += 35;
            $reasons[] = 'Raw SQL has higher review burden because string concatenation can slip in.';
        }

        if (! $input['uses_bindings']) {
            $score += 35;
            $reasons[] = 'User input is not protected by parameter binding.';
        }

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $score += 15;
            $reasons[] = 'Identifiers such as columns, tables, and sort direction cannot be safely parameterized and need allowlists.';
        }

        $score = min(100, $score);

        return [
            'score' => $score,
            'label' => match (true) {
                $score >= 80 => 'critical',
                $score >= 55 => 'high',
                $score >= 35 => 'medium',
                default => 'low',
            },
            'reasons' => $reasons,
        ];
    }

    /**
     * @param  array{query_style: string, dynamic_parts: string, uses_bindings: bool}  $input
     * @param  array{score: int, label: string, reasons: array<int, string>}  $risk
     * @return array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}
     */
    private function readinessScore(array $input, array $risk): array
    {
        $score = 100 - $risk['score'];
        $blockers = [];
        $nextActions = [];

        if (! $input['uses_bindings']) {
            $blockers[] = 'Values are not parameter-bound yet.';
            $nextActions[] = 'Replace concatenation or interpolation with query builder clauses or raw SQL bindings.';
        }

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $score -= 10;
            $blockers[] = 'Dynamic SQL identifiers need an allowlist before merge.';
            $nextActions[] = 'Add a fixed map for columns, tables, directions, or operators and test invalid keys.';
        }

        if ($input['query_style'] === 'raw-sql') {
            $score -= 10;
            $nextActions[] = 'Document why raw SQL is needed and show bindings in the review packet.';
        }

        $nextActions[] = 'Run malicious payload tests for OR 1=1, quoted comments, authorization scope, and unsafe sort input.';

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'label' => match (true) {
                $score >= 80 => 'ready',
                $score >= 60 => 'review',
                $score >= 40 => 'blocked',
                default => 'critical-blocked',
            },
            'blockers' => $blockers,
            'next_actions' => $nextActions,
        ];
    }

    /**
     * Decide whether this query path is merge-ready from a security-review perspective.
     *
     * @param  array{query_style: string, dynamic_parts: string, uses_bindings: bool}  $input
     * @param  array{score: int, label: string, reasons: array<int, string>}  $risk
     * @param  array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}  $readinessScore
     * @return array{decision: string, reason: string, required_evidence: array<int, string>, ci_checks: array<int, string>}
     */
    private function mergeGate(array $input, array $risk, array $readinessScore): array
    {
        $requiredEvidence = [
            'Feature test proving `OR 1=1` does not broaden the result set.',
            'Review note showing where user-controlled values become bindings.',
            'Authorization assertion proving SQL payloads cannot bypass tenant or role scope.',
        ];

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $requiredEvidence[] = 'Allowlist test proving invalid identifiers fall back safely or are rejected.';
        }

        if ($input['query_style'] === 'raw-sql') {
            $requiredEvidence[] = 'Raw SQL justification explaining why query builder or Eloquent is not enough.';
        }

        return [
            'decision' => match (true) {
                ! $input['uses_bindings'] || $risk['label'] === 'critical' => 'block',
                $readinessScore['label'] === 'review' || $risk['label'] === 'high' => 'review-required',
                default => 'merge-ready',
            },
            'reason' => match (true) {
                ! $input['uses_bindings'] => 'Do not merge while user-controlled values can enter SQL without bindings.',
                $risk['label'] === 'critical' => 'Critical SQL Injection risk must be fixed and reviewed before release.',
                $readinessScore['label'] === 'review' || $risk['label'] === 'high' => 'Merge only after another engineer checks bindings, allowlists, and payload tests.',
                default => 'The query path has the minimum evidence expected for a focused SQL Injection review.',
            },
            'required_evidence' => $requiredEvidence,
            'ci_checks' => [
                'php artisan test --filter SqlInjectionDefensePlan',
                'php artisan test --filter Injection',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * @param  array{query_style: string, uses_bindings: bool}  $input
     * @param  array{score: int, label: string, reasons: array<int, string>}  $risk
     */
    private function recommendation(array $input, array $risk): string
    {
        if (! $input['uses_bindings']) {
            return 'Replace string-concatenated SQL with parameterized queries or Laravel query builder bindings before this code ships.';
        }

        if ($input['query_style'] === 'query-builder') {
            return 'Keep using the query builder, but review dynamic identifiers with allowlists and test malicious payloads.';
        }

        return "Risk is {$risk['label']}; keep values bound, allowlist identifiers, and add tests that prove payloads are treated as data.";
    }

    /**
     * @param  array{input_surface: string}  $input
     * @return array<int, string>
     */
    private function attackExplanation(array $input): array
    {
        return [
            "SQL Injection happens when {$input['input_surface']} input changes the SQL structure instead of being treated as data.",
            'The attacker tries to close a string, add boolean logic, comment out the rest of the query, or stack another statement.',
            'Parameterized queries separate SQL code from values, so payloads such as `\' OR 1=1 --` stay data instead of becoming logic.',
        ];
    }

    /**
     * @param  array{dynamic_parts: string}  $input
     * @return array<string, string>
     */
    private function safeQueryPatterns(array $input): array
    {
        return [
            'eloquent_or_builder' => <<<'PHP'
User::query()
    ->where('email', $request->string('email'))
    ->first();
PHP,
            'raw_with_bindings' => <<<'PHP'
DB::select('select * from users where email = ?', [$request->input('email')]);
PHP,
            'named_bindings' => <<<'PHP'
DB::select('select * from users where status = :status', ['status' => $status]);
PHP,
            'dynamic_identifier' => $input['dynamic_parts'] === 'order-by'
                ? <<<'PHP'
$column = Arr::get(['name' => 'name', 'created' => 'created_at'], $request->input('sort'), 'created_at');
$direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
User::query()->orderBy($column, $direction)->get();
PHP
                : 'For table or column names, choose from a fixed allowlist; never bind or concatenate arbitrary identifiers.',
        ];
    }

    /**
     * Explain which query parts can use bindings and which require structural controls.
     *
     * @param  array{query_style: string, dynamic_parts: string}  $input
     * @return array<int, array{category: string, binding_rule: string, examples: array<int, string>, review_control: string, common_mistake: string}>
     */
    private function defenseTaxonomy(array $input): array
    {
        return [
            [
                'category' => 'Values',
                'binding_rule' => 'Can and should be parameter-bound.',
                'examples' => ['email', 'status', 'search term', 'date range value'],
                'review_control' => 'Use Eloquent where clauses, query builder clauses, or raw SQL positional/named bindings.',
                'common_mistake' => 'Concatenating request values into SQL because the field was already validated.',
            ],
            [
                'category' => 'Identifiers',
                'binding_rule' => 'Cannot be parameter-bound as values; must be selected from an allowlist.',
                'examples' => ['table name', 'column name', 'sort direction', $input['dynamic_parts']],
                'review_control' => 'Map user choices to fixed internal identifiers before building the query.',
                'common_mistake' => 'Trying to bind a column name or passing request input directly to orderBy.',
            ],
            [
                'category' => 'Raw SQL',
                'binding_rule' => 'Allowed only when the SQL shape is fixed and every user value is bound.',
                'examples' => ['reporting query', 'window function', "{$input['query_style']} fallback"],
                'review_control' => 'Document why raw SQL is needed, show bindings, and add payload tests in the review packet.',
                'common_mistake' => 'Using DB::raw as an escape hatch for complex conditions without proving bindings.',
            ],
        ];
    }

    /**
     * @param  array{query_style: string, dynamic_parts: string, input_surface: string}  $input
     * @return array<int, array{label: string, before: string, after: string, review_note: string}>
     */
    private function fixExamples(array $input): array
    {
        $examples = [
            [
                'label' => 'Replace concatenated value SQL',
                'before' => <<<'PHP'
$sql = "select * from users where email = '".$request->input('email')."'";
$user = DB::select($sql);
PHP,
                'after' => <<<'PHP'
$user = DB::select(
    'select * from users where email = ?',
    [$request->input('email')]
);
PHP,
                'review_note' => 'The SQL shape is fixed and the email value travels as a binding.',
            ],
            [
                'label' => 'Prefer query builder for normal filters',
                'before' => <<<'PHP'
DB::table('users')
    ->whereRaw("status = '".$request->input('status')."'")
    ->get();
PHP,
                'after' => <<<'PHP'
DB::table('users')
    ->where('status', $request->string('status'))
    ->get();
PHP,
                'review_note' => 'A normal where value should use query builder bindings instead of whereRaw.',
            ],
        ];

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $examples[] = [
                'label' => 'Allowlist dynamic identifiers',
                'before' => <<<'PHP'
User::query()
    ->orderBy($request->input('sort'), $request->input('direction'))
    ->get();
PHP,
                'after' => <<<'PHP'
$columns = ['name' => 'name', 'created' => 'created_at'];
$directions = ['asc' => 'asc', 'desc' => 'desc'];

$column = $columns[$request->input('sort')] ?? 'created_at';
$direction = $directions[$request->input('direction')] ?? 'desc';

User::query()->orderBy($column, $direction)->get();
PHP,
                'review_note' => 'Identifiers cannot be value-bound, so request choices must map through a fixed allowlist.',
            ];
        }

        if ($input['query_style'] === 'eloquent') {
            $examples[] = [
                'label' => 'Keep Eloquent scopes value-bound',
                'before' => <<<'PHP'
User::whereRaw("name like '%".$request->input('q')."%'")->get();
PHP,
                'after' => <<<'PHP'
User::query()
    ->where('name', 'like', '%'.$request->string('q').'%')
    ->get();
PHP,
                'review_note' => 'Eloquent still needs care when dropping into raw fragments.',
            ];
        }

        return $examples;
    }

    /**
     * @return array<int, string>
     */
    private function unsafePatterns(): array
    {
        return [
            'Concatenating request input into SQL strings.',
            'Using `DB::raw()` with untrusted values.',
            'Trusting escaped strings as the main defense instead of binding parameters.',
            'Letting users choose table names, column names, or sort direction without an allowlist.',
        ];
    }

    /**
     * @param  array{dynamic_parts: string}  $input
     * @return array<int, string>
     */
    private function allowlistReview(array $input): array
    {
        return [
            "Dynamic part under review: {$input['dynamic_parts']}.",
            'Values belong in bindings.',
            'Identifiers belong in explicit allowlists.',
            'Sort direction should collapse to `asc` or `desc`, never arbitrary input.',
        ];
    }

    /**
     * @return array<int, array{payload: string, expected: string}>
     */
    private function testPayloads(): array
    {
        return [
            ['payload' => "' OR 1=1 --", 'expected' => 'No extra rows are returned.'],
            ['payload' => "admin@example.com' --", 'expected' => 'The value is searched literally or rejected by validation.'],
            ['payload' => 'name desc; drop table users; --', 'expected' => 'Sort input is rejected or mapped through an allowlist.'],
        ];
    }

    /**
     * @param  array{input_surface: string, dynamic_parts: string}  $input
     * @return array<int, array{case: string, payload: string, assertion: string}>
     */
    private function testMatrix(array $input): array
    {
        $matrix = [
            [
                'case' => "{$input['input_surface']} value payload stays data",
                'payload' => "' OR 1=1 --",
                'assertion' => 'Response returns only authorized matching rows, no broad result set.',
            ],
            [
                'case' => 'quoted comment payload does not bypass filters',
                'payload' => "admin@example.com' --",
                'assertion' => 'Response treats the value literally or rejects it through validation.',
            ],
            [
                'case' => 'authorization remains enforced',
                'payload' => "' OR role = 'admin' --",
                'assertion' => 'The request still receives 403 or scoped results when the user lacks access.',
            ],
        ];

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $matrix[] = [
                'case' => 'dynamic identifier is allowlisted',
                'payload' => 'name desc; drop table users; --',
                'assertion' => 'Invalid identifier input is rejected or mapped to a default safe identifier.',
            ];
        }

        return $matrix;
    }

    /**
     * @param  array{input_surface: string, dynamic_parts: string}  $input
     */
    private function featureTestSnippet(array $input, string $query): string
    {
        $methodSuffix = $this->testMethodSuffix($query);
        $route = match ($input['input_surface']) {
            'login-form' => '/login',
            'filter-api' => '/api/users',
            'admin-report' => '/admin/reports/users',
            default => '/users',
        };

        $sortAssertion = in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)
            ? <<<PHP

    \$this->getJson('{$route}?sort=name%20desc;%20drop%20table%20users;--')
        ->assertOk()
        ->assertJsonMissing(['unsafe_sort_applied' => true]);
PHP
            : '';

        return <<<PHP
public function test_{$methodSuffix}_rejects_sql_injection_payloads(): void
{
    User::factory()->create(['email' => 'admin@example.com']);
    User::factory()->create(['email' => 'reader@example.com']);

    \$this->getJson('{$route}?q='.urlencode("' OR 1=1 --"))
        ->assertOk()
        ->assertJsonMissing(['email' => 'reader@example.com']);

    \$this->getJson('{$route}?q='.urlencode("admin@example.com' --"))
        ->assertOk()
        ->assertJsonFragment(['email' => 'admin@example.com']);{$sortAssertion}
}
PHP;
    }

    /**
     * Convert StudlyCase into a lower snake-case method suffix.
     */
    private function testMethodSuffix(string $query): string
    {
        return Str::of($query)->snake()->lower()->toString();
    }

    /**
     * @param  array{input_surface: string, dynamic_parts: string, query_style: string}  $input
     * @return array<int, array{boundary: string, risk: string, control: string}>
     */
    private function threatModel(array $input): array
    {
        return [
            [
                'boundary' => $input['input_surface'],
                'risk' => 'User-controlled text can cross from application input into SQL construction.',
                'control' => 'Validate shape early, then pass values through bindings instead of concatenation.',
            ],
            [
                'boundary' => $input['dynamic_parts'],
                'risk' => 'Dynamic query structure can let users choose SQL identifiers or operators.',
                'control' => 'Use explicit maps for columns, tables, sort directions, and operators.',
            ],
            [
                'boundary' => $input['query_style'],
                'risk' => 'Lower-level query APIs make unsafe string building easier to hide in review.',
                'control' => 'Require review evidence showing bindings, allowlists, and payload tests before merge.',
            ],
        ];
    }

    /**
     * @param  array{dynamic_parts: string, query_style: string, uses_bindings: bool}  $input
     * @return array<int, string>
     */
    private function reviewQuestions(array $input): array
    {
        $questions = [
            'Where does request input first enter this query path?',
            'Which values are bound, and where can a reviewer see those bindings?',
            'Which tests prove payloads stay data instead of becoming SQL logic?',
        ];

        if (! $input['uses_bindings']) {
            $questions[] = 'What exact concatenation or interpolation will be removed before merge?';
        }

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $questions[] = 'Which allowlist maps user choices to approved SQL identifiers?';
        }

        if ($input['query_style'] === 'raw-sql') {
            $questions[] = 'Why is raw SQL needed here instead of Eloquent or the query builder?';
        }

        return $questions;
    }

    /**
     * @param  array{query_style: string, dynamic_parts: string, uses_bindings: bool}  $input
     * @param  array{score: int, label: string, reasons: array<int, string>}  $risk
     * @return array<int, string>
     */
    private function rolloutSteps(array $input, array $risk): array
    {
        $steps = [
            'Add or update tests with malicious payloads before changing query code.',
            'Replace unsafe value handling with query builder clauses or raw SQL bindings.',
            'Run focused SQL Injection tests and route checks before broader regression tests.',
        ];

        if (in_array($input['dynamic_parts'], ['order-by', 'table-name', 'column-name'], true)) {
            $steps[] = 'Add an allowlist map for dynamic identifiers and test invalid keys.';
        }

        if ($risk['label'] === 'critical') {
            $steps[] = 'Block release until the unsafe path is fixed and reviewed by another engineer.';
        }

        return $steps;
    }

    /**
     * @return array<int, string>
     */
    private function laravelReviewChecklist(): array
    {
        return [
            'Prefer Eloquent or the query builder for normal reads and writes.',
            'When raw SQL is necessary, use positional or named bindings.',
            'Never put request input inside `DB::raw()`.',
            'Allowlist dynamic columns, tables, and directions.',
            'Add feature tests with malicious payloads and assert authorization still applies.',
        ];
    }

    /**
     * @param  array{query_style: string, dynamic_parts: string, input_surface: string}  $input
     * @param  array{label: string}  $risk
     * @param  array{label: string}  $readinessScore
     * @return array<int, string>
     */
    private function searchTerms(array $input, array $risk, array $readinessScore): array
    {
        return [
            'sql-injection',
            'parameterized-query',
            'prepared-statement',
            'laravel-bindings',
            "query-style:{$input['query_style']}",
            "input-surface:{$input['input_surface']}",
            "dynamic-part:{$input['dynamic_parts']}",
            "risk:{$risk['label']}",
            "readiness:{$readinessScore['label']}",
        ];
    }

    private function interviewAnswer(): string
    {
        return 'SQL Injection is when user input becomes part of SQL logic instead of staying data. The practical defense is parameterized queries: SQL structure is fixed and user values are passed separately as bindings. In Laravel, Eloquent and the query builder handle most value binding for you, but raw SQL must use bindings, and dynamic identifiers such as order-by columns need allowlists. In an interview, mention payloads like OR 1=1, explain why escaping alone is weaker than binding, and say you would test malicious inputs.';
    }

    /**
     * Build a copy-ready review packet for pull requests or interview notes.
     *
     * @param  array{score: int, label: string, reasons: array<int, string>}  $risk
     * @param  array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}  $readinessScore
     * @param  array{decision: string, reason: string, required_evidence: array<int, string>, ci_checks: array<int, string>}  $mergeGate
     * @param  array<int, string>  $attackExplanation
     * @param  array<string, string>  $safeQueryPatterns
     * @param  array<int, array{category: string, binding_rule: string, examples: array<int, string>, review_control: string, common_mistake: string}>  $defenseTaxonomy
     * @param  array<int, array{label: string, before: string, after: string, review_note: string}>  $fixExamples
     * @param  array<int, string>  $allowlistReview
     * @param  array<int, array{payload: string, expected: string}>  $testPayloads
     * @param  array<int, array{case: string, payload: string, assertion: string}>  $testMatrix
     * @param  non-empty-string  $featureTestSnippet
     * @param  array<int, array{boundary: string, risk: string, control: string}>  $threatModel
     * @param  array<int, string>  $reviewQuestions
     * @param  array<int, string>  $rolloutSteps
     * @param  array<int, string>  $laravelReviewChecklist
     * @param  array<int, string>  $searchTerms
     */
    private function reviewPacketMarkdown(
        string $query,
        array $risk,
        array $readinessScore,
        array $mergeGate,
        string $recommendation,
        array $attackExplanation,
        array $safeQueryPatterns,
        array $defenseTaxonomy,
        array $fixExamples,
        array $allowlistReview,
        array $testPayloads,
        array $testMatrix,
        string $featureTestSnippet,
        array $threatModel,
        array $reviewQuestions,
        array $rolloutSteps,
        array $laravelReviewChecklist,
        array $searchTerms,
        string $interviewAnswer,
    ): string {
        $riskReasons = $risk['reasons'] === []
            ? ['No high-risk signal was detected, but payload tests are still required.']
            : $risk['reasons'];

        return implode("\n", [
            "# SQL Injection Defense Packet: {$query}",
            '',
            '## Risk',
            "- Level: {$risk['label']} ({$risk['score']}/100)",
            ...$this->markdownBullets($riskReasons),
            '',
            '## Readiness',
            "- Level: {$readinessScore['label']} ({$readinessScore['score']}/100)",
            ...$this->markdownBullets($readinessScore['blockers'] === [] ? ['No merge-blocking SQL Injection issue is currently detected.'] : $readinessScore['blockers']),
            '',
            '## Next Actions',
            ...$this->markdownBullets($readinessScore['next_actions']),
            '',
            '## Merge Gate',
            "- Decision: {$mergeGate['decision']}",
            "- Reason: {$mergeGate['reason']}",
            'Required evidence:',
            ...$this->markdownBullets($mergeGate['required_evidence']),
            'CI checks:',
            ...$this->markdownBullets($mergeGate['ci_checks']),
            '',
            '## Recommendation',
            $recommendation,
            '',
            '## Attack Explanation',
            ...$this->markdownBullets($attackExplanation),
            '',
            '## Threat Model',
            ...$this->threatModelMarkdownBullets($threatModel),
            '',
            '## Safe Query Patterns',
            '```json',
            json_encode($safeQueryPatterns, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}',
            '```',
            '',
            '## Defense Taxonomy',
            ...$this->defenseTaxonomyMarkdownBullets($defenseTaxonomy),
            '',
            '## Fix Examples',
            ...$this->fixExampleMarkdownBlocks($fixExamples),
            '',
            '## Allowlist Review',
            ...$this->markdownBullets($allowlistReview),
            '',
            '## Payload Tests',
            ...$this->payloadMarkdownBullets($testPayloads),
            '',
            '## Test Matrix',
            ...$this->testMatrixMarkdownBullets($testMatrix),
            '',
            '## Feature Test Snippet',
            '```php',
            $featureTestSnippet,
            '```',
            '',
            '## Review Questions',
            ...$this->markdownBullets($reviewQuestions),
            '',
            '## Rollout Steps',
            ...$this->markdownBullets($rolloutSteps),
            '',
            '## Laravel Review Checklist',
            ...$this->markdownBullets($laravelReviewChecklist),
            '',
            '## Search Terms',
            ...$this->markdownBullets($searchTerms),
            '',
            '## Interview Answer',
            $interviewAnswer,
        ]);
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function markdownBullets(array $items): array
    {
        return array_map(fn (string $item): string => "- {$item}", $items);
    }

    /**
     * @param  array<int, array{payload: string, expected: string}>  $items
     * @return array<int, string>
     */
    private function payloadMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => sprintf('- `%s` -> %s', $item['payload'], $item['expected']),
            $items,
        );
    }

    /**
     * @param  array<int, array{label: string, before: string, after: string, review_note: string}>  $items
     * @return array<int, string>
     */
    private function fixExampleMarkdownBlocks(array $items): array
    {
        return collect($items)
            ->flatMap(fn (array $item): array => [
                "### {$item['label']}",
                $item['review_note'],
                '',
                'Before:',
                '```php',
                $item['before'],
                '```',
                'After:',
                '```php',
                $item['after'],
                '```',
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{case: string, payload: string, assertion: string}>  $items
     * @return array<int, string>
     */
    private function testMatrixMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => sprintf('- %s: `%s` -> %s', $item['case'], $item['payload'], $item['assertion']),
            $items,
        );
    }

    /**
     * @param  array<int, array{category: string, binding_rule: string, examples: array<int, string>, review_control: string, common_mistake: string}>  $items
     * @return array<int, string>
     */
    private function defenseTaxonomyMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => sprintf(
                '- %s: %s Examples: %s. Review control: %s Common mistake: %s',
                $item['category'],
                $item['binding_rule'],
                implode(', ', $item['examples']),
                $item['review_control'],
                $item['common_mistake'],
            ),
            $items,
        );
    }

    /**
     * @param  array<int, array{boundary: string, risk: string, control: string}>  $items
     * @return array<int, string>
     */
    private function threatModelMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => sprintf('- `%s`: %s Control: %s', $item['boundary'], $item['risk'], $item['control']),
            $items,
        );
    }
}

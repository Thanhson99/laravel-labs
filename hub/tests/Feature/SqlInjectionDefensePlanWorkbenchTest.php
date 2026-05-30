<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class SqlInjectionDefensePlanWorkbenchTest extends TestCase
{
    /**
     * The SQL Injection defense workbench renders the planner.
     */
    public function test_sql_injection_defense_workbench_renders(): void
    {
        $this->get('/workbench/sql-injection-defense-plan')
            ->assertOk()
            ->assertSee('SQL Injection defense starts with parameterized queries')
            ->assertSee('POST /api/practice/sql-injection-defense-plan')
            ->assertSee('Plan SQL defense')
            ->assertSee('copySqlInjectionPacket')
            ->assertSee('Scenario Presets')
            ->assertSee('Defense Taxonomy')
            ->assertSee('Merge Gate')
            ->assertSee('data-sql-injection-preset="unsafe-search"', false)
            ->assertSee('data-sql-injection-preset="admin-report-sort"', false)
            ->assertSee('Uses parameter bindings');
    }

    /**
     * The API returns risk, safe patterns, payloads, and interview guidance.
     */
    public function test_sql_injection_defense_api_returns_plan(): void
    {
        $this->postJson('/api/practice/sql-injection-defense-plan', [
            'query_name' => 'User Search',
            'query_style' => 'raw-sql',
            'input_surface' => 'search-box',
            'dynamic_parts' => 'order-by',
            'uses_bindings' => false,
        ])
            ->assertOk()
            ->assertJsonPath('data.query', 'UserSearch')
            ->assertJsonPath('data.risk_score.label', 'critical')
            ->assertJsonPath('data.readiness_score.label', 'critical-blocked')
            ->assertJsonPath('data.readiness_score.blockers.0', 'Values are not parameter-bound yet.')
            ->assertJsonPath('data.merge_gate.decision', 'block')
            ->assertJsonPath('data.merge_gate.reason', 'Do not merge while user-controlled values can enter SQL without bindings.')
            ->assertJsonPath('data.merge_gate.required_evidence.0', 'Feature test proving `OR 1=1` does not broaden the result set.')
            ->assertJsonPath('data.merge_gate.ci_checks.0', 'php artisan test --filter SqlInjectionDefensePlan')
            ->assertJsonPath('data.search_terms.0', 'sql-injection')
            ->assertJsonPath('data.search_terms.7', 'risk:critical')
            ->assertJsonPath('data.recommendation', 'Replace string-concatenated SQL with parameterized queries or Laravel query builder bindings before this code ships.')
            ->assertJsonPath('data.safe_query_patterns.raw_with_bindings', "DB::select('select * from users where email = ?', [\$request->input('email')]);")
            ->assertJsonPath('data.defense_taxonomy.0.category', 'Values')
            ->assertJsonPath('data.defense_taxonomy.1.category', 'Identifiers')
            ->assertJsonPath('data.defense_taxonomy.1.binding_rule', 'Cannot be parameter-bound as values; must be selected from an allowlist.')
            ->assertJsonPath('data.defense_taxonomy.2.category', 'Raw SQL')
            ->assertJsonPath('data.fix_examples.0.label', 'Replace concatenated value SQL')
            ->assertJsonPath('data.fix_examples.2.label', 'Allowlist dynamic identifiers')
            ->assertJsonPath('data.test_payloads.0.payload', "' OR 1=1 --")
            ->assertJsonPath('data.test_matrix.3.case', 'dynamic identifier is allowlisted')
            ->assertJson(
                fn ($json) => $json->where(
                    'data.feature_test_snippet',
                    fn (string $snippet): bool => str_contains($snippet, 'test_user_search_rejects_sql_injection_payloads')
                        && str_contains($snippet, '/users?q=')
                        && str_contains($snippet, 'unsafe_sort_applied')
                )
            )
            ->assertJsonPath('data.threat_model.0.boundary', 'search-box')
            ->assertJsonPath('data.review_questions.3', 'What exact concatenation or interpolation will be removed before merge?')
            ->assertJsonPath('data.rollout_steps.4', 'Block release until the unsafe path is fixed and reviewed by another engineer.')
            ->assertJson(
                fn ($json) => $json->where(
                    'data.review_packet_markdown',
                    fn (string $packet): bool => str_contains($packet, '# SQL Injection Defense Packet: UserSearch')
                        && str_contains($packet, '## Readiness')
                        && str_contains($packet, '## Next Actions')
                        && str_contains($packet, '## Merge Gate')
                        && str_contains($packet, '## Defense Taxonomy')
                        && str_contains($packet, '## Fix Examples')
                        && str_contains($packet, '## Payload Tests')
                        && str_contains($packet, '## Test Matrix')
                        && str_contains($packet, '## Feature Test Snippet')
                        && str_contains($packet, '## Search Terms')
                        && str_contains($packet, '## Interview Answer')
                )
            )
            ->assertJsonPath('data.commands.1', 'php artisan route:list --path=sql-injection-defense-plan')
            ->assertSee('SQL Injection is when user input becomes part of SQL logic');
    }

    /**
     * Invalid SQL Injection defense payloads return validation errors.
     */
    public function test_sql_injection_defense_api_validates_payload(): void
    {
        $this->postJson('/api/practice/sql-injection-defense-plan', [
            'query_name' => '<bad>',
            'query_style' => 'string',
            'input_surface' => 'unknown',
            'dynamic_parts' => 'all',
            'uses_bindings' => 'maybe',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'query_name',
                'query_style',
                'input_surface',
                'dynamic_parts',
                'uses_bindings',
            ]);
    }
}

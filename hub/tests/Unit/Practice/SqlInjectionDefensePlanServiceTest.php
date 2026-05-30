<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\SqlInjectionDefensePlanService;
use PHPUnit\Framework\TestCase;

final class SqlInjectionDefensePlanServiceTest extends TestCase
{
    /**
     * Raw SQL without bindings is treated as critical risk.
     */
    public function test_raw_sql_without_bindings_is_critical(): void
    {
        $plan = (new SqlInjectionDefensePlanService)->plan([
            'query_name' => 'Admin Report',
            'query_style' => 'raw-sql',
            'input_surface' => 'admin-report',
            'dynamic_parts' => 'column-name',
            'uses_bindings' => false,
        ]);

        $this->assertSame('AdminReport', $plan['query']);
        $this->assertSame('critical', $plan['risk_score']['label']);
        $this->assertSame('critical-blocked', $plan['readiness_score']['label']);
        $this->assertContains('Values are not parameter-bound yet.', $plan['readiness_score']['blockers']);
        $this->assertContains('Dynamic SQL identifiers need an allowlist before merge.', $plan['readiness_score']['blockers']);
        $this->assertSame('block', $plan['merge_gate']['decision']);
        $this->assertSame('Do not merge while user-controlled values can enter SQL without bindings.', $plan['merge_gate']['reason']);
        $this->assertContains('Allowlist test proving invalid identifiers fall back safely or are rejected.', $plan['merge_gate']['required_evidence']);
        $this->assertContains('Raw SQL justification explaining why query builder or Eloquent is not enough.', $plan['merge_gate']['required_evidence']);
        $this->assertSame('php artisan test --filter SqlInjectionDefensePlan', $plan['merge_gate']['ci_checks'][0]);
        $this->assertContains('risk:critical', $plan['search_terms']);
        $this->assertContains('readiness:critical-blocked', $plan['search_terms']);
        $this->assertSame('Values belong in bindings.', $plan['allowlist_review'][1]);
        $this->assertSame('Values', $plan['defense_taxonomy'][0]['category']);
        $this->assertSame('Identifiers', $plan['defense_taxonomy'][1]['category']);
        $this->assertSame('Cannot be parameter-bound as values; must be selected from an allowlist.', $plan['defense_taxonomy'][1]['binding_rule']);
        $this->assertContains('column-name', $plan['defense_taxonomy'][1]['examples']);
        $this->assertSame('Raw SQL', $plan['defense_taxonomy'][2]['category']);
        $this->assertSame('Replace concatenated value SQL', $plan['fix_examples'][0]['label']);
        $this->assertSame('Allowlist dynamic identifiers', $plan['fix_examples'][2]['label']);
        $this->assertSame('dynamic identifier is allowlisted', $plan['test_matrix'][3]['case']);
        $this->assertStringContainsString('test_admin_report_rejects_sql_injection_payloads', $plan['feature_test_snippet']);
        $this->assertStringContainsString('/admin/reports/users?q=', $plan['feature_test_snippet']);
        $this->assertStringContainsString('/admin/reports/users?sort=', $plan['feature_test_snippet']);
        $this->assertSame('admin-report', $plan['threat_model'][0]['boundary']);
        $this->assertContains('Which allowlist maps user choices to approved SQL identifiers?', $plan['review_questions']);
        $this->assertContains('Block release until the unsafe path is fixed and reviewed by another engineer.', $plan['rollout_steps']);
        $this->assertStringContainsString('# SQL Injection Defense Packet: AdminReport', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Readiness', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Merge Gate', $plan['review_packet_markdown']);
        $this->assertStringContainsString('- Decision: block', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Defense Taxonomy', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Fix Examples', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Test Matrix', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Feature Test Snippet', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Search Terms', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Review Questions', $plan['review_packet_markdown']);
        $this->assertStringContainsString('parameterized queries', $plan['interview_answer']);
        $this->assertSame('php artisan test --filter SqlInjectionDefensePlan', $plan['commands'][0]);
    }

    /**
     * Bound query builder examples remain lower risk but still review identifiers.
     */
    public function test_bound_query_builder_is_lower_risk(): void
    {
        $plan = (new SqlInjectionDefensePlanService)->plan([
            'query_name' => 'User Search',
            'query_style' => 'query-builder',
            'input_surface' => 'search-box',
            'dynamic_parts' => 'where-value',
            'uses_bindings' => true,
        ]);

        $this->assertSame('low', $plan['risk_score']['label']);
        $this->assertSame('ready', $plan['readiness_score']['label']);
        $this->assertSame('merge-ready', $plan['merge_gate']['decision']);
        $this->assertSame([], $plan['readiness_score']['blockers']);
        $this->assertContains('readiness:ready', $plan['search_terms']);
        $this->assertSame('Can and should be parameter-bound.', $plan['defense_taxonomy'][0]['binding_rule']);
        $this->assertContains('where-value', $plan['defense_taxonomy'][1]['examples']);
        $this->assertStringContainsString('Keep using the query builder', $plan['recommendation']);
        $this->assertSame("' OR 1=1 --", $plan['test_payloads'][0]['payload']);
        $this->assertCount(2, $plan['fix_examples']);
        $this->assertCount(3, $plan['test_matrix']);
        $this->assertStringContainsString('test_user_search_rejects_sql_injection_payloads', $plan['feature_test_snippet']);
        $this->assertStringNotContainsString('unsafe_sort_applied', $plan['feature_test_snippet']);
        $this->assertNotContains('Block release until the unsafe path is fixed and reviewed by another engineer.', $plan['rollout_steps']);
        $this->assertStringContainsString('No high-risk signal was detected', $plan['review_packet_markdown']);
    }
}

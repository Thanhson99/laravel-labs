<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LsmTreePlanWorkbenchTest extends TestCase
{
    /**
     * The LSM Tree workbench renders the storage-engine form.
     */
    public function test_lsm_tree_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/lsm-tree-plan');

        $response
            ->assertOk()
            ->assertSee('LSM Tree Plan Workbench')
            ->assertSee('POST /api/practice/lsm-tree-plan')
            ->assertSee('LsmTreePlanService')
            ->assertSee('Plan LSM Tree tradeoffs');
    }

    /**
     * Write-heavy workloads show why LSM Trees optimize sequential writes.
     */
    public function test_lsm_tree_plan_api_returns_write_optimized_plan(): void
    {
        $response = $this->postJson('/api/practice/lsm-tree-plan', [
            'workload_pattern' => 'write-heavy',
            'write_rate_per_second' => 25000,
            'read_miss_ratio' => 'high',
            'compaction_strategy' => 'leveled',
            'bloom_filter_enabled' => 'yes',
            'schema_expectation' => 'schema-flexible',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.claim', 'NoSQL is not fast simply because it has no schema.')
            ->assertJsonPath('data.architecture_decision_record.status', 'accepted-with-operational-guards')
            ->assertJsonPath('data.write_path.0.step', '1. Append to WAL')
            ->assertJsonPath('data.write_path.2.step', '3. Flush immutable segment')
            ->assertJsonPath('data.component_map.4.name', 'Bloom Filter')
            ->assertJsonPath('data.compaction_plan.strategy', 'leveled')
            ->assertJsonPath('data.bloom_filter_plan.enabled', true)
            ->assertJsonPath('data.amplification_model.0.name', 'write amplification')
            ->assertJsonPath('data.range_scan_plan.0.area', 'key design')
            ->assertJsonPath('data.tombstone_ttl_policy.0.policy', 'Treat delete as a write first, not immediate physical removal.')
            ->assertJsonPath('data.capacity_plan.compaction', 'Budget more compaction write bandwidth because leveled compaction keeps reads predictable by rewriting data more often.')
            ->assertJsonPath('data.cost_model.0.cost', 'memory')
            ->assertJsonPath('data.engine_comparison.0.engine', 'LSM-backed key-value or wide-column store')
            ->assertJsonPath('data.engine_comparison.2.engine', 'Search or analytics projection')
            ->assertJsonPath('data.decision_rules.1.question', 'Are point-read misses common?')
            ->assertJsonPath('data.benchmark_plan.0.scenario', 'steady write ingest')
            ->assertJsonPath('data.benchmark_plan.4.scenario', 'TTL or delete wave')
            ->assertJsonPath('data.rollout_plan.0.phase', 'model')
            ->assertJsonPath('data.rollout_plan.3.phase', 'ramp')
            ->assertJsonPath('data.anti_patterns.0.name', 'schema myth')
            ->assertJsonPath('data.incident_runbook.0.phase', 'triage')
            ->assertJsonPath('data.fit_matrix.0.fit', 'strong')
            ->assertJsonPath('data.data_model_review.0.area', 'partition key')
            ->assertJsonPath('data.query_contract.0.query_shape', 'point lookup by full key')
            ->assertJsonPath('data.interview_checklist.0', 'Start by rejecting the schema myth: schema flexibility is not the main speed mechanism.')
            ->assertJsonPath('data.failure_modes.1.failure', 'compaction backlog')
            ->assertJsonPath('data.workload_score.level', 'strong-fit')
            ->assertJsonPath('data.slo_guardrails.0.signal', 'foreground p99 latency')
            ->assertJsonPath('data.slo_guardrails.1.signal', 'compaction debt')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter LsmTreePlan');
    }

    /**
     * Missing Bloom Filters are called out for high read-miss workloads.
     */
    public function test_lsm_tree_plan_api_warns_when_bloom_filter_is_missing(): void
    {
        $response = $this->postJson('/api/practice/lsm-tree-plan', [
            'workload_pattern' => 'read-heavy',
            'write_rate_per_second' => 200,
            'read_miss_ratio' => 'high',
            'compaction_strategy' => 'size-tiered',
            'bloom_filter_enabled' => 'no',
            'schema_expectation' => 'schema-strict',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.architecture_decision_record.status', 'review-before-adoption')
            ->assertJsonPath('data.bloom_filter_plan.enabled', false)
            ->assertJsonPath('data.bloom_filter_plan.action', 'Enable Bloom Filters before optimizing application code for point-read misses.')
            ->assertJsonPath('data.amplification_model.1.name', 'read amplification')
            ->assertJsonPath('data.range_scan_plan.2.area', 'pagination')
            ->assertJsonPath('data.tombstone_ttl_policy.3.policy', 'Schedule large TTL/delete cleanup during measured low-traffic windows.')
            ->assertJsonPath('data.capacity_plan.memory', 'Reserve memory for memtables and block cache, then budget a Bloom Filter experiment if read misses become expensive.')
            ->assertJsonPath('data.cost_model.3.cost', 'operations')
            ->assertJsonPath('data.engine_comparison.3.engine', 'Cache layer')
            ->assertJsonPath('data.decision_rules.3.question', 'Does the system use TTL or frequent deletes?')
            ->assertJsonPath('data.benchmark_plan.2.pass_signal', 'Benchmark proves missing Bloom Filters are still acceptable for the workload.')
            ->assertJsonPath('data.rollout_plan.1.phase', 'benchmark')
            ->assertJsonPath('data.anti_patterns.4.name', 'missing Bloom Filter on high misses')
            ->assertJsonPath('data.incident_runbook.1.phase', 'contain')
            ->assertJsonPath('data.fit_matrix.1.fit', 'risky')
            ->assertJsonPath('data.data_model_review.2.area', 'document shape')
            ->assertJsonPath('data.query_contract.2.allowed', 'no')
            ->assertJsonPath('data.interview_checklist.4', 'Close with measurement: p99 latency, compaction backlog, amplification, disk headroom, and workload-specific benchmarks.')
            ->assertJsonPath('data.failure_modes.3.failure', 'expensive negative lookup')
            ->assertJsonPath('data.workload_score.level', 'needs-careful-review')
            ->assertJsonPath('data.slo_guardrails.3.signal', 'read-miss efficiency')
            ->assertJsonPath('data.tuning_checklist.4', 'Add a Bloom Filter experiment before accepting high point-read miss cost.');
    }

    /**
     * Invalid LSM Tree payloads return validation errors.
     */
    public function test_lsm_tree_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/lsm-tree-plan', [
            'workload_pattern' => 'random',
            'write_rate_per_second' => 0,
            'read_miss_ratio' => 'unknown',
            'compaction_strategy' => 'manual',
            'bloom_filter_enabled' => 'maybe',
            'schema_expectation' => 'none',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'workload_pattern',
                'write_rate_per_second',
                'read_miss_ratio',
                'compaction_strategy',
                'bloom_filter_enabled',
                'schema_expectation',
            ]);
    }
}

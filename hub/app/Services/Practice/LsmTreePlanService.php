<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class LsmTreePlanService
{
    /**
     * Build a NoSQL LSM Tree explanation and tuning plan.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{summary: array{claim: string, why_fast: string, schema_note: string}, architecture_decision_record: array{status: string, decision: string, context: string, consequences: array<int, string>}, write_path: array<int, array{step: string, purpose: string}>, read_path: array<int, array{step: string, purpose: string}>, component_map: array<int, array{name: string, role: string, tradeoff: string}>, compaction_plan: array{strategy: string, good_fit: string, risk: string, tuning: array<int, string>}, bloom_filter_plan: array{enabled: bool, impact: string, action: string}, amplification_model: array<int, array{name: string, symptom: string, control: string}>, range_scan_plan: array<int, array{area: string, risk: string, control: string}>, tombstone_ttl_policy: array<int, array{policy: string, why: string}>, capacity_plan: array{write_ingest: string, memory: string, disk: string, compaction: string}, cost_model: array<int, array{cost: string, driver: string, mitigation: string}>, engine_comparison: array<int, array{engine: string, good_at: string, watch_out: string, interview_signal: string}>, decision_rules: array<int, array{question: string, choose: string, reason: string}>, benchmark_plan: array<int, array{scenario: string, metric: string, pass_signal: string}>, rollout_plan: array<int, array{phase: string, goal: string, exit_signal: string}>, anti_patterns: array<int, array{name: string, risk: string, correction: string}>, incident_runbook: array<int, array{phase: string, action: string, evidence: string}>, fit_matrix: array<int, array{workload: string, fit: string, reason: string}>, data_model_review: array<int, array{area: string, check: string, pass_signal: string}>, query_contract: array<int, array{query_shape: string, allowed: string, reason: string}>, interview_checklist: array<int, string>, failure_modes: array<int, array{failure: string, symptom: string, response: string}>, workload_score: array{level: string, score: int, reasons: array<int, string>}, tuning_checklist: array<int, string>, slo_guardrails: array<int, array{signal: string, target: string, alert: string, action: string}>, observability_plan: array<int, string>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        return [
            'summary' => $this->summaryFor($input),
            'architecture_decision_record' => $this->architectureDecisionRecordFor($input),
            'write_path' => $this->writePath(),
            'read_path' => $this->readPathFor($input),
            'component_map' => $this->componentMap(),
            'compaction_plan' => $this->compactionPlanFor($input),
            'bloom_filter_plan' => $this->bloomFilterPlanFor($input),
            'amplification_model' => $this->amplificationModelFor($input),
            'range_scan_plan' => $this->rangeScanPlanFor($input),
            'tombstone_ttl_policy' => $this->tombstoneTtlPolicyFor($input),
            'capacity_plan' => $this->capacityPlanFor($input),
            'cost_model' => $this->costModelFor($input),
            'engine_comparison' => $this->engineComparisonFor($input),
            'decision_rules' => $this->decisionRulesFor($input),
            'benchmark_plan' => $this->benchmarkPlanFor($input),
            'rollout_plan' => $this->rolloutPlanFor($input),
            'anti_patterns' => $this->antiPatternsFor($input),
            'incident_runbook' => $this->incidentRunbookFor($input),
            'fit_matrix' => $this->fitMatrixFor($input),
            'data_model_review' => $this->dataModelReviewFor($input),
            'query_contract' => $this->queryContractFor($input),
            'interview_checklist' => $this->interviewChecklist(),
            'failure_modes' => $this->failureModesFor($input),
            'workload_score' => $this->workloadScoreFor($input),
            'tuning_checklist' => $this->tuningChecklistFor($input),
            'slo_guardrails' => $this->sloGuardrailsFor($input),
            'observability_plan' => [
                'Track memtable flush latency and flush frequency.',
                'Track compaction queue depth, compaction bytes written, and write amplification.',
                'Track Bloom Filter false-positive rate and point-read disk touches.',
                'Track read amplification by counting SSTables or segments checked per lookup.',
                'Track p95 and p99 write latency during compaction pressure.',
            ],
            'interview_answer' => $this->interviewAnswerFor($input),
            'commands' => [
                'php artisan route:list --path=lsm-tree-plan',
                'php artisan test --filter LsmTreePlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the main misconception correction.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{claim: string, why_fast: string, schema_note: string}
     */
    private function summaryFor(array $input): array
    {
        return [
            'claim' => 'NoSQL is not fast simply because it has no schema.',
            'why_fast' => 'An LSM Tree makes writes fast by accepting writes in memory first, appending immutable segments sequentially, then reorganizing data later through compaction.',
            'schema_note' => $input['schema_expectation'] === 'schema-flexible'
                ? 'Flexible schema can reduce modeling friction, but the storage-engine win comes from write path design.'
                : 'Even strict-schema systems can use LSM-style storage; schema strictness and write-path layout are separate concerns.',
        ];
    }

    /**
     * Return an ADR-style decision for using LSM-backed storage.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{status: string, decision: string, context: string, consequences: array<int, string>}
     */
    private function architectureDecisionRecordFor(array $input): array
    {
        $status = $input['workload_pattern'] === 'write-heavy' || $input['write_rate_per_second'] >= 10000
            ? 'accepted-with-operational-guards'
            : 'review-before-adoption';

        return [
            'status' => $status,
            'decision' => "Use LSM-backed storage only for {$input['workload_pattern']} access patterns that pass benchmark and data-model review.",
            'context' => 'The storage engine can turn random writes into sequential WAL and segment flushes, but reads, deletes, range scans, and compaction must be designed explicitly.',
            'consequences' => [
                'Data model must define partition key, sort key, allowed query shapes, and TTL/delete policy.',
                'Benchmarks must include compaction pressure, read misses, range scans, and delete waves.',
                'Operational dashboards must track amplification, compaction debt, tombstones, disk headroom, and p99 latency.',
                'Analytical scans and broad ad hoc queries need another serving path or projection.',
            ],
        ];
    }

    /**
     * Return the LSM write path.
     *
     * @return array<int, array{step: string, purpose: string}>
     */
    private function writePath(): array
    {
        return [
            [
                'step' => '1. Append to WAL',
                'purpose' => 'Persist the write sequentially so the node can recover after a crash.',
            ],
            [
                'step' => '2. Update memtable',
                'purpose' => 'Keep the latest sorted in-memory view for fast acknowledgement and later flush.',
            ],
            [
                'step' => '3. Flush immutable segment',
                'purpose' => 'When the memtable is full, write a sorted SSTable or segment sequentially to disk.',
            ],
            [
                'step' => '4. Compact segments',
                'purpose' => 'Merge old segments, discard overwritten values or tombstones, and reduce read amplification.',
            ],
        ];
    }

    /**
     * Return the LSM read path.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{step: string, purpose: string}>
     */
    private function readPathFor(array $input): array
    {
        $segments = $input['bloom_filter_enabled'] === 'yes'
            ? 'Use Bloom Filters to skip segments that cannot contain the key.'
            : 'Check more segment indexes because no Bloom Filter can rule out missing keys early.';

        return [
            [
                'step' => '1. Check memtable',
                'purpose' => 'Read the newest value before touching disk.',
            ],
            [
                'step' => '2. Check immutable memtable or recent segment',
                'purpose' => 'Catch recently flushed data while compaction is still pending.',
            ],
            [
                'step' => '3. Probe segment metadata',
                'purpose' => $segments,
            ],
            [
                'step' => '4. Resolve newest version',
                'purpose' => 'Pick the newest value across levels and respect tombstones for deletes.',
            ],
        ];
    }

    /**
     * Return LSM component roles.
     *
     * @return array<int, array{name: string, role: string, tradeoff: string}>
     */
    private function componentMap(): array
    {
        return [
            [
                'name' => 'WAL',
                'role' => 'Sequential durability log before acknowledging writes.',
                'tradeoff' => 'Adds write bytes, but avoids random in-place disk mutation.',
            ],
            [
                'name' => 'memtable',
                'role' => 'Sorted in-memory write buffer.',
                'tradeoff' => 'Fast writes depend on memory sizing and flush pressure.',
            ],
            [
                'name' => 'SSTable or segment',
                'role' => 'Immutable sorted disk file created by sequential flush.',
                'tradeoff' => 'Many segments improve write speed first, then require compaction for read efficiency.',
            ],
            [
                'name' => 'compaction',
                'role' => 'Background merge that removes old versions and reduces segment count.',
                'tradeoff' => 'Improves reads but creates write amplification and can hurt tail latency.',
            ],
            [
                'name' => 'Bloom Filter',
                'role' => 'Probabilistic membership check before touching a segment.',
                'tradeoff' => 'Saves disk reads on misses, but uses memory and can return false positives.',
            ],
        ];
    }

    /**
     * Return compaction guidance.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{strategy: string, good_fit: string, risk: string, tuning: array<int, string>}
     */
    private function compactionPlanFor(array $input): array
    {
        $strategy = $input['compaction_strategy'];

        $plans = [
            'size-tiered' => [
                'good_fit' => 'Good for high write throughput because it merges similarly sized files in batches.',
                'risk' => 'Can leave more overlapping segments, increasing read amplification for point reads.',
            ],
            'leveled' => [
                'good_fit' => 'Good for read-heavy point lookups because levels reduce overlap.',
                'risk' => 'Creates more write amplification and can increase compaction pressure.',
            ],
            'universal' => [
                'good_fit' => 'Good for mixed or bursty workloads where broad file selection can reduce space amplification.',
                'risk' => 'Needs careful tuning because large merges can affect tail latency.',
            ],
        ];

        return [
            'strategy' => $strategy,
            'good_fit' => $plans[$strategy]['good_fit'],
            'risk' => $plans[$strategy]['risk'],
            'tuning' => [
                'Size memtable and flush thresholds so writes do not stall during bursts.',
                'Throttle compaction before it competes too aggressively with foreground reads and writes.',
                'Tune tombstone retention so deletes are removed without breaking consistency windows.',
                'Load test p99 latency while compaction is actively running.',
            ],
        ];
    }

    /**
     * Return Bloom Filter guidance.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{enabled: bool, impact: string, action: string}
     */
    private function bloomFilterPlanFor(array $input): array
    {
        if ($input['bloom_filter_enabled'] === 'yes') {
            return [
                'enabled' => true,
                'impact' => 'Bloom Filters reduce unnecessary segment reads, especially when point lookups miss.',
                'action' => 'Track false-positive rate and allocate enough memory for useful filters.',
            ];
        }

        return [
            'enabled' => false,
            'impact' => 'Missing Bloom Filters make negative lookups more expensive because more segments must be checked.',
            'action' => $input['read_miss_ratio'] === 'high'
                ? 'Enable Bloom Filters before optimizing application code for point-read misses.'
                : 'Document why memory is more valuable elsewhere and watch read amplification.',
        ];
    }

    /**
     * Return the three amplification costs that make LSM tuning concrete.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{name: string, symptom: string, control: string}>
     */
    private function amplificationModelFor(array $input): array
    {
        return [
            [
                'name' => 'write amplification',
                'symptom' => $input['compaction_strategy'] === 'leveled'
                    ? 'Leveled compaction can rewrite data more often to keep reads efficient.'
                    : 'Batch compaction still rewrites data while merging segments and clearing old versions.',
                'control' => 'Tune compaction concurrency, level size, file size, and throttling against p99 write latency.',
            ],
            [
                'name' => 'read amplification',
                'symptom' => $input['bloom_filter_enabled'] === 'yes'
                    ? 'Reads may still touch multiple levels, but Bloom Filters reduce unnecessary segment checks.'
                    : 'Negative point reads can scan more segment metadata because no Bloom Filter rules segments out early.',
                'control' => 'Use Bloom Filters, indexes, leveled compaction, and hot-key caching where the workload proves it.',
            ],
            [
                'name' => 'space amplification',
                'symptom' => 'Old versions, tombstones, and overlapping segments temporarily consume extra disk.',
                'control' => 'Watch disk headroom, tombstone age, compaction backlog, and snapshot retention.',
            ],
        ];
    }

    /**
     * Return range-scan guidance for LSM-backed data models.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{area: string, risk: string, control: string}>
     */
    private function rangeScanPlanFor(array $input): array
    {
        return [
            [
                'area' => 'key design',
                'risk' => 'A poor partition key or sort key turns range scans into many segment and partition reads.',
                'control' => 'Model keys around the access pattern, such as tenant plus time bucket plus entity ID.',
            ],
            [
                'area' => 'segment overlap',
                'risk' => $input['compaction_strategy'] === 'size-tiered'
                    ? 'Size-tiered compaction can keep overlapping segments longer, so range scans may merge more files.'
                    : 'Even with leveled compaction, range scans still pay for multiple levels and tombstone checks.',
                'control' => 'Tune compaction and keep range queries bounded by partition, prefix, and time window.',
            ],
            [
                'area' => 'pagination',
                'risk' => 'Offset-style pagination over a large sorted range can repeatedly scan and discard old rows.',
                'control' => 'Use cursor pagination from the last seen key and enforce page-size limits.',
            ],
            [
                'area' => 'hot range',
                'risk' => 'A single recent time range can receive most writes and reads, creating uneven pressure.',
                'control' => 'Use time buckets, sharding, or adaptive caching for known hot ranges.',
            ],
        ];
    }

    /**
     * Return tombstone and TTL policy guidance.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{policy: string, why: string}>
     */
    private function tombstoneTtlPolicyFor(array $input): array
    {
        return [
            [
                'policy' => 'Treat delete as a write first, not immediate physical removal.',
                'why' => 'LSM systems write tombstones so replicas and later compaction can agree that older values are deleted.',
            ],
            [
                'policy' => 'Align TTL volume with compaction capacity.',
                'why' => 'Large TTL waves can create tombstone storms that increase reads, compaction work, and disk pressure.',
            ],
            [
                'policy' => 'Keep snapshots and backup retention visible to compaction planning.',
                'why' => 'Old snapshots can keep obsolete data and tombstones alive longer than expected.',
            ],
            [
                'policy' => $input['workload_pattern'] === 'write-heavy'
                    ? 'Throttle mass deletes during peak write windows.'
                    : 'Schedule large TTL/delete cleanup during measured low-traffic windows.',
                'why' => 'Delete-heavy periods compete with normal writes and can create compaction debt.',
            ],
        ];
    }

    /**
     * Return a capacity-planning view for the LSM Tree workload.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{write_ingest: string, memory: string, disk: string, compaction: string}
     */
    private function capacityPlanFor(array $input): array
    {
        $writeTier = $input['write_rate_per_second'] >= 10000 ? 'high' : ($input['write_rate_per_second'] >= 1000 ? 'medium' : 'low');

        return [
            'write_ingest' => "Treat {$input['write_rate_per_second']} writes per second as {$writeTier} ingest pressure and validate WAL fsync plus flush throughput under burst load.",
            'memory' => $input['bloom_filter_enabled'] === 'yes'
                ? 'Reserve memory for memtables, block cache, and Bloom Filters; do not let filters starve hot read cache.'
                : 'Reserve memory for memtables and block cache, then budget a Bloom Filter experiment if read misses become expensive.',
            'disk' => 'Plan disk headroom for active data plus WAL, immutable segments, old versions, tombstones, snapshots, and in-progress compaction output.',
            'compaction' => $input['compaction_strategy'] === 'leveled'
                ? 'Budget more compaction write bandwidth because leveled compaction keeps reads predictable by rewriting data more often.'
                : 'Budget burst compaction bandwidth because batch merges can create sudden disk and latency pressure.',
        ];
    }

    /**
     * Return cost drivers that should be planned before choosing LSM-backed storage.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{cost: string, driver: string, mitigation: string}>
     */
    private function costModelFor(array $input): array
    {
        return [
            [
                'cost' => 'memory',
                'driver' => $input['bloom_filter_enabled'] === 'yes'
                    ? 'Memtables, block cache, and Bloom Filters all compete for RAM.'
                    : 'Memtables and block cache need RAM, and disabling Bloom Filters can move cost into disk reads.',
                'mitigation' => 'Budget memory by role and validate cache hit rate plus Bloom Filter false-positive rate under load.',
            ],
            [
                'cost' => 'disk',
                'driver' => 'WAL files, immutable segments, snapshots, tombstones, and compaction output require headroom beyond active data.',
                'mitigation' => 'Alert on disk headroom, space amplification, tombstone count, and compaction backlog before writes stall.',
            ],
            [
                'cost' => 'cpu and io',
                'driver' => $input['compaction_strategy'] === 'leveled'
                    ? 'Leveled compaction spends more CPU and IO to reduce read overlap.'
                    : 'Batch compaction can create bursts of CPU and IO pressure.',
                'mitigation' => 'Throttle compaction, isolate disks where possible, and benchmark foreground p99 during active merges.',
            ],
            [
                'cost' => 'operations',
                'driver' => 'The team must operate compaction, tombstones, range scans, backups, and incident response.',
                'mitigation' => 'Own dashboards, runbooks, benchmark suites, and query-contract review before production rollout.',
            ],
        ];
    }

    /**
     * Return a comparison that separates LSM engines from adjacent storage choices.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{engine: string, good_at: string, watch_out: string, interview_signal: string}>
     */
    private function engineComparisonFor(array $input): array
    {
        return [
            [
                'engine' => 'LSM-backed key-value or wide-column store',
                'good_at' => $input['workload_pattern'] === 'write-heavy'
                    ? 'High-ingest writes, event streams, time-series records, and point lookups with planned keys.'
                    : 'Predictable key-value access when compaction, cache, and Bloom Filters are tuned from workload evidence.',
                'watch_out' => 'Compaction pressure, tombstones, range scans, and read amplification can erase the write-path win.',
                'interview_signal' => 'Choose it when write path and query contract are explicit, not because the product says NoSQL.',
            ],
            [
                'engine' => 'B-tree relational index',
                'good_at' => 'Transactional updates, relational constraints, secondary indexes, and bounded OLTP queries.',
                'watch_out' => 'Random in-place index updates can become expensive under very high write ingest.',
                'interview_signal' => 'Use it when relational integrity and flexible indexed queries matter more than append-heavy ingest.',
            ],
            [
                'engine' => 'Search or analytics projection',
                'good_at' => 'Full-text search, faceting, ranking, aggregations, and broad analytical scans.',
                'watch_out' => 'It needs sync, replay, permission filtering, and freshness controls from the source of truth.',
                'interview_signal' => 'Route broad scans and ranking away from the LSM primary store instead of forcing one engine to do everything.',
            ],
            [
                'engine' => 'Cache layer',
                'good_at' => 'Hot-key reads, computed responses, and shielding repeated misses from the database.',
                'watch_out' => 'Cache invalidation and stampede control can hide the real storage bottleneck if not measured.',
                'interview_signal' => 'Use cache as a read-path control, not as proof that the storage engine is correctly modeled.',
            ],
        ];
    }

    /**
     * Return decision rules for choosing LSM tuning controls.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{question: string, choose: string, reason: string}>
     */
    private function decisionRulesFor(array $input): array
    {
        return [
            [
                'question' => 'Is the workload write-heavy with high ingest?',
                'choose' => $input['workload_pattern'] === 'write-heavy'
                    ? 'Prefer larger memtable capacity, sequential flush throughput, and compaction throttling.'
                    : 'Keep write buffering balanced with read-path needs.',
                'reason' => 'LSM speed appears when writes can be buffered and flushed without constant stalls.',
            ],
            [
                'question' => 'Are point-read misses common?',
                'choose' => $input['read_miss_ratio'] === 'high'
                    ? 'Use Bloom Filters and measure false-positive rate.'
                    : 'Keep Bloom Filters sized reasonably, but prioritize block cache and indexes if misses are rare.',
                'reason' => 'A Bloom Filter is most valuable when many lookups ask for keys that are absent.',
            ],
            [
                'question' => 'Are range scans product-critical?',
                'choose' => 'Design partition and sort keys first, then tune compaction for bounded scans.',
                'reason' => 'Range scans are shaped more by key layout and segment overlap than by the word NoSQL.',
            ],
            [
                'question' => 'Does the system use TTL or frequent deletes?',
                'choose' => 'Model tombstone volume, snapshot retention, and compaction debt before accepting the delete pattern.',
                'reason' => 'Deletes are cheap writes initially but can become expensive reads and compaction work later.',
            ],
        ];
    }

    /**
     * Return benchmark scenarios that validate the LSM Tree tuning plan.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{scenario: string, metric: string, pass_signal: string}>
     */
    private function benchmarkPlanFor(array $input): array
    {
        return [
            [
                'scenario' => 'steady write ingest',
                'metric' => 'writes_per_second, WAL fsync latency, memtable flush latency, and p99 write latency',
                'pass_signal' => "Sustains {$input['write_rate_per_second']} writes per second without flush stalls or unbounded compaction backlog.",
            ],
            [
                'scenario' => 'compaction under foreground traffic',
                'metric' => 'compaction_queue_depth, bytes_compacted_per_second, write_amplification, and p99 read/write latency',
                'pass_signal' => 'Foreground p99 remains within the service objective while compaction actively merges segments.',
            ],
            [
                'scenario' => 'point-read misses',
                'metric' => 'segments_checked_per_miss, Bloom Filter false-positive rate, and p99 point-read latency',
                'pass_signal' => $input['bloom_filter_enabled'] === 'yes'
                    ? 'Misses skip most irrelevant segments and false-positive rate stays within the memory budget.'
                    : 'Benchmark proves missing Bloom Filters are still acceptable for the workload.',
            ],
            [
                'scenario' => 'bounded range scan',
                'metric' => 'rows_scanned, segments_merged, tombstones_seen, and page latency',
                'pass_signal' => 'Cursor pagination returns bounded pages without scanning unrelated partitions or stale ranges.',
            ],
            [
                'scenario' => 'TTL or delete wave',
                'metric' => 'tombstone_count, disk_used, compaction_debt, and read latency after expiry',
                'pass_signal' => 'Deletes do not create lasting tombstone buildup or disk growth after compaction catches up.',
            ],
        ];
    }

    /**
     * Return rollout phases for introducing or tuning LSM-backed storage.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{phase: string, goal: string, exit_signal: string}>
     */
    private function rolloutPlanFor(array $input): array
    {
        return [
            [
                'phase' => 'model',
                'goal' => 'Lock the partition key, sort key, allowed query shapes, TTL/delete policy, and schema validation boundary.',
                'exit_signal' => 'Data model review and query contract both reject broad scans and unbounded pagination.',
            ],
            [
                'phase' => 'benchmark',
                'goal' => "Replay {$input['write_rate_per_second']} writes per second with compaction, point-read misses, bounded ranges, and delete waves active.",
                'exit_signal' => 'p99 latency, compaction backlog, amplification, disk headroom, and Bloom Filter behavior stay inside targets.',
            ],
            [
                'phase' => 'dual-read',
                'goal' => 'Compare primary-store results with the new LSM path before it owns serving traffic.',
                'exit_signal' => 'Mismatch rate, lag, and missing-key behavior are understood and operational alerts are ready.',
            ],
            [
                'phase' => 'ramp',
                'goal' => $input['workload_pattern'] === 'write-heavy'
                    ? 'Increase write traffic gradually while watching flush stalls and compaction debt.'
                    : 'Increase read traffic gradually while watching segments checked per lookup and range-scan page latency.',
                'exit_signal' => 'Rollback threshold, dashboard owner, and incident runbook are confirmed before full traffic.',
            ],
        ];
    }

    /**
     * Return common LSM Tree anti-patterns and the safer correction.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{name: string, risk: string, correction: string}>
     */
    private function antiPatternsFor(array $input): array
    {
        $patterns = [
            [
                'name' => 'schema myth',
                'risk' => 'Saying NoSQL is fast because it has no schema hides the real storage-engine and workload tradeoff.',
                'correction' => 'Explain WAL, memtable, immutable segment flush, compaction, and Bloom Filters.',
            ],
            [
                'name' => 'ignoring compaction',
                'risk' => 'Write benchmarks look good until background compaction competes with real traffic.',
                'correction' => 'Benchmark p99 latency while compaction is active and alert on compaction backlog.',
            ],
            [
                'name' => 'unbounded range query',
                'risk' => 'A range scan without bounded partition, prefix, or cursor can read many segments and tombstones.',
                'correction' => 'Design keys around access patterns and require cursor pagination with page-size limits.',
            ],
            [
                'name' => 'delete storm',
                'risk' => 'Large TTL or delete waves create tombstones that make later reads and compaction expensive.',
                'correction' => 'Spread expiry, watch tombstone count, and align delete volume with compaction capacity.',
            ],
        ];

        if ($input['bloom_filter_enabled'] === 'no') {
            $patterns[] = [
                'name' => 'missing Bloom Filter on high misses',
                'risk' => 'Negative lookups may touch too many segments and inflate p99 point-read latency.',
                'correction' => 'Enable Bloom Filters or prove with benchmark data that miss cost is acceptable.',
            ];
        }

        return $patterns;
    }

    /**
     * Return an incident runbook for LSM latency or disk-pressure events.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{phase: string, action: string, evidence: string}>
     */
    private function incidentRunbookFor(array $input): array
    {
        return [
            [
                'phase' => 'triage',
                'action' => 'Separate foreground traffic symptoms from background compaction symptoms.',
                'evidence' => 'p99 read/write latency, compaction queue depth, memtable flush latency, disk utilization',
            ],
            [
                'phase' => 'contain',
                'action' => $input['workload_pattern'] === 'write-heavy'
                    ? 'Apply write backpressure or reduce ingest burst size before flush stalls cascade.'
                    : 'Reduce expensive reads with bounded queries, cache hot keys, or pause broad scans.',
                'evidence' => 'ingest rate, rejected or throttled writes, segments checked per read, hot range metrics',
            ],
            [
                'phase' => 'recover',
                'action' => 'Let compaction catch up with controlled throughput and verify disk headroom before restoring full traffic.',
                'evidence' => 'compaction debt trending down, disk headroom stable, segment count normalizing',
            ],
            [
                'phase' => 'harden',
                'action' => 'Add a benchmark case and alert that would have detected this pressure earlier.',
                'evidence' => 'new benchmark result, alert threshold, runbook owner, follow-up tuning change',
            ],
        ];
    }

    /**
     * Return a fit matrix that contrasts good and risky LSM workloads.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{workload: string, fit: string, reason: string}>
     */
    private function fitMatrixFor(array $input): array
    {
        return [
            [
                'workload' => 'high ingest event or time-series writes',
                'fit' => 'strong',
                'reason' => 'Sequential WAL and segment flushes absorb writes efficiently when compaction bandwidth is planned.',
            ],
            [
                'workload' => 'point lookups with many misses',
                'fit' => $input['bloom_filter_enabled'] === 'yes' ? 'conditional' : 'risky',
                'reason' => $input['bloom_filter_enabled'] === 'yes'
                    ? 'Bloom Filters can skip irrelevant segments, but false-positive rate still needs measurement.'
                    : 'Without Bloom Filters, negative lookups can touch too many segments.',
            ],
            [
                'workload' => 'large unbounded analytical scans',
                'fit' => 'risky',
                'reason' => 'LSM layouts are not a substitute for analytical storage or bounded access patterns.',
            ],
            [
                'workload' => 'frequent TTL and delete-heavy data',
                'fit' => 'conditional',
                'reason' => 'Deletes are tombstones first, so the workload depends on compaction capacity and retention policy.',
            ],
        ];
    }

    /**
     * Return data-model review checks before accepting an LSM-backed design.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{area: string, check: string, pass_signal: string}>
     */
    private function dataModelReviewFor(array $input): array
    {
        return [
            [
                'area' => 'partition key',
                'check' => 'Does the partition key spread writes and reads instead of concentrating all recent traffic on one partition?',
                'pass_signal' => 'Hot partition metrics stay bounded during peak ingest and recent-range reads.',
            ],
            [
                'area' => 'sort key',
                'check' => 'Does the sort key support the exact range and pagination shape the product needs?',
                'pass_signal' => 'Range scans are bounded by partition, prefix, and cursor rather than broad table scans.',
            ],
            [
                'area' => 'document shape',
                'check' => $input['schema_expectation'] === 'schema-flexible'
                    ? 'Is flexible document shape validated at the ingestion boundary?'
                    : 'Does strict schema still match the LSM access pattern instead of only relational normalization?',
                'pass_signal' => 'Invalid or oversized records are rejected before they become compaction and read-path cost.',
            ],
            [
                'area' => 'delete and TTL fields',
                'check' => 'Are expiry and delete fields designed to avoid synchronized tombstone waves?',
                'pass_signal' => 'TTL distribution, tombstone count, and compaction debt are measured during load tests.',
            ],
        ];
    }

    /**
     * Return query-shape rules for an LSM-backed feature.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{query_shape: string, allowed: string, reason: string}>
     */
    private function queryContractFor(array $input): array
    {
        return [
            [
                'query_shape' => 'point lookup by full key',
                'allowed' => 'yes',
                'reason' => $input['bloom_filter_enabled'] === 'yes'
                    ? 'Full-key lookups pair well with memtable checks, segment indexes, and Bloom Filters.'
                    : 'Full-key lookups are acceptable, but misses should be measured because Bloom Filters are disabled.',
            ],
            [
                'query_shape' => 'bounded prefix or time-window range',
                'allowed' => 'yes-with-limits',
                'reason' => 'Bounded ranges can work when the partition key, sort key, page size, and cursor are explicit.',
            ],
            [
                'query_shape' => 'offset pagination over large ranges',
                'allowed' => 'no',
                'reason' => 'Offset pagination repeatedly scans and discards rows, increasing segment and tombstone work.',
            ],
            [
                'query_shape' => 'ad hoc analytical scan',
                'allowed' => 'no',
                'reason' => 'Use an analytical store, search engine, or precomputed projection instead of forcing LSM storage into broad scans.',
            ],
        ];
    }

    /**
     * Return a concise checklist for interview answers.
     *
     * @return array<int, string>
     */
    private function interviewChecklist(): array
    {
        return [
            'Start by rejecting the schema myth: schema flexibility is not the main speed mechanism.',
            'Describe the write path: WAL, memtable, immutable sorted segment, then background compaction.',
            'Name the read-side helpers and costs: Bloom Filters, segment indexes, read amplification, and tombstones.',
            'State the tradeoff: faster writes now, operational cost later through compaction and amplification.',
            'Close with measurement: p99 latency, compaction backlog, amplification, disk headroom, and workload-specific benchmarks.',
        ];
    }

    /**
     * Return common operational failure modes in LSM-backed systems.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{failure: string, symptom: string, response: string}>
     */
    private function failureModesFor(array $input): array
    {
        $modes = [
            [
                'failure' => 'memtable flush stall',
                'symptom' => 'Write latency spikes because memory fills faster than immutable segments can be flushed.',
                'response' => 'Increase flush capacity, tune memtable size, add write backpressure, and check disk throughput.',
            ],
            [
                'failure' => 'compaction backlog',
                'symptom' => 'Segment count grows, read amplification rises, and disk usage keeps increasing.',
                'response' => 'Raise compaction throughput carefully, rebalance workload, and alert on compaction queue depth.',
            ],
            [
                'failure' => 'tombstone buildup',
                'symptom' => 'Deletes look cheap at write time but reads and compaction become expensive later.',
                'response' => 'Review TTL/delete pattern, snapshot retention, and compaction windows before increasing delete volume.',
            ],
        ];

        if ($input['bloom_filter_enabled'] === 'no') {
            $modes[] = [
                'failure' => 'expensive negative lookup',
                'symptom' => 'Read misses touch too many segments and inflate p99 point-read latency.',
                'response' => 'Enable Bloom Filters or redesign the query path so high-miss lookups are cached or avoided.',
            ];
        }

        return $modes;
    }

    /**
     * Score how strongly the submitted workload fits LSM-style storage.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array{level: string, score: int, reasons: array<int, string>}
     */
    private function workloadScoreFor(array $input): array
    {
        $score = 40;
        $reasons = ['LSM Tree trades cheap sequential writes for background compaction and possible read amplification.'];

        if ($input['workload_pattern'] === 'write-heavy') {
            $score += 25;
            $reasons[] = 'Write-heavy workloads benefit from memtable buffering and sequential segment flushes.';
        } elseif ($input['workload_pattern'] === 'read-heavy') {
            $score -= 10;
            $reasons[] = 'Read-heavy workloads need stronger compaction and Bloom Filter tuning.';
        }

        if ($input['write_rate_per_second'] >= 10000) {
            $score += 20;
            $reasons[] = 'High write rate makes avoiding random in-place disk updates more valuable.';
        }

        if ($input['read_miss_ratio'] === 'high' && $input['bloom_filter_enabled'] === 'yes') {
            $score += 10;
            $reasons[] = 'Bloom Filters help high miss-rate point reads skip irrelevant segments.';
        } elseif ($input['read_miss_ratio'] === 'high') {
            $score -= 15;
            $reasons[] = 'High read-miss ratio without Bloom Filters can create avoidable disk work.';
        }

        $score = max(0, min(100, $score));

        return [
            'level' => $score >= 70 ? 'strong-fit' : ($score >= 45 ? 'conditional-fit' : 'needs-careful-review'),
            'score' => $score,
            'reasons' => $reasons,
        ];
    }

    /**
     * Return a practical tuning checklist.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, string>
     */
    private function tuningChecklistFor(array $input): array
    {
        $items = [
            'Explain speed through write path design, not through the phrase schemaless.',
            'Measure write amplification, read amplification, and space amplification separately.',
            'Choose compaction strategy from workload shape instead of copying a default blindly.',
            'Protect p99 latency during compaction with throttling and load tests.',
        ];

        if ($input['bloom_filter_enabled'] === 'no') {
            $items[] = 'Add a Bloom Filter experiment before accepting high point-read miss cost.';
        }

        if ($input['schema_expectation'] === 'schema-flexible') {
            $items[] = 'Keep document shape validation at the application or ingestion boundary even when storage is flexible.';
        }

        return $items;
    }

    /**
     * Return SLO guardrails that turn the plan into release and alert criteria.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     * @return array<int, array{signal: string, target: string, alert: string, action: string}>
     */
    private function sloGuardrailsFor(array $input): array
    {
        return [
            [
                'signal' => 'foreground p99 latency',
                'target' => $input['workload_pattern'] === 'write-heavy'
                    ? 'Write p99 remains within the product SLO while memtable flush and compaction are active.'
                    : 'Read p99 remains within the product SLO while point misses and bounded ranges run together.',
                'alert' => 'Page when p99 breaches the SLO for two consecutive windows during normal traffic.',
                'action' => 'Throttle ingest, reduce broad reads, or pause rollout until compaction and cache metrics stabilize.',
            ],
            [
                'signal' => 'compaction debt',
                'target' => 'Compaction backlog trends down after bursts and does not grow across the full benchmark run.',
                'alert' => 'Alert when queue depth, pending bytes, or segment count grows for more than one compaction window.',
                'action' => 'Increase compaction capacity carefully, lower ingest burst size, or change compaction strategy after benchmark evidence.',
            ],
            [
                'signal' => 'disk headroom',
                'target' => 'Free disk covers active data plus WAL, snapshots, tombstones, and compaction output with agreed safety margin.',
                'alert' => 'Alert before space amplification can push the node below the safety margin.',
                'action' => 'Add capacity, prune expired data safely, reduce snapshot retention, or slow writes before stalls occur.',
            ],
            [
                'signal' => 'read-miss efficiency',
                'target' => $input['bloom_filter_enabled'] === 'yes'
                    ? 'Bloom Filter false-positive rate and segments checked per miss stay inside benchmark targets.'
                    : 'Segments checked per miss remains acceptable without Bloom Filters under high-miss tests.',
                'alert' => 'Alert when miss latency or segment checks climb after compaction, TTL waves, or cache churn.',
                'action' => 'Tune Bloom Filter memory, add a miss cache, redesign keys, or reject high-miss query shapes.',
            ],
            [
                'signal' => 'tombstone pressure',
                'target' => 'Tombstone count and age fall after TTL/delete waves instead of accumulating across releases.',
                'alert' => 'Alert when tombstone age, tombstones per read, or delete-wave latency exceeds the tested envelope.',
                'action' => 'Spread expiry, review snapshot retention, and schedule delete-heavy jobs within measured compaction capacity.',
            ],
        ];
    }

    /**
     * Return an interview-ready answer.
     *
     * @param  array{workload_pattern: string, write_rate_per_second: int, read_miss_ratio: string, compaction_strategy: string, bloom_filter_enabled: string, schema_expectation: string}  $input
     */
    private function interviewAnswerFor(array $input): string
    {
        return "NoSQL is often fast for writes not because it lacks schema, but because many engines use an LSM Tree write path. A write goes to a WAL and memtable, then flushes immutable sorted segments sequentially. Later, compaction merges segments, removes old versions and tombstones, and Bloom Filters help point reads skip files that cannot contain the key. The tradeoff is read amplification, write amplification during compaction, and operational tuning. For {$input['workload_pattern']} workloads, I would tune {$input['compaction_strategy']} compaction and Bloom Filters based on measured p99 latency, read misses, and compaction pressure.";
    }
}

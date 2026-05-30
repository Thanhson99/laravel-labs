<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class SiemElkPlanService
{
    /**
     * Build a SIEM and ELK implementation plan for security log analysis practice.
     *
     * @param  array{environment_name: string, log_sources: string, detection_goal: string, retention_need: string, alert_maturity: string, data_sensitivity: string, team_size: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $environment = Str::studly($input['environment_name']);
        $riskLevel = $this->riskLevel($input);
        $recommendation = $this->recommendation($input, $riskLevel);
        $readinessScore = $this->readinessScore($input);
        $logstashPipeline = $this->logstashPipelineExample($input);
        $detectionRuleYaml = $this->detectionRuleYaml($input);
        $indexLifecyclePolicy = $this->indexLifecyclePolicy($input);
        $incidentEvidencePacketMarkdown = $this->incidentEvidencePacketMarkdown($environment, $input, $riskLevel, $readinessScore);
        $artifactBundle = $this->artifactBundle($input, $logstashPipeline, $detectionRuleYaml, $indexLifecyclePolicy, $incidentEvidencePacketMarkdown);
        $artifactManifest = $this->artifactManifest($input, $artifactBundle);

        return [
            'environment' => $environment,
            'risk_level' => $riskLevel,
            'readiness_score' => $readinessScore,
            'recommendation' => $recommendation,
            'executive_summary' => [
                'headline' => "{$riskLevel} SIEM readiness for {$input['log_sources']} focused on {$input['detection_goal']}.",
                'decision' => $recommendation,
                'first_action' => $this->firstAction($input),
                'why_it_matters' => 'SIEM is useful only when logs become searchable evidence, correlated alerts, and repeatable incident response, not just attractive dashboards.',
            ],
            'elk_roles' => $this->elkRoles(),
            'pipeline' => $this->pipeline($input),
            'field_contract' => $this->fieldContract($input),
            'detection_rules' => $this->detectionRules($input),
            'alert_policy' => $this->alertPolicy($input),
            'retention_policy' => $this->retentionPolicy($input),
            'privacy_controls' => $this->privacyControls($input),
            'dashboard_plan' => $this->dashboardPlan($input),
            'saved_queries' => $this->savedQueries($input),
            'sample_events' => $this->sampleEvents($input),
            'parser_tests' => $this->parserTests($input),
            'detection_as_code' => $this->detectionAsCode($input),
            'logstash_pipeline_example' => $logstashPipeline,
            'detection_rule_yaml' => $detectionRuleYaml,
            'index_lifecycle_policy' => $indexLifecyclePolicy,
            'dashboard_panels' => $this->dashboardPanels($input),
            'rollout_plan' => $this->rolloutPlan($input),
            'capacity_plan' => $this->capacityPlan($input),
            'correlation_matrix' => $this->correlationMatrix($input),
            'access_model' => $this->accessModel($input),
            'threat_model' => $this->threatModel($input),
            'false_positive_workflow' => $this->falsePositiveWorkflow($input),
            'maturity_roadmap' => $this->maturityRoadmap($input),
            'decommission_plan' => $this->decommissionPlan($input),
            'failure_modes' => $this->failureModes($input),
            'runbook' => $this->runbook($input),
            'review_checklist' => $this->reviewChecklist(),
            'interview_answer' => $this->interviewAnswer($input, $riskLevel),
            'implementation_prompt' => $this->implementationPrompt($input),
            'incident_evidence_packet_markdown' => $incidentEvidencePacketMarkdown,
            'artifact_bundle' => $artifactBundle,
            'artifact_manifest' => $artifactManifest,
            'promotion_gate' => $this->promotionGate($input, $readinessScore, $artifactManifest),
            'security_slo' => $this->securitySlo($input),
            'tabletop_drill' => $this->tabletopDrill($input),
            'post_incident_feedback_loop' => $this->postIncidentFeedbackLoop($input),
            'executive_brief' => $this->executiveBrief($input, $riskLevel, $readinessScore),
            'operating_cadence' => $this->operatingCadence($input),
            'interview_rubric' => $this->interviewRubric($input),
            'data_quality_scorecard' => $this->dataQualityScorecard($input),
            'cost_control_plan' => $this->costControlPlan($input),
            'source_onboarding_checklist' => $this->sourceOnboardingChecklist($input),
            'commands' => [
                'php artisan test --filter SiemElkPlan',
                'php artisan route:list --path=siem-elk-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return an operational readiness level for the SIEM scenario.
     *
     * @param  array{retention_need: string, alert_maturity: string, data_sensitivity: string, team_size: string}  $input
     */
    private function riskLevel(array $input): string
    {
        $score = 0;
        $score += $input['alert_maturity'] === 'low' ? 3 : 0;
        $score += $input['retention_need'] === 'long' ? 2 : 0;
        $score += $input['data_sensitivity'] === 'high' ? 2 : 0;
        $score += $input['team_size'] === 'solo' ? 1 : 0;

        return match (true) {
            $score >= 6 => 'high',
            $score >= 3 => 'medium',
            default => 'controlled',
        };
    }

    /**
     * Return the recommended implementation posture.
     *
     * @param  array{alert_maturity: string, team_size: string, data_sensitivity: string}  $input
     */
    private function recommendation(array $input, string $riskLevel): string
    {
        if ($input['team_size'] === 'solo' && $riskLevel === 'high') {
            return 'Start with managed SIEM or a minimal ELK deployment, then add custom correlation rules only after parsing, retention, and alert ownership are reliable.';
        }

        if ($input['alert_maturity'] === 'high') {
            return 'Build an ELK-backed SIEM pipeline with detection-as-code, reviewed alert thresholds, index lifecycle policy, and incident evidence exports.';
        }

        return 'Use ELK as the searchable evidence layer first: ship logs, normalize fields, create a few high-signal detections, and avoid alert noise until ownership is clear.';
    }

    /**
     * Return a readiness score with concrete blockers and next actions.
     *
     * @param  array{retention_need: string, alert_maturity: string, data_sensitivity: string, team_size: string}  $input
     * @return array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}
     */
    private function readinessScore(array $input): array
    {
        $score = 100;
        $blockers = [];
        $nextActions = [];

        if ($input['alert_maturity'] === 'low') {
            $score -= 30;
            $blockers[] = 'Alert ownership and noise-control rules are not mature yet.';
            $nextActions[] = 'Start with three high-signal alerts and require owner, severity, query, dedup window, and runbook.';
        }

        if ($input['retention_need'] === 'long') {
            $score -= 15;
            $nextActions[] = 'Define ILM hot, warm, archive, and delete windows before ingest volume grows.';
        }

        if ($input['data_sensitivity'] === 'high') {
            $score -= 20;
            $blockers[] = 'Sensitive data requires redaction and access controls before broad indexing.';
            $nextActions[] = 'Mask secrets, tokens, authorization headers, and PII before data reaches Elasticsearch.';
        }

        if ($input['team_size'] === 'solo') {
            $score -= 15;
            $nextActions[] = 'Avoid custom SIEM complexity until parsing, retention, backup, and alert handoff are repeatable.';
        }

        $score = max(0, $score);

        if ($nextActions === []) {
            $nextActions[] = 'Promote detections through reviewed pull requests, parser fixtures, and post-incident feedback.';
        }

        return [
            'score' => $score,
            'label' => match (true) {
                $score >= 85 => 'ready-for-reviewed-rules',
                $score >= 65 => 'needs-hardening',
                $score >= 40 => 'pilot-only',
                default => 'blocked',
            },
            'blockers' => $blockers,
            'next_actions' => $nextActions,
        ];
    }

    /**
     * Return the first implementation action.
     *
     * @param  array{log_sources: string, detection_goal: string}  $input
     */
    private function firstAction(array $input): string
    {
        return "Inventory {$input['log_sources']} logs and define the fields needed to detect {$input['detection_goal']} before tuning dashboards.";
    }

    /**
     * Return ELK component responsibilities.
     *
     * @return array<int, array{component: string, responsibility: string, mistake_to_avoid: string}>
     */
    private function elkRoles(): array
    {
        return [
            [
                'component' => 'Elastic Agent or Beats',
                'responsibility' => 'Ship host, application, container, cloud, and network events into the pipeline.',
                'mistake_to_avoid' => 'Installing shippers without tagging source, environment, owner, and parser version.',
            ],
            [
                'component' => 'Logstash',
                'responsibility' => 'Parse, normalize, enrich, drop noise, redact sensitive fields, and route events.',
                'mistake_to_avoid' => 'Treating parsing as optional; bad parsing makes correlation rules unreliable.',
            ],
            [
                'component' => 'Elasticsearch',
                'responsibility' => 'Index events for search, dashboards, correlation, retention, and incident evidence.',
                'mistake_to_avoid' => 'Keeping everything hot forever and letting storage cost or shard count explode.',
            ],
            [
                'component' => 'Kibana',
                'responsibility' => 'Provide saved searches, dashboards, alert rules, investigation views, and evidence export.',
                'mistake_to_avoid' => 'Using dashboards as a substitute for alert ownership and runbooks.',
            ],
        ];
    }

    /**
     * Return the recommended SIEM data flow.
     *
     * @param  array{log_sources: string}  $input
     * @return array<int, array{step: string, output: string}>
     */
    private function pipeline(array $input): array
    {
        return [
            ['step' => 'collect', 'output' => "Ship {$input['log_sources']} logs through Elastic Agent, Beats, syslog, or cloud export."],
            ['step' => 'normalize', 'output' => 'Convert timestamps, users, IPs, event actions, outcomes, severity, and request IDs into a shared field contract.'],
            ['step' => 'enrich', 'output' => 'Add environment, service owner, geo/IP reputation when allowed, cloud account, host role, and deployment version.'],
            ['step' => 'store', 'output' => 'Index events with an ILM policy that separates hot search, warm investigation, and delete/archive windows.'],
            ['step' => 'detect', 'output' => 'Run a small set of high-signal correlation rules before adding noisy behavior analytics.'],
            ['step' => 'respond', 'output' => 'Attach each alert to owner, severity, runbook, evidence query, and escalation path.'],
        ];
    }

    /**
     * Return normalized fields every event should try to provide.
     *
     * @param  array{data_sensitivity: string}  $input
     * @return array<int, string>
     */
    private function fieldContract(array $input): array
    {
        $fields = [
            '@timestamp',
            'event.dataset',
            'event.action',
            'event.outcome',
            'log.level',
            'service.name',
            'host.name',
            'user.id',
            'source.ip',
            'destination.ip',
            'http.request.method',
            'http.response.status_code',
            'trace.id',
            'cloud.account.id',
            'tags',
        ];

        if ($input['data_sensitivity'] !== 'low') {
            $fields[] = 'pii.redacted';
            $fields[] = 'access.scope';
        }

        return $fields;
    }

    /**
     * Return starter detections tied to the selected goal.
     *
     * @param  array{detection_goal: string, log_sources: string}  $input
     * @return array<int, array{name: string, signal: string, response: string}>
     */
    private function detectionRules(array $input): array
    {
        $base = [
            [
                'name' => 'auth.bruteforce.window',
                'signal' => 'Many failed logins for one user or source IP inside a short window.',
                'response' => 'Check user, IP, MFA status, recent password changes, and block or challenge if confirmed.',
            ],
            [
                'name' => 'privilege.change.unexpected',
                'signal' => 'Admin role, IAM policy, or production permission changed outside approved deployment or ticket window.',
                'response' => 'Validate approver, rollback unauthorized permission, and preserve audit evidence.',
            ],
            [
                'name' => 'web.attack.pattern',
                'signal' => 'Repeated 401, 403, 404, SQLi, path traversal, or unusual user-agent patterns across routes.',
                'response' => 'Correlate WAF/proxy logs with application logs before blocking broad traffic.',
            ],
        ];

        $base[] = [
            'name' => "goal.{$input['detection_goal']}.evidence",
            'signal' => "Events from {$input['log_sources']} contain enough user, host, IP, action, outcome, and timestamp context to investigate {$input['detection_goal']}.",
            'response' => 'Open a saved Kibana investigation view and attach evidence to the incident record.',
        ];

        return $base;
    }

    /**
     * Return alert ownership and noise-control policy.
     *
     * @param  array{alert_maturity: string}  $input
     * @return array<string, mixed>
     */
    private function alertPolicy(array $input): array
    {
        return [
            'mode' => $input['alert_maturity'] === 'low' ? 'quiet-start' : 'reviewed-alerting',
            'rules' => [
                'Every alert has severity, owner, runbook, dedup window, sample query, and false-positive notes.',
                'Page humans only on actionable alerts; route weak signals to triage queues or dashboards.',
                'Review noisy rules weekly until the false-positive rate is understood.',
            ],
        ];
    }

    /**
     * Return storage and retention guidance.
     *
     * @param  array{retention_need: string}  $input
     * @return array{hot: string, warm: string, archive_or_delete: string}
     */
    private function retentionPolicy(array $input): array
    {
        return match ($input['retention_need']) {
            'long' => [
                'hot' => 'Keep 14-30 days searchable for active investigations.',
                'warm' => 'Keep 90-180 days lower-cost for audit and slow investigations.',
                'archive_or_delete' => 'Archive or delete after compliance window with documented legal/security approval.',
            ],
            'medium' => [
                'hot' => 'Keep 7-14 days searchable.',
                'warm' => 'Keep 30-90 days lower-cost.',
                'archive_or_delete' => 'Delete or archive after the agreed investigation window.',
            ],
            default => [
                'hot' => 'Keep 3-7 days searchable while validating signal quality.',
                'warm' => 'Keep a short warm window only if incidents require it.',
                'archive_or_delete' => 'Delete aggressively until the team proves which logs are useful.',
            ],
        };
    }

    /**
     * Return privacy controls for security log handling.
     *
     * @param  array{data_sensitivity: string}  $input
     * @return array<int, string>
     */
    private function privacyControls(array $input): array
    {
        $controls = [
            'Apply role-based access to dashboards, raw logs, alert history, and exported evidence.',
            'Keep audit logs for searches and dashboard access, especially for security investigations.',
            'Mask secrets, tokens, passwords, authorization headers, and session identifiers before indexing.',
        ];

        if ($input['data_sensitivity'] !== 'low') {
            $controls[] = 'Redact or tokenize PII fields and document who can temporarily access raw evidence.';
        }

        return $controls;
    }

    /**
     * Return dashboards that support incident response.
     *
     * @param  array{detection_goal: string}  $input
     * @return array<int, array{name: string, questions: array<int, string>}>
     */
    private function dashboardPlan(array $input): array
    {
        return [
            [
                'name' => 'security overview',
                'questions' => ['Which services are producing high severity events?', 'Which users, IPs, and hosts repeat across alerts?'],
            ],
            [
                'name' => "{$input['detection_goal']} investigation",
                'questions' => ['What happened first?', 'Which identity and source changed?', 'What evidence proves impact?'],
            ],
            [
                'name' => 'pipeline health',
                'questions' => ['Are shippers delayed?', 'Are parsers failing?', 'Is Elasticsearch ingest or storage saturated?'],
            ],
        ];
    }

    /**
     * Return saved Kibana-style investigation queries.
     *
     * @param  array{detection_goal: string, log_sources: string}  $input
     * @return array<int, array{name: string, query: string, purpose: string}>
     */
    private function savedQueries(array $input): array
    {
        return [
            [
                'name' => 'failed-login-burst',
                'query' => 'event.action:login AND event.outcome:failure AND @timestamp >= now-15m',
                'purpose' => 'Find brute-force or credential-stuffing patterns before paging.',
            ],
            [
                'name' => 'privilege-change-window',
                'query' => 'event.action:(role_change OR policy_update OR permission_grant) AND event.outcome:success',
                'purpose' => 'Review account, actor, approver, and change window for privilege events.',
            ],
            [
                'name' => "{$input['detection_goal']}-evidence",
                'query' => "event.dataset:{$input['log_sources']} AND tags:security",
                'purpose' => 'Open an investigation view already scoped to the selected source and detection goal.',
            ],
        ];
    }

    /**
     * Return compact sample events for parser and detection tests.
     *
     * @param  array{environment_name: string, log_sources: string, detection_goal: string}  $input
     * @return array<int, array<string, string>>
     */
    private function sampleEvents(array $input): array
    {
        return [
            [
                '@timestamp' => '2026-05-28T09:15:00Z',
                'event.dataset' => $input['log_sources'],
                'event.action' => 'login',
                'event.outcome' => 'failure',
                'service.name' => Str::slug($input['environment_name']),
                'user.id' => 'user-142',
                'source.ip' => '203.0.113.42',
                'tags' => 'security,parser-fixture',
            ],
            [
                '@timestamp' => '2026-05-28T09:18:00Z',
                'event.dataset' => $input['log_sources'],
                'event.action' => $input['detection_goal'],
                'event.outcome' => 'success',
                'service.name' => Str::slug($input['environment_name']),
                'user.id' => 'admin-7',
                'source.ip' => '198.51.100.10',
                'tags' => 'security,true-positive',
            ],
        ];
    }

    /**
     * Return parser test cases that keep field normalization honest.
     *
     * @param  array{data_sensitivity: string}  $input
     * @return array<int, array{name: string, input: string, expected: array<int, string>}>
     */
    private function parserTests(array $input): array
    {
        $expected = ['@timestamp', 'event.action', 'event.outcome', 'user.id', 'source.ip'];

        if ($input['data_sensitivity'] !== 'low') {
            $expected[] = 'pii.redacted';
        }

        return [
            [
                'name' => 'auth-failure-normalizes-fields',
                'input' => 'failed login user=user-142 src=203.0.113.42 reason=bad_password',
                'expected' => $expected,
            ],
            [
                'name' => 'secret-redaction-blocks-indexing',
                'input' => 'Authorization: Bearer example-token password=example',
                'expected' => ['pii.redacted', 'tags', 'event.action'],
            ],
        ];
    }

    /**
     * Return detection-as-code promotion rules.
     *
     * @param  array{detection_goal: string}  $input
     * @return array{name: string, required_files: array<int, string>, promotion_checks: array<int, string>}
     */
    private function detectionAsCode(array $input): array
    {
        return [
            'name' => "{$input['detection_goal']}-rule-pack",
            'required_files' => [
                'rules/detections/auth-abuse.yml',
                'tests/fixtures/security-events.json',
                'docs/runbooks/security-alerts.md',
            ],
            'promotion_checks' => [
                'Rule has a true-positive fixture, false-positive fixture, and missing-field fixture.',
                'Rule contains owner, severity, evidence query, dedup window, and response runbook.',
                'Parser test proves required fields exist before the alert is enabled.',
                'Privacy review confirms secrets and PII are redacted before indexing.',
            ],
        ];
    }

    /**
     * Return a small Logstash-style parsing example learners can adapt.
     *
     * @param  array{log_sources: string, data_sensitivity: string}  $input
     */
    private function logstashPipelineExample(array $input): string
    {
        $redaction = $input['data_sensitivity'] === 'low'
            ? 'mutate { add_tag => ["privacy-reviewed"] }'
            : 'mutate { gsub => ["message", "(Authorization: Bearer )[A-Za-z0-9._-]+", "\\1[REDACTED]"] add_field => { "pii.redacted" => "true" } }';

        return <<<LOGSTASH
input {
  beats { port => 5044 }
}

filter {
  mutate {
    add_field => {
      "event.dataset" => "{$input['log_sources']}"
      "tags" => "security"
    }
  }
  grok {
    match => { "message" => "%{TIMESTAMP_ISO8601:@timestamp} %{WORD:event.action} user=%{DATA:user.id} src=%{IP:source.ip} outcome=%{WORD:event.outcome}" }
  }
  {$redaction}
}

output {
  elasticsearch {
    index => "security-{$input['log_sources']}-%{+YYYY.MM.dd}"
  }
}
LOGSTASH;
    }

    /**
     * Return a YAML-like detection rule learners can review as detection-as-code.
     *
     * @param  array{detection_goal: string, log_sources: string}  $input
     */
    private function detectionRuleYaml(array $input): string
    {
        return <<<YAML
id: {$input['detection_goal']}-001
name: {$input['detection_goal']} high-signal detection
source: {$input['log_sources']}
severity: high
owner: security-platform
query: >
  event.dataset:{$input['log_sources']} AND tags:security AND event.outcome:failure
dedup_window: 15m
required_fields:
  - @timestamp
  - event.action
  - event.outcome
  - user.id
  - source.ip
runbook: docs/runbooks/security-alerts.md
tests:
  true_positive: tests/fixtures/{$input['detection_goal']}-true-positive.json
  false_positive: tests/fixtures/{$input['detection_goal']}-false-positive.json
  missing_field: tests/fixtures/{$input['detection_goal']}-missing-field.json
YAML;
    }

    /**
     * Return index lifecycle policy details for storage cost control.
     *
     * @param  array{retention_need: string}  $input
     * @return array<int, array{phase: string, window: string, action: string}>
     */
    private function indexLifecyclePolicy(array $input): array
    {
        return match ($input['retention_need']) {
            'long' => [
                ['phase' => 'hot', 'window' => '0-30 days', 'action' => 'Search frequently, keep replicas and fast storage.'],
                ['phase' => 'warm', 'window' => '31-180 days', 'action' => 'Reduce cost, keep searchable for audit and slow investigations.'],
                ['phase' => 'delete-or-archive', 'window' => 'after compliance window', 'action' => 'Archive with approval or delete with evidence of retention policy.'],
            ],
            'medium' => [
                ['phase' => 'hot', 'window' => '0-14 days', 'action' => 'Search frequently during active incident windows.'],
                ['phase' => 'warm', 'window' => '15-90 days', 'action' => 'Keep lower-cost investigation history.'],
                ['phase' => 'delete-or-archive', 'window' => 'after 90 days', 'action' => 'Delete or archive based on audit need.'],
            ],
            default => [
                ['phase' => 'hot', 'window' => '0-7 days', 'action' => 'Keep short searchable history while proving signal quality.'],
                ['phase' => 'warm', 'window' => 'optional', 'action' => 'Skip warm storage until the team proves a need.'],
                ['phase' => 'delete-or-archive', 'window' => 'after 7 days', 'action' => 'Delete aggressively to control cost.'],
            ],
        };
    }

    /**
     * Return dashboard panels that connect monitoring views to decisions.
     *
     * @param  array{detection_goal: string}  $input
     * @return array<int, array{panel: string, signal: string, decision: string}>
     */
    private function dashboardPanels(array $input): array
    {
        return [
            [
                'panel' => 'Alert volume by rule and severity',
                'signal' => 'High alert count, repeated false positives, or sudden silence.',
                'decision' => 'Tune thresholds, disable noisy rules, or check ingestion failure.',
            ],
            [
                'panel' => "{$input['detection_goal']} investigation timeline",
                'signal' => 'First bad event, affected identity, source IP, host, and service sequence.',
                'decision' => 'Confirm scope before containment or escalation.',
            ],
            [
                'panel' => 'Pipeline health',
                'signal' => 'Shipper lag, parser failures, rejected documents, shard pressure, and storage growth.',
                'decision' => 'Fix ingestion reliability before trusting missing alerts.',
            ],
        ];
    }

    /**
     * Return a staged rollout plan for SIEM rules and parsing changes.
     *
     * @param  array{alert_maturity: string}  $input
     * @return array<int, array{stage: string, goal: string, promote_when: string}>
     */
    private function rolloutPlan(array $input): array
    {
        return [
            [
                'stage' => 'shadow',
                'goal' => 'Parse and score events without paging humans.',
                'promote_when' => 'Parser tests pass and expected fields appear on real traffic.',
            ],
            [
                'stage' => 'triage-only',
                'goal' => 'Send alerts to a review queue and measure false positives.',
                'promote_when' => 'Owner accepts severity, dedup, runbook, and false-positive rate.',
            ],
            [
                'stage' => $input['alert_maturity'] === 'high' ? 'pageable' : 'reviewed',
                'goal' => 'Promote only high-signal alerts to paging or reviewed incident workflow.',
                'promote_when' => 'Detection has fixtures, saved query, privacy approval, and rollback path.',
            ],
        ];
    }

    /**
     * Return rough ingest and storage guidance from the selected sources.
     *
     * @param  array{log_sources: string, retention_need: string}  $input
     * @return array{daily_ingest_estimate: string, index_pattern: string, storage_warning: string, scale_checks: array<int, string>}
     */
    private function capacityPlan(array $input): array
    {
        $dailyIngest = match ($input['log_sources']) {
            'mixed-enterprise' => '100-500 GB/day',
            'kubernetes' => '30-150 GB/day',
            'cloud-auth' => '5-50 GB/day',
            'linux-nginx' => '10-80 GB/day',
            default => '2-25 GB/day',
        };

        return [
            'daily_ingest_estimate' => $dailyIngest,
            'index_pattern' => "security-{$input['log_sources']}-yyyy.mm.dd",
            'storage_warning' => $input['retention_need'] === 'long'
                ? 'Long retention can dominate cost; prove which fields and indices must stay searchable before scaling ingest.'
                : 'Shorter retention lowers cost, but incident response may need exports or archive for severe cases.',
            'scale_checks' => [
                'Track shipper lag, rejected documents, ingest pipeline failures, shard count, disk watermarks, and query latency.',
                'Estimate hot storage from daily ingest, replica count, compression, and hot retention days.',
                'Set source-level drop rules for noisy low-value logs before increasing cluster size.',
            ],
        ];
    }

    /**
     * Return event relationships needed for useful correlation.
     *
     * @param  array{detection_goal: string}  $input
     * @return array<int, array{join_key: string, relates: string, detection_value: string}>
     */
    private function correlationMatrix(array $input): array
    {
        return [
            [
                'join_key' => 'user.id',
                'relates' => 'Auth events, privilege changes, application actions, and cloud control-plane activity.',
                'detection_value' => 'Shows whether one identity moved from failed auth to successful sensitive action.',
            ],
            [
                'join_key' => 'source.ip',
                'relates' => 'Proxy, WAF, application, SSH, cloud auth, and impossible-travel signals.',
                'detection_value' => 'Groups distributed symptoms into one suspicious source or network block.',
            ],
            [
                'join_key' => 'trace.id',
                'relates' => 'Application request logs, API gateway logs, queue jobs, and downstream service events.',
                'detection_value' => "Turns {$input['detection_goal']} investigation from scattered log search into one request timeline.",
            ],
            [
                'join_key' => 'cloud.account.id',
                'relates' => 'Identity, audit, network, storage, and compute events inside the same cloud boundary.',
                'detection_value' => 'Separates tenant/account blast radius during incident triage.',
            ],
        ];
    }

    /**
     * Return access boundaries for SIEM data and evidence.
     *
     * @param  array{data_sensitivity: string, team_size: string}  $input
     * @return array<int, array{role: string, can_do: string, cannot_do: string}>
     */
    private function accessModel(array $input): array
    {
        $rawRestriction = $input['data_sensitivity'] === 'high'
            ? 'Cannot view unredacted raw logs without incident approval.'
            : 'Cannot change parsing or retention policy without review.';

        return [
            [
                'role' => 'security-viewer',
                'can_do' => 'View dashboards, saved searches, alert summaries, and redacted evidence packets.',
                'cannot_do' => $rawRestriction,
            ],
            [
                'role' => 'security-analyst',
                'can_do' => 'Investigate alerts, export evidence packets, annotate false positives, and request containment.',
                'cannot_do' => 'Cannot edit detection rules or retention windows without peer review.',
            ],
            [
                'role' => $input['team_size'] === 'soc' ? 'detection-engineer' : 'platform-owner',
                'can_do' => 'Promote parser changes, detection rules, ILM policy, and dashboard changes through reviewed deployment.',
                'cannot_do' => 'Cannot bypass privacy review or remove audit logs for searches and exports.',
            ],
        ];
    }

    /**
     * Return a copy-ready packet for incident notes or interview discussion.
     *
     * @param  array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}  $readinessScore
     * @param  array{environment_name: string, log_sources: string, detection_goal: string, retention_need: string, alert_maturity: string, data_sensitivity: string, team_size: string}  $input
     */
    private function incidentEvidencePacketMarkdown(string $environment, array $input, string $riskLevel, array $readinessScore): string
    {
        return <<<MARKDOWN
# SIEM ELK Evidence Packet: {$environment}

## Scope
- Source: {$input['log_sources']}
- Detection goal: {$input['detection_goal']}
- Risk level: {$riskLevel}
- Readiness: {$readinessScore['label']} ({$readinessScore['score']}/100)
- Retention: {$input['retention_need']}
- Data sensitivity: {$input['data_sensitivity']}

## First Questions
- What happened first?
- Which user, host, IP, service, and cloud account are involved?
- Is this expected deployment, scanner noise, policy change, or confirmed abuse?

## Evidence To Attach
- Saved query: {$input['detection_goal']}-evidence
- Sample events before and after the first suspicious action
- Parser test result proving required fields exist
- Detection rule version, owner, severity, dedup window, and runbook
- Access review proving secrets and PII were redacted before export

## Decision
Contain only the affected identity, IP, host, route, token, or permission after evidence confirms scope.
MARKDOWN;
    }

    /**
     * Return SIEM pipeline threats that need controls before broad rollout.
     *
     * @param  array{data_sensitivity: string}  $input
     * @return array<int, array{threat: string, impact: string, control: string}>
     */
    private function threatModel(array $input): array
    {
        return [
            [
                'threat' => 'log injection',
                'impact' => 'Attacker-controlled log content can forge fields, hide real events, or poison dashboards.',
                'control' => 'Parse structured fields explicitly, escape user-controlled text, and keep raw message separate from normalized fields.',
            ],
            [
                'threat' => 'secret leakage',
                'impact' => $input['data_sensitivity'] === 'high'
                    ? 'Sensitive tokens, PII, or credentials may become searchable by too many users.'
                    : 'Operational metadata may be exposed beyond the team that needs it.',
                'control' => 'Redact before indexing, restrict raw log access, and audit searches plus exports.',
            ],
            [
                'threat' => 'alert fatigue',
                'impact' => 'Analysts stop trusting alerts and real incidents wait behind noisy rules.',
                'control' => 'Require owner, severity, dedup window, false-positive review, and promotion stages.',
            ],
            [
                'threat' => 'pipeline blind spot',
                'impact' => 'Missing shippers, parser failures, or dropped indices can make the SIEM look quiet while attacks continue.',
                'control' => 'Alert on shipper lag, parser error rate, rejected documents, and source heartbeat gaps.',
            ],
        ];
    }

    /**
     * Return a workflow for tuning noisy detections without losing evidence.
     *
     * @param  array{alert_maturity: string}  $input
     * @return array<int, array{step: string, action: string, done_when: string}>
     */
    private function falsePositiveWorkflow(array $input): array
    {
        return [
            [
                'step' => 'classify',
                'action' => 'Label each reviewed alert as true positive, expected behavior, benign scanner, parser error, or unknown.',
                'done_when' => 'Every noisy alert has a reason code instead of a vague ignore decision.',
            ],
            [
                'step' => 'tune',
                'action' => 'Adjust threshold, allowlist, source scope, required fields, or suppression window with a reviewed diff.',
                'done_when' => 'The rule keeps the true-positive fixture and reduces repeated benign alerts.',
            ],
            [
                'step' => $input['alert_maturity'] === 'high' ? 'promote' : 'hold',
                'action' => 'Promote only when evidence, owner, runbook, and false-positive rate are acceptable.',
                'done_when' => 'The rule has a documented decision to page, triage-only, dashboard-only, or disable.',
            ],
        ];
    }

    /**
     * Return a maturity roadmap from searchable logs to reliable security operations.
     *
     * @param  array{team_size: string}  $input
     * @return array<int, array{phase: string, focus: string, exit_criteria: string}>
     */
    private function maturityRoadmap(array $input): array
    {
        return [
            [
                'phase' => 'foundation',
                'focus' => 'Ship logs, normalize fields, redact secrets, and make source health visible.',
                'exit_criteria' => 'A responder can search by user, IP, host, service, action, outcome, and time window.',
            ],
            [
                'phase' => 'detection',
                'focus' => 'Add a small set of high-signal rules with fixtures, owners, and saved queries.',
                'exit_criteria' => 'Rules produce explainable alerts with known false-positive handling.',
            ],
            [
                'phase' => $input['team_size'] === 'soc' ? 'soc-scale' : 'team-scale',
                'focus' => 'Standardize detection-as-code, evidence packets, access reviews, and post-incident tuning.',
                'exit_criteria' => 'Incidents improve parser quality, rule quality, and runbooks instead of only closing tickets.',
            ],
        ];
    }

    /**
     * Return rollback and decommission guidance for bad rules or old log sources.
     *
     * @param  array{log_sources: string}  $input
     * @return array<int, array{trigger: string, action: string, evidence_to_keep: string}>
     */
    private function decommissionPlan(array $input): array
    {
        return [
            [
                'trigger' => 'Rule is noisy for two review cycles without useful detections.',
                'action' => 'Disable paging, move to triage-only or dashboard-only, and open a rule redesign task.',
                'evidence_to_keep' => 'False-positive samples, owner decision, previous thresholds, and final status.',
            ],
            [
                'trigger' => "{$input['log_sources']} source is deprecated or replaced.",
                'action' => 'Stop new ingest after migration, archive required evidence, then remove index templates and dashboards.',
                'evidence_to_keep' => 'Migration date, replacement source, retention approval, and last searchable index.',
            ],
            [
                'trigger' => 'Parser change breaks required fields.',
                'action' => 'Rollback parser version and block rule promotion until fixtures pass again.',
                'evidence_to_keep' => 'Failed fixture, parser version, affected rules, and recovery timestamp.',
            ],
        ];
    }

    /**
     * Return copy-ready implementation artifacts with stable filenames.
     *
     * @param  array{detection_goal: string, log_sources: string}  $input
     * @param  array<int, array{phase: string, window: string, action: string}>  $indexLifecyclePolicy
     * @return array<int, array{filename: string, type: string, purpose: string, content: string}>
     */
    private function artifactBundle(
        array $input,
        string $logstashPipeline,
        string $detectionRuleYaml,
        array $indexLifecyclePolicy,
        string $incidentEvidencePacketMarkdown,
    ): array {
        return [
            [
                'filename' => "pipelines/{$input['log_sources']}.conf",
                'type' => 'logstash',
                'purpose' => 'Parse, normalize, tag, redact, and index the selected security log source.',
                'content' => $logstashPipeline,
            ],
            [
                'filename' => "rules/detections/{$input['detection_goal']}.yml",
                'type' => 'detection-rule',
                'purpose' => 'Review the high-signal detection rule as code before enabling alerts.',
                'content' => $detectionRuleYaml,
            ],
            [
                'filename' => "policies/ilm-security-{$input['log_sources']}.json",
                'type' => 'ilm-policy',
                'purpose' => 'Document hot, warm, and archive/delete behavior for security indices.',
                'content' => json_encode(['policy' => ['phases' => $indexLifecyclePolicy]], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            ],
            [
                'filename' => "runbooks/{$input['detection_goal']}-evidence.md",
                'type' => 'incident-runbook',
                'purpose' => 'Attach investigation scope, evidence, and containment decision to an incident.',
                'content' => $incidentEvidencePacketMarkdown,
            ],
        ];
    }

    /**
     * Return a manifest for reviewing copied artifacts before promotion.
     *
     * @param  array{detection_goal: string, log_sources: string}  $input
     * @param  array<int, array{filename: string, type: string, purpose: string, content: string}>  $artifactBundle
     * @return array{bundle_name: string, artifact_count: int, files: array<int, array{filename: string, type: string, sha256: string}>, validation_checks: array<int, string>, promotion_note: string}
     */
    private function artifactManifest(array $input, array $artifactBundle): array
    {
        return [
            'bundle_name' => "siem-elk-{$input['log_sources']}-{$input['detection_goal']}",
            'artifact_count' => count($artifactBundle),
            'files' => collect($artifactBundle)
                ->map(fn (array $artifact): array => [
                    'filename' => $artifact['filename'],
                    'type' => $artifact['type'],
                    'sha256' => hash('sha256', $artifact['content']),
                ])
                ->values()
                ->all(),
            'validation_checks' => [
                'Run parser fixtures before enabling the detection rule.',
                'Review redaction behavior with a sensitive sample event.',
                'Confirm ILM windows match retention and legal requirements.',
                'Attach the evidence packet template to the incident runbook before paging humans.',
            ],
            'promotion_note' => 'Promote the bundle through review in this order: parser, redaction, detection rule, dashboard, alert routing, then paging.',
        ];
    }

    /**
     * Return the final gate for enabling alerts from the generated artifacts.
     *
     * @param  array{alert_maturity: string, data_sensitivity: string}  $input
     * @param  array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}  $readinessScore
     * @param  array{artifact_count: int, validation_checks: array<int, string>}  $artifactManifest
     * @return array{decision: string, score: int, blockers: array<int, string>, required_before_paging: array<int, string>}
     */
    private function promotionGate(array $input, array $readinessScore, array $artifactManifest): array
    {
        $score = $readinessScore['score'];
        $blockers = $readinessScore['blockers'];

        if ($artifactManifest['artifact_count'] < 4) {
            $score -= 20;
            $blockers[] = 'Artifact bundle is incomplete.';
        }

        if ($input['data_sensitivity'] === 'high') {
            $blockers[] = 'Privacy review must approve redaction before raw events are broadly searchable.';
        }

        if ($input['alert_maturity'] === 'low') {
            $blockers[] = 'Keep the first release triage-only until false-positive rate is measured.';
        }

        $score = max(0, $score);

        return [
            'decision' => match (true) {
                $score >= 85 => 'enable-paging',
                $score >= 60 => 'triage-only',
                default => 'do-not-page',
            },
            'score' => $score,
            'blockers' => array_values(array_unique($blockers)),
            'required_before_paging' => [
                'Parser fixtures pass for true positive, false positive, and missing-field cases.',
                'Redaction test proves secrets, tokens, and PII do not reach broad-search indices.',
                'Detection owner accepts severity, dedup window, saved query, and runbook.',
                ...$artifactManifest['validation_checks'],
            ],
        ];
    }

    /**
     * Return measurable operating targets for SIEM reliability and usefulness.
     *
     * @param  array{alert_maturity: string}  $input
     * @return array<int, array{metric: string, target: string, why: string}>
     */
    private function securitySlo(array $input): array
    {
        return [
            [
                'metric' => 'source_heartbeat_coverage',
                'target' => '95% of expected sources report at least one heartbeat or event in the last 15 minutes.',
                'why' => 'A quiet SIEM is dangerous when sources silently stop shipping logs.',
            ],
            [
                'metric' => 'parser_success_rate',
                'target' => '99% of security events produce required normalized fields.',
                'why' => 'Correlation rules depend on stable fields, not raw message text.',
            ],
            [
                'metric' => 'alert_triage_latency',
                'target' => $input['alert_maturity'] === 'high' ? 'Critical alerts acknowledged within 10 minutes.' : 'Triage-only alerts reviewed within one business day.',
                'why' => 'Detection value depends on timely ownership and decision making.',
            ],
            [
                'metric' => 'false_positive_review_rate',
                'target' => 'Noisy rules receive a tune, suppress, demote, or decommission decision within two review cycles.',
                'why' => 'Alert fatigue is a reliability problem, not only a people problem.',
            ],
        ];
    }

    /**
     * Return a short drill plan for validating the SIEM pipeline end to end.
     *
     * @param  array{detection_goal: string, log_sources: string}  $input
     * @return array{scenario: string, steps: array<int, string>, success_criteria: array<int, string>}
     */
    private function tabletopDrill(array $input): array
    {
        return [
            'scenario' => "Simulate {$input['detection_goal']} from {$input['log_sources']} and prove the SIEM creates useful evidence without paging too early.",
            'steps' => [
                'Inject one true-positive fixture and one benign fixture into a non-production index.',
                'Confirm Logstash parsing produces required normalized fields.',
                'Open the saved Kibana evidence query and export the incident packet.',
                'Walk through containment decision with security, platform, and service owner.',
                'Record one parser improvement, one rule improvement, and one runbook improvement.',
            ],
            'success_criteria' => [
                'True-positive fixture appears in the detection view with user, source IP, action, outcome, and timestamp.',
                'Benign fixture does not page humans.',
                'Evidence packet is complete enough for a responder who did not build the rule.',
            ],
        ];
    }

    /**
     * Return the learning loop after every security incident or noisy rule review.
     *
     * @param  array{detection_goal: string}  $input
     * @return array<int, array{signal: string, update: string}>
     */
    private function postIncidentFeedbackLoop(array $input): array
    {
        return [
            [
                'signal' => 'Missed detection',
                'update' => "Add a fixture and rule condition for the missed {$input['detection_goal']} behavior before closing the incident.",
            ],
            [
                'signal' => 'Noisy detection',
                'update' => 'Add false-positive examples, tune threshold or scope, and document why the rule stays enabled or gets demoted.',
            ],
            [
                'signal' => 'Slow investigation',
                'update' => 'Add missing correlation fields, saved query shortcuts, and evidence packet sections.',
            ],
            [
                'signal' => 'Unclear ownership',
                'update' => 'Update alert owner, escalation path, and runbook decision rights before the next drill.',
            ],
        ];
    }

    /**
     * Return a concise leadership summary for the selected plan.
     *
     * @param  array{log_sources: string, detection_goal: string}  $input
     * @param  array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}  $readinessScore
     * @return array{status: string, message: string, next_decision: string}
     */
    private function executiveBrief(array $input, string $riskLevel, array $readinessScore): array
    {
        return [
            'status' => $readinessScore['label'],
            'message' => "The {$input['log_sources']} SIEM plan for {$input['detection_goal']} is {$riskLevel} risk with readiness {$readinessScore['score']}/100. It should focus on evidence quality, redaction, alert ownership, and parser reliability before paging.",
            'next_decision' => $readinessScore['score'] >= 60
                ? 'Approve triage-only rollout and review false-positive rate before paging.'
                : 'Do not enable paging yet; fix blockers and run a tabletop drill first.',
        ];
    }

    /**
     * Return a practical operating cadence for keeping SIEM useful after launch.
     *
     * @param  array{team_size: string}  $input
     * @return array<int, array{cadence: string, owner: string, checks: array<int, string>}>
     */
    private function operatingCadence(array $input): array
    {
        $owner = $input['team_size'] === 'soc' ? 'security-analyst' : 'platform-owner';

        return [
            [
                'cadence' => 'daily',
                'owner' => $owner,
                'checks' => [
                    'Review critical alerts, triage queue, source heartbeat gaps, and parser error spikes.',
                    'Confirm no new broad-search index contains secrets, tokens, or unexpected PII.',
                ],
            ],
            [
                'cadence' => 'weekly',
                'owner' => 'detection-owner',
                'checks' => [
                    'Tune noisy rules with reason codes and update true-positive or false-positive fixtures.',
                    'Review top log volume sources and drop low-value noise before increasing cluster capacity.',
                ],
            ],
            [
                'cadence' => 'monthly',
                'owner' => 'security-platform',
                'checks' => [
                    'Review access grants, exported evidence, ILM retention windows, and dashboard ownership.',
                    'Run one tabletop drill or parser regression test against the most important detection.',
                ],
            ],
            [
                'cadence' => 'quarterly',
                'owner' => 'incident-lead',
                'checks' => [
                    'Decommission stale sources, stale dashboards, and rules that never produce useful evidence.',
                    'Update executive risk brief with readiness score, blockers, and security SLO trend.',
                ],
            ],
        ];
    }

    /**
     * Return an interview rubric for evaluating SIEM and ELK answers.
     *
     * @param  array{detection_goal: string}  $input
     * @return array{strong_answer: array<int, string>, weak_answer: array<int, string>, follow_up_questions: array<int, string>}
     */
    private function interviewRubric(array $input): array
    {
        return [
            'strong_answer' => [
                'Separates SIEM capability from ELK component names.',
                'Explains source collection, parsing, normalized fields, indexing, detection, alert ownership, and runbooks.',
                'Names tradeoffs: alert noise, retention cost, privacy, access control, parser quality, and source heartbeat.',
                "Gives a concrete {$input['detection_goal']} detection example with evidence fields and response action.",
            ],
            'weak_answer' => [
                'Says only that Elasticsearch stores logs and Kibana makes dashboards.',
                'Cannot explain how events are parsed, correlated, retained, redacted, or promoted to alerts.',
                'Ignores false positives, missing log sources, access control, and runbook ownership.',
            ],
            'follow_up_questions' => [
                'How do you know the SIEM is quiet because nothing happened versus because logs stopped arriving?',
                'What fields must exist before you trust a correlation rule?',
                'When would you keep an alert triage-only instead of paging?',
                'How would you reduce retention cost without destroying incident evidence?',
            ],
        ];
    }

    /**
     * Return a scorecard for deciding whether SIEM data is trustworthy enough.
     *
     * @param  array{data_sensitivity: string}  $input
     * @return array<int, array{dimension: string, target: string, test: string}>
     */
    private function dataQualityScorecard(array $input): array
    {
        $privacyTarget = $input['data_sensitivity'] === 'high'
            ? '100% of known secret and PII samples are redacted before indexing.'
            : 'Sensitive fields are either absent, masked, or explicitly approved for indexing.';

        return [
            [
                'dimension' => 'field completeness',
                'target' => 'Required fields exist on at least 99% of security events.',
                'test' => 'Sample events must include @timestamp, event.action, event.outcome, user.id or host.name, source.ip, service.name, and tags.',
            ],
            [
                'dimension' => 'timestamp correctness',
                'target' => 'Event time and ingest time drift stays within the accepted investigation window.',
                'test' => 'Compare @timestamp with ingest timestamp and flag sources with repeated clock drift.',
            ],
            [
                'dimension' => 'parser stability',
                'target' => 'Parser failure rate stays below 1% for security events.',
                'test' => 'Run parser fixtures during release and alert when grok failures spike.',
            ],
            [
                'dimension' => 'privacy safety',
                'target' => $privacyTarget,
                'test' => 'Run redaction fixtures for tokens, passwords, authorization headers, email, and user identifiers.',
            ],
        ];
    }

    /**
     * Return practical cost levers for ELK ingest and retention.
     *
     * @param  array{retention_need: string, log_sources: string}  $input
     * @return array<int, array{lever: string, action: string, tradeoff: string}>
     */
    private function costControlPlan(array $input): array
    {
        return [
            [
                'lever' => 'source filtering',
                'action' => "Drop duplicate, debug, health-check, and low-value {$input['log_sources']} events before indexing.",
                'tradeoff' => 'Dropping too early can remove evidence, so keep allowlisted security fields and sampled raw examples.',
            ],
            [
                'lever' => 'field pruning',
                'action' => 'Index normalized fields needed for detection and store bulky raw payloads only when approved.',
                'tradeoff' => 'Cheaper queries may lose forensic detail unless evidence export preserves raw samples.',
            ],
            [
                'lever' => 'tiered retention',
                'action' => $input['retention_need'] === 'long'
                    ? 'Move older indices to warm/archive storage after active investigation windows.'
                    : 'Keep hot retention short and export severe incidents before deletion.',
                'tradeoff' => 'Lower storage cost increases restore time for older investigations.',
            ],
            [
                'lever' => 'rule scope',
                'action' => 'Run expensive correlation only on security-tagged datasets and high-risk services first.',
                'tradeoff' => 'Narrow scope reduces cost but must be revisited when risk or architecture changes.',
            ],
        ];
    }

    /**
     * Return a checklist for adding a new log source to the SIEM pipeline.
     *
     * @param  array{log_sources: string}  $input
     * @return array<int, string>
     */
    private function sourceOnboardingChecklist(array $input): array
    {
        return [
            "Name the owner and business purpose for {$input['log_sources']} before enabling ingest.",
            'Document expected daily volume, sample events, required fields, and parser version.',
            'Add heartbeat or freshness monitoring so missing logs become visible.',
            'Run privacy/redaction fixtures before the source reaches broad-search indices.',
            'Create at least one saved investigation query and one runbook note for responders.',
            'Set ILM policy, access role, dashboard owner, and decommission criteria.',
        ];
    }

    /**
     * Return common implementation failures.
     *
     * @param  array{team_size: string}  $input
     * @return array<int, string>
     */
    private function failureModes(array $input): array
    {
        return [
            'Logs arrive but timestamps, source identity, or user fields are inconsistent, so correlation fails.',
            'Alert rules fire often but no owner knows whether to page, ignore, or investigate.',
            'Retention is chosen before cost and compliance needs are understood.',
            'Dashboards look complete, but raw events hide secrets or PII that too many people can read.',
            $input['team_size'] === 'solo'
                ? 'A solo maintainer builds too much custom SIEM logic and cannot tune or operate it.'
                : 'Multiple teams ship logs without shared parsing conventions, causing duplicate fields and broken searches.',
        ];
    }

    /**
     * Return incident response steps for SIEM alerts.
     *
     * @param  array{detection_goal: string}  $input
     * @return array<int, string>
     */
    private function runbook(array $input): array
    {
        return [
            "Confirm the {$input['detection_goal']} alert with the saved Kibana query and raw event samples.",
            'Identify affected user, host, service, IP, cloud account, time window, and action outcome.',
            'Check whether this is expected deployment, approved access, scanner noise, or real abuse.',
            'Contain only the affected account, IP, token, host, route, or permission when evidence supports it.',
            'Export the evidence query, alert timeline, action taken, and follow-up rule change.',
        ];
    }

    /**
     * Return review gates before promoting SIEM rules.
     *
     * @return array<int, string>
     */
    private function reviewChecklist(): array
    {
        return [
            'Can the rule explain what happened, who or what did it, when it happened, and which system was affected?',
            'Does the rule have test events for true positive, false positive, and missing-field behavior?',
            'Is the alert actionable with owner, severity, dedup, and runbook?',
            'Are secrets and PII redacted before indexing or export?',
            'Is retention tied to investigation, compliance, cost, and deletion requirements?',
        ];
    }

    /**
     * Return a concise interview answer.
     *
     * @param  array{log_sources: string, detection_goal: string}  $input
     */
    private function interviewAnswer(array $input, string $riskLevel): string
    {
        return "SIEM is the security system that collects events, normalizes them, correlates suspicious behavior, alerts owners, and preserves evidence for investigation. ELK can implement that pipeline: Beats or Elastic Agent ship logs, Logstash parses and enriches them, Elasticsearch indexes them, and Kibana provides search, dashboards, and alerts. In this {$riskLevel}-risk scenario, I would first make {$input['log_sources']} logs searchable with a shared field contract, then add high-signal {$input['detection_goal']} rules, retention policy, privacy controls, and runbooks.";
    }

    /**
     * Return a copy-ready implementation prompt.
     *
     * @param  array{environment_name: string, log_sources: string, detection_goal: string, retention_need: string, alert_maturity: string, data_sensitivity: string, team_size: string}  $input
     */
    private function implementationPrompt(array $input): string
    {
        return <<<PROMPT
Design a SIEM/ELK plan for {$input['environment_name']}.
Sources: {$input['log_sources']}.
Detection goal: {$input['detection_goal']}.
Retention: {$input['retention_need']}.
Alert maturity: {$input['alert_maturity']}.
Data sensitivity: {$input['data_sensitivity']}.
Team size: {$input['team_size']}.

Return: ingest flow, normalized fields, 3 detection rules, alert ownership, retention policy, privacy controls, dashboards, failure modes, and a 5-step incident runbook.
Use this flow: source -> shipper -> parser -> index -> dashboard or alert -> runbook.
PROMPT;
    }
}

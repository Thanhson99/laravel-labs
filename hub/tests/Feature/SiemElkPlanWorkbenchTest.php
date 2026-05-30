<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class SiemElkPlanWorkbenchTest extends TestCase
{
    /**
     * The SIEM ELK workbench renders the security logging planning loop.
     */
    public function test_siem_elk_plan_workbench_renders(): void
    {
        $this->get('/workbench/siem-elk-plan')
            ->assertOk()
            ->assertSee('SIEM ELK Plan Workbench')
            ->assertSee('POST /api/practice/siem-elk-plan')
            ->assertSee('SiemElkPlanService')
            ->assertSee('Cloud auth abuse')
            ->assertSee('Laravel web attack')
            ->assertSee('Executive Summary')
            ->assertSee('Implementation Prompt')
            ->assertSee('Saved Queries')
            ->assertSee('Parser Tests')
            ->assertSee('Detection-as-Code')
            ->assertSee('Logstash Pipeline')
            ->assertSee('Detection Rule YAML')
            ->assertSee('Index Lifecycle Policy')
            ->assertSee('Rollout Plan')
            ->assertSee('Evidence Packet')
            ->assertSee('Capacity Plan')
            ->assertSee('Correlation Matrix')
            ->assertSee('Access Model')
            ->assertSee('Threat Model')
            ->assertSee('False-Positive Workflow')
            ->assertSee('Maturity Roadmap')
            ->assertSee('Decommission Plan')
            ->assertSee('Artifact Bundle')
            ->assertSee('Artifact Manifest')
            ->assertSee('Promotion Gate')
            ->assertSee('Security SLO')
            ->assertSee('Tabletop Drill')
            ->assertSee('Post-Incident Feedback Loop')
            ->assertSee('Executive Brief')
            ->assertSee('Operating Cadence')
            ->assertSee('Interview Rubric')
            ->assertSee('Data Quality Scorecard')
            ->assertSee('Cost Control Plan')
            ->assertSee('Source Onboarding Checklist')
            ->assertSee('Copy evidence')
            ->assertSee('Plan SIEM pipeline');
    }

    /**
     * The SIEM ELK API returns ELK roles, field contracts, detections, and runbook guidance.
     */
    public function test_siem_elk_plan_api_returns_plan(): void
    {
        $this->postJson('/api/practice/siem-elk-plan', [
            'environment_name' => 'Production Security Logs',
            'log_sources' => 'cloud-auth',
            'detection_goal' => 'auth-abuse',
            'retention_need' => 'long',
            'alert_maturity' => 'low',
            'data_sensitivity' => 'high',
            'team_size' => 'small',
        ])
            ->assertOk()
            ->assertJsonPath('data.environment', 'ProductionSecurityLogs')
            ->assertJsonPath('data.risk_level', 'high')
            ->assertJsonPath('data.readiness_score.label', 'blocked')
            ->assertJsonPath('data.readiness_score.next_actions.0', 'Start with three high-signal alerts and require owner, severity, query, dedup window, and runbook.')
            ->assertJsonPath('data.executive_summary.first_action', 'Inventory cloud-auth logs and define the fields needed to detect auth-abuse before tuning dashboards.')
            ->assertJsonPath('data.elk_roles.1.component', 'Logstash')
            ->assertJsonPath('data.pipeline.4.step', 'detect')
            ->assertJsonPath('data.field_contract.0', '@timestamp')
            ->assertJsonPath('data.detection_rules.0.name', 'auth.bruteforce.window')
            ->assertJsonPath('data.saved_queries.0.query', 'event.action:login AND event.outcome:failure AND @timestamp >= now-15m')
            ->assertJsonPath('data.parser_tests.1.name', 'secret-redaction-blocks-indexing')
            ->assertJsonPath('data.detection_as_code.required_files.0', 'rules/detections/auth-abuse.yml')
            ->assertJsonPath('data.index_lifecycle_policy.0.window', '0-30 days')
            ->assertJsonPath('data.dashboard_panels.2.panel', 'Pipeline health')
            ->assertJsonPath('data.rollout_plan.0.stage', 'shadow')
            ->assertJsonPath('data.capacity_plan.daily_ingest_estimate', '5-50 GB/day')
            ->assertJsonPath('data.correlation_matrix.0.join_key', 'user.id')
            ->assertJsonPath('data.access_model.0.role', 'security-viewer')
            ->assertJsonPath('data.threat_model.0.threat', 'log injection')
            ->assertJsonPath('data.false_positive_workflow.0.step', 'classify')
            ->assertJsonPath('data.maturity_roadmap.0.phase', 'foundation')
            ->assertJsonPath('data.decommission_plan.2.action', 'Rollback parser version and block rule promotion until fixtures pass again.')
            ->assertJsonPath('data.artifact_bundle.0.filename', 'pipelines/cloud-auth.conf')
            ->assertJsonPath('data.artifact_bundle.1.type', 'detection-rule')
            ->assertJsonPath('data.artifact_manifest.bundle_name', 'siem-elk-cloud-auth-auth-abuse')
            ->assertJsonPath('data.artifact_manifest.artifact_count', 4)
            ->assertJsonPath('data.promotion_gate.decision', 'do-not-page')
            ->assertJsonPath('data.promotion_gate.required_before_paging.0', 'Parser fixtures pass for true positive, false positive, and missing-field cases.')
            ->assertJsonPath('data.security_slo.0.metric', 'source_heartbeat_coverage')
            ->assertJsonPath('data.tabletop_drill.success_criteria.1', 'Benign fixture does not page humans.')
            ->assertJsonPath('data.post_incident_feedback_loop.0.signal', 'Missed detection')
            ->assertJsonPath('data.executive_brief.next_decision', 'Do not enable paging yet; fix blockers and run a tabletop drill first.')
            ->assertJsonPath('data.operating_cadence.0.cadence', 'daily')
            ->assertJsonPath('data.interview_rubric.weak_answer.0', 'Says only that Elasticsearch stores logs and Kibana makes dashboards.')
            ->assertJsonPath('data.data_quality_scorecard.0.dimension', 'field completeness')
            ->assertJsonPath('data.cost_control_plan.0.lever', 'source filtering')
            ->assertJsonPath('data.source_onboarding_checklist.2', 'Add heartbeat or freshness monitoring so missing logs become visible.')
            ->assertJsonPath('data.alert_policy.mode', 'quiet-start')
            ->assertJsonPath('data.retention_policy.hot', 'Keep 14-30 days searchable for active investigations.')
            ->assertJsonPath('data.dashboard_plan.2.name', 'pipeline health')
            ->assertJsonPath('data.runbook.0', 'Confirm the auth-abuse alert with the saved Kibana query and raw event samples.')
            ->assertJsonPath('data.commands.1', 'php artisan route:list --path=siem-elk-plan')
            ->assertSee('SIEM is the security system')
            ->assertSee('security-cloud-auth')
            ->assertSee('id: auth-abuse-001')
            ->assertSee('SIEM ELK Evidence Packet: ProductionSecurityLogs')
            ->assertJsonFragment(['event.action' => 'auth-abuse']);
    }

    /**
     * Invalid SIEM ELK payloads return validation errors.
     */
    public function test_siem_elk_plan_api_validates_payload(): void
    {
        $this->postJson('/api/practice/siem-elk-plan', [
            'environment_name' => '<bad>',
            'log_sources' => 'everything',
            'detection_goal' => 'magic',
            'retention_need' => 'forever',
            'alert_maturity' => 'unknown',
            'data_sensitivity' => 'secret',
            'team_size' => 'crowd',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'environment_name',
                'log_sources',
                'detection_goal',
                'retention_need',
                'alert_maturity',
                'data_sensitivity',
                'team_size',
            ]);
    }
}

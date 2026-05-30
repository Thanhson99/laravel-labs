<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\SiemElkPlanService;
use PHPUnit\Framework\TestCase;

final class SiemElkPlanServiceTest extends TestCase
{
    /**
     * The planner returns SIEM and ELK guidance beyond dashboard-only logging.
     */
    public function test_siem_elk_plan_includes_pipeline_detections_and_privacy_controls(): void
    {
        $plan = (new SiemElkPlanService)->plan([
            'environment_name' => 'Production Security Logs',
            'log_sources' => 'cloud-auth',
            'detection_goal' => 'auth-abuse',
            'retention_need' => 'long',
            'alert_maturity' => 'low',
            'data_sensitivity' => 'high',
            'team_size' => 'small',
        ]);

        $this->assertSame('ProductionSecurityLogs', $plan['environment']);
        $this->assertSame('high', $plan['risk_level']);
        $this->assertSame(35, $plan['readiness_score']['score']);
        $this->assertSame('blocked', $plan['readiness_score']['label']);
        $this->assertSame('Elastic Agent or Beats', $plan['elk_roles'][0]['component']);
        $this->assertSame('collect', $plan['pipeline'][0]['step']);
        $this->assertContains('pii.redacted', $plan['field_contract']);
        $this->assertSame('auth.bruteforce.window', $plan['detection_rules'][0]['name']);
        $this->assertSame('failed-login-burst', $plan['saved_queries'][0]['name']);
        $this->assertSame('login', $plan['sample_events'][0]['event.action']);
        $this->assertSame('auth-failure-normalizes-fields', $plan['parser_tests'][0]['name']);
        $this->assertSame('auth-abuse-rule-pack', $plan['detection_as_code']['name']);
        $this->assertStringContainsString('beats { port => 5044 }', $plan['logstash_pipeline_example']);
        $this->assertStringContainsString('id: auth-abuse-001', $plan['detection_rule_yaml']);
        $this->assertSame('hot', $plan['index_lifecycle_policy'][0]['phase']);
        $this->assertSame('Alert volume by rule and severity', $plan['dashboard_panels'][0]['panel']);
        $this->assertSame('shadow', $plan['rollout_plan'][0]['stage']);
        $this->assertSame('5-50 GB/day', $plan['capacity_plan']['daily_ingest_estimate']);
        $this->assertSame('user.id', $plan['correlation_matrix'][0]['join_key']);
        $this->assertSame('security-viewer', $plan['access_model'][0]['role']);
        $this->assertSame('log injection', $plan['threat_model'][0]['threat']);
        $this->assertSame('classify', $plan['false_positive_workflow'][0]['step']);
        $this->assertSame('foundation', $plan['maturity_roadmap'][0]['phase']);
        $this->assertSame('Rule is noisy for two review cycles without useful detections.', $plan['decommission_plan'][0]['trigger']);
        $this->assertSame('pipelines/cloud-auth.conf', $plan['artifact_bundle'][0]['filename']);
        $this->assertSame('rules/detections/auth-abuse.yml', $plan['artifact_bundle'][1]['filename']);
        $this->assertStringContainsString('"phase": "hot"', $plan['artifact_bundle'][2]['content']);
        $this->assertStringContainsString('SIEM ELK Evidence Packet', $plan['artifact_bundle'][3]['content']);
        $this->assertSame('siem-elk-cloud-auth-auth-abuse', $plan['artifact_manifest']['bundle_name']);
        $this->assertSame(4, $plan['artifact_manifest']['artifact_count']);
        $this->assertSame('pipelines/cloud-auth.conf', $plan['artifact_manifest']['files'][0]['filename']);
        $this->assertSame(64, strlen($plan['artifact_manifest']['files'][0]['sha256']));
        $this->assertSame('Run parser fixtures before enabling the detection rule.', $plan['artifact_manifest']['validation_checks'][0]);
        $this->assertSame('do-not-page', $plan['promotion_gate']['decision']);
        $this->assertSame(35, $plan['promotion_gate']['score']);
        $this->assertContains('Keep the first release triage-only until false-positive rate is measured.', $plan['promotion_gate']['blockers']);
        $this->assertSame('Parser fixtures pass for true positive, false positive, and missing-field cases.', $plan['promotion_gate']['required_before_paging'][0]);
        $this->assertSame('source_heartbeat_coverage', $plan['security_slo'][0]['metric']);
        $this->assertStringContainsString('Simulate auth-abuse', $plan['tabletop_drill']['scenario']);
        $this->assertSame('Missed detection', $plan['post_incident_feedback_loop'][0]['signal']);
        $this->assertSame('blocked', $plan['executive_brief']['status']);
        $this->assertSame('daily', $plan['operating_cadence'][0]['cadence']);
        $this->assertSame('detection-owner', $plan['operating_cadence'][1]['owner']);
        $this->assertSame('Separates SIEM capability from ELK component names.', $plan['interview_rubric']['strong_answer'][0]);
        $this->assertStringContainsString('logs stopped arriving', $plan['interview_rubric']['follow_up_questions'][0]);
        $this->assertSame('field completeness', $plan['data_quality_scorecard'][0]['dimension']);
        $this->assertStringContainsString('100% of known secret', $plan['data_quality_scorecard'][3]['target']);
        $this->assertSame('source filtering', $plan['cost_control_plan'][0]['lever']);
        $this->assertStringContainsString('Name the owner', $plan['source_onboarding_checklist'][0]);
        $this->assertSame('quiet-start', $plan['alert_policy']['mode']);
        $this->assertStringContainsString('14-30 days', $plan['retention_policy']['hot']);
        $this->assertStringContainsString('Redact or tokenize PII', $plan['privacy_controls'][3]);
        $this->assertStringContainsString('SIEM is the security system', $plan['interview_answer']);
        $this->assertStringContainsString('source -> shipper', $plan['implementation_prompt']);
        $this->assertStringContainsString('SIEM ELK Evidence Packet: ProductionSecurityLogs', $plan['incident_evidence_packet_markdown']);
        $this->assertSame('php artisan test --filter SiemElkPlan', $plan['commands'][0]);
    }
}

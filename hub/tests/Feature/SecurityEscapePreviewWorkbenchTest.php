<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class SecurityEscapePreviewWorkbenchTest extends TestCase
{
    /**
     * The Blade security workbench renders the escaping practice loop.
     */
    public function test_security_escape_preview_workbench_renders(): void
    {
        $response = $this->get('/workbench/security-escape-preview');

        $response
            ->assertOk()
            ->assertSee('Security Escape Preview Workbench')
            ->assertSee('POST /api/practice/security-escape-preview')
            ->assertSee('SecurityEscapePreviewService')
            ->assertSee('Preview escaped output')
            ->assertSee('Rendering context')
            ->assertSee('JavaScript data')
            ->assertSee('Payload presets')
            ->assertSee('JavaScript URL')
            ->assertSee('DOM sink')
            ->assertSee('Context rule')
            ->assertSee('Risk findings')
            ->assertSee('Release decision')
            ->assertSee('Payload tests')
            ->assertSee('Generated test plan')
            ->assertSee('Secure snippets')
            ->assertSee('Review checklist')
            ->assertSee('Remediation steps')
            ->assertSee('Severity summary')
            ->assertSee('XSS variants')
            ->assertSee('Interview outline')
            ->assertSee('CSP');
    }

    /**
     * The security escape API returns escaped content and risk flags.
     */
    public function test_security_escape_preview_api_returns_escaped_content(): void
    {
        $response = $this->postJson('/api/practice/security-escape-preview', [
            'title' => 'Profile bio preview',
            'body' => "Hello <script>alert('xss')</script> Laravel",
            'rendering_context' => 'javascript_data',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Profile bio preview')
            ->assertJsonPath('data.rendering_context', 'javascript_data')
            ->assertJsonPath('data.risk_flags.0', 'script-tag')
            ->assertJsonPath('data.risk_findings.0.severity', 'high')
            ->assertJsonPath('data.severity_summary.high', 1)
            ->assertJsonPath('data.release_decision.status', 'block')
            ->assertJsonPath('data.review_checklist.0.status', 'review')
            ->assertJsonPath('data.review_checklist.1.status', 'required')
            ->assertJsonPath('data.remediation_steps.0.owner', 'feature owner')
            ->assertJsonPath('data.remediation_steps.0.priority', 1)
            ->assertJsonPath('data.variant_review.0.variant', 'reflected-xss')
            ->assertJsonPath('data.variant_review.2.variant', 'dom-xss')
            ->assertJsonPath('data.secure_code_snippets.0.label', 'Safe script data handoff')
            ->assertJsonPath('data.secure_code_snippets.1.code', 'target.textContent = viewModel.displayName;')
            ->assertJsonPath('data.test_plan.0.name', 'blocks_script_tag_payload_in_javascript_data_context')
            ->assertJsonPath('data.test_plan.0.purpose', 'Prove the most relevant payload is classified before release.')
            ->assertJsonPath('data.interview_answer_outline.0', 'XSS means untrusted data reaches a browser context where it can execute instead of render as data.')
            ->assertJsonPath('data.escaped.body', 'Hello &lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt; Laravel')
            ->assertJsonPath('data.blade_rules.0', 'Use `{{ $value }}` for user-provided text.')
            ->assertJsonPath('data.context_rule', 'Serialize data with @json or Js::from instead of concatenating strings inside script tags.')
            ->assertJsonPath('data.payload_tests.0', "</script><script>alert('xss')</script>")
            ->assertJsonPath('data.csp_note', 'Use Content Security Policy as defense-in-depth. It should reduce blast radius, not replace output escaping or sanitization.');
    }

    /**
     * URL context blocks unsafe schemes in the safe rendering preview.
     */
    public function test_security_escape_preview_api_blocks_unsafe_url_context(): void
    {
        $response = $this->postJson('/api/practice/security-escape-preview', [
            'title' => 'Link preview',
            'body' => 'javascript:alert(1)',
            'rendering_context' => 'url',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.rendering_context', 'url')
            ->assertJsonPath('data.risk_flags.0', 'javascript-url')
            ->assertJsonPath('data.safe_rendering.url_value', '#blocked-unsafe-url')
            ->assertJsonPath('data.secure_code_snippets.0.label', 'Validated URL render')
            ->assertJsonPath('data.remediation_steps.0.done_when', 'High-risk flags disappear or are proven harmless by context-specific tests.')
            ->assertJsonPath('data.context_rule', 'Validate URL schemes with an allowlist before rendering href or src.')
            ->assertJsonPath('data.test_plan.0.name', 'blocks_javascript_url_payload_in_url_context')
            ->assertJsonPath('data.review_checklist.0.file_hint', 'request validation rules and link-rendering views');
    }

    /**
     * Invalid preview payloads return validation errors.
     */
    public function test_security_escape_preview_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/security-escape-preview', [
            'title' => 'No',
            'body' => '',
            'rendering_context' => 'raw_html',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'body', 'rendering_context']);
    }
}

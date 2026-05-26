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
            ->assertSee('Preview escaped output');
    }

    /**
     * The security escape API returns escaped content and risk flags.
     */
    public function test_security_escape_preview_api_returns_escaped_content(): void
    {
        $response = $this->postJson('/api/practice/security-escape-preview', [
            'title' => 'Profile bio preview',
            'body' => "Hello <script>alert('xss')</script> Laravel",
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Profile bio preview')
            ->assertJsonPath('data.risk_flags.0', 'script-tag')
            ->assertJsonPath('data.escaped.body', 'Hello &lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt; Laravel')
            ->assertJsonPath('data.blade_rules.0', 'Use `{{ $value }}` for user-provided text.');
    }

    /**
     * Invalid preview payloads return validation errors.
     */
    public function test_security_escape_preview_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/security-escape-preview', [
            'title' => 'No',
            'body' => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'body']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\SecurityEscapePreviewService;
use PHPUnit\Framework\TestCase;

final class SecurityEscapePreviewServiceTest extends TestCase
{
    /**
     * Script payloads block release until rendering is fixed.
     */
    public function test_script_payload_blocks_release(): void
    {
        $preview = (new SecurityEscapePreviewService)->preview([
            'title' => 'Profile',
            'body' => '<script>alert(1)</script>',
            'rendering_context' => 'html_text',
        ]);

        $this->assertSame('script-tag', $preview['risk_flags'][0]);
        $this->assertSame(1, $preview['severity_summary']['high']);
        $this->assertSame('block', $preview['release_decision']['status']);
        $this->assertSame('&lt;script&gt;alert(1)&lt;/script&gt;', $preview['escaped']['body']);
        $this->assertSame('required', $preview['review_checklist'][1]['status']);
        $this->assertSame('feature owner', $preview['remediation_steps'][0]['owner']);
        $this->assertSame('test owner', $preview['remediation_steps'][2]['owner']);
        $this->assertSame('reflected-xss', $preview['variant_review'][0]['variant']);
        $this->assertSame('stored-xss', $preview['variant_review'][1]['variant']);
        $this->assertSame('blocks_script_tag_payload_in_html_text_context', $preview['test_plan'][0]['name']);
        $this->assertSame('assertJsonPath("data.risk_flags.0", "script-tag")', $preview['test_plan'][0]['expected_assertion']);
        $this->assertSame('Escaped Blade text', $preview['secure_code_snippets'][0]['label']);
        $this->assertSame('{{ $comment->body }}', $preview['secure_code_snippets'][0]['code']);
        $this->assertSame('XSS means untrusted data reaches a browser context where it can execute instead of render as data.', $preview['interview_answer_outline'][0]);
    }

    /**
     * DOM sink payloads require review without automatically blocking release.
     */
    public function test_dom_sink_payload_requires_review(): void
    {
        $preview = (new SecurityEscapePreviewService)->preview([
            'title' => 'Hydration code',
            'body' => 'element.innerHTML = userInput',
            'rendering_context' => 'javascript_data',
        ]);

        $this->assertSame('unsafe-dom-sink', $preview['risk_flags'][0]);
        $this->assertSame(1, $preview['severity_summary']['medium']);
        $this->assertSame('review', $preview['release_decision']['status']);
        $this->assertSame('Blade script blocks and frontend hydration payloads', $preview['review_checklist'][0]['file_hint']);
        $this->assertSame('security reviewer', $preview['remediation_steps'][0]['owner']);
        $this->assertStringContainsString('DOM sink signal found.', $preview['variant_review'][2]['review_prompt']);
    }

    /**
     * Unsafe URL schemes are blocked in URL previews.
     */
    public function test_url_context_blocks_unsafe_scheme(): void
    {
        $preview = (new SecurityEscapePreviewService)->preview([
            'title' => 'Link',
            'body' => 'javascript:alert(1)',
            'rendering_context' => 'url',
        ]);

        $this->assertSame('javascript-url', $preview['risk_flags'][0]);
        $this->assertSame('#blocked-unsafe-url', $preview['safe_rendering']['url_value']);
        $this->assertSame('Validated URL render', $preview['secure_code_snippets'][0]['label']);
        $this->assertSame('request validation rules and link-rendering views', $preview['review_checklist'][0]['file_hint']);
    }

    /**
     * Plain text previews remain ready and still return focused payload suggestions.
     */
    public function test_plain_text_preview_is_ready(): void
    {
        $preview = (new SecurityEscapePreviewService)->preview([
            'title' => 'Safe note',
            'body' => 'Plain learner-visible content',
            'rendering_context' => 'html_text',
        ]);

        $this->assertSame([], $preview['risk_flags']);
        $this->assertSame('ready', $preview['release_decision']['status']);
        $this->assertSame('ready', $preview['review_checklist'][0]['status']);
        $this->assertSame('implementer', $preview['remediation_steps'][0]['owner']);
        $this->assertSame('blocks_no_risk_flag_payload_in_html_text_context', $preview['test_plan'][0]['name']);
        $this->assertSame('assertJsonPath("data.release_decision.status", "ready")', $preview['test_plan'][0]['expected_assertion']);
        $this->assertContains('<script>alert(1)</script>', $preview['payload_tests']);
    }
}

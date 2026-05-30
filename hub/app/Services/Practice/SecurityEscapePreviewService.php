<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class SecurityEscapePreviewService
{
    /**
     * Build an escaped preview and security checklist for learner-provided content.
     *
     * @param  array{title: string, body: string, rendering_context?: string}  $content
     * @return array<string, mixed>
     */
    public function preview(array $content): array
    {
        $title = trim($content['title']);
        $body = trim($content['body']);
        $context = (string) ($content['rendering_context'] ?? 'html_text');
        $riskFindings = $this->riskFindings($title.' '.$body);
        $riskFlags = collect($riskFindings)
            ->pluck('flag')
            ->values()
            ->all();
        $severitySummary = $this->severitySummary($riskFindings);

        return [
            'title' => $title,
            'body' => $body,
            'rendering_context' => $context,
            'escaped' => [
                'title' => e($title),
                'body' => e($body),
            ],
            'safe_rendering' => $this->safeRenderingExamples($body, $context),
            'secure_code_snippets' => $this->secureCodeSnippets($context),
            'risk_flags' => $riskFlags,
            'risk_findings' => $riskFindings,
            'severity_summary' => $severitySummary,
            'release_decision' => $this->releaseDecision($severitySummary),
            'blade_rules' => [
                'Use `{{ $value }}` for user-provided text.',
                'Avoid `{!! $value !!}` unless the HTML source is trusted and sanitized.',
                'Use `@json($value)` or `Js::from($value)` when handing server data to JavaScript.',
                'Validate input shape before rendering it.',
                'Keep escaping decisions visible in the Blade view.',
            ],
            'context_rule' => $this->contextRule($context),
            'payload_tests' => $this->payloadTests($context),
            'test_plan' => $this->testPlan($context, $riskFlags),
            'review_checklist' => $this->reviewChecklist($context, $riskFindings),
            'remediation_steps' => $this->remediationSteps($context, $riskFindings, $severitySummary),
            'variant_review' => $this->variantReview($riskFindings),
            'interview_answer_outline' => $this->interviewAnswerOutline($context, $riskFindings, $severitySummary),
            'csp_note' => 'Use Content Security Policy as defense-in-depth. It should reduce blast radius, not replace output escaping or sanitization.',
            'next_action' => $riskFlags === []
                ? 'Render with escaped Blade output and keep the feature test focused on visible text.'
                : 'Render escaped output, review the context-specific finding, then add a payload test proving script-like content is not executed.',
        ];
    }

    /**
     * Detect common browser execution patterns that should be treated as unsafe user content.
     *
     * @return array<int, array{flag: string, severity: string, evidence: string, fix: string}>
     */
    private function riskFindings(string $value): array
    {
        $lowerValue = Str::lower($value);
        $findings = [];

        if (str_contains($lowerValue, '<script')) {
            $findings[] = [
                'flag' => 'script-tag',
                'severity' => 'high',
                'evidence' => 'Input contains a script tag that can execute if rendered as raw HTML.',
                'fix' => 'Render as escaped text or remove the tag through a sanitizer before any rich-text output.',
            ];
        }

        if (str_contains($lowerValue, 'onerror=') || str_contains($lowerValue, 'onclick=') || str_contains($lowerValue, 'onload=')) {
            $findings[] = [
                'flag' => 'inline-event-handler',
                'severity' => 'high',
                'evidence' => 'Input contains an inline event handler such as onerror, onclick, or onload.',
                'fix' => 'Do not render user-controlled attributes as HTML. Sanitize rich text and escape normal text.',
            ];
        }

        if (str_contains($lowerValue, 'javascript:')) {
            $findings[] = [
                'flag' => 'javascript-url',
                'severity' => 'high',
                'evidence' => 'Input contains a javascript: URL that can execute when used in href or src.',
                'fix' => 'Allowlist http, https, mailto, or application-specific URL schemes before rendering links.',
            ];
        }

        if (str_contains($lowerValue, 'innerhtml') || str_contains($lowerValue, 'document.write')) {
            $findings[] = [
                'flag' => 'unsafe-dom-sink',
                'severity' => 'medium',
                'evidence' => 'Input references DOM sinks commonly involved in DOM-based XSS.',
                'fix' => 'Prefer textContent, safe templating, or sanitized HTML before assigning to DOM sinks.',
            ];
        }

        if (str_contains($lowerValue, '<svg') || str_contains($lowerValue, '<iframe') || str_contains($lowerValue, 'srcdoc=')) {
            $findings[] = [
                'flag' => 'active-html',
                'severity' => 'medium',
                'evidence' => 'Input contains active HTML elements or attributes that need sanitizer review.',
                'fix' => 'Strip active elements from rich text or isolate trusted embeds behind explicit allowlists.',
            ];
        }

        return $findings;
    }

    /**
     * Build examples for safe output in common browser contexts.
     *
     * @return array{blade_text: string, javascript_data: string, url_value: string}
     */
    private function safeRenderingExamples(string $body, string $context): array
    {
        return [
            'blade_text' => e($body),
            'javascript_data' => json_encode($body, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR),
            'url_value' => $context === 'url' ? $this->safeUrl($body) : 'Use a dedicated validated URL field before rendering href/src.',
        ];
    }

    /**
     * Return short code examples for rendering the selected context safely.
     *
     * @return array<int, array{label: string, code: string, note: string}>
     */
    private function secureCodeSnippets(string $context): array
    {
        return match ($context) {
            'html_attribute' => [
                [
                    'label' => 'Escaped attribute value',
                    'code' => '<input value="{{ $profile->display_name }}">',
                    'note' => 'Escape the value and keep the attribute name controlled by the template.',
                ],
                [
                    'label' => 'Controlled class map',
                    'code' => '@class(["is-active" => $isActive])',
                    'note' => 'Build classes from booleans or allowlists instead of raw request strings.',
                ],
            ],
            'javascript_data' => [
                [
                    'label' => 'Safe script data handoff',
                    'code' => '<script>window.viewModel = @json($viewModel);</script>',
                    'note' => 'Serialize data as JSON rather than concatenating strings inside script tags.',
                ],
                [
                    'label' => 'Safe DOM text update',
                    'code' => 'target.textContent = viewModel.displayName;',
                    'note' => 'Use textContent for user-visible text in the browser.',
                ],
            ],
            'url' => [
                [
                    'label' => 'Validated URL render',
                    'code' => '<a href="{{ $safeProfileUrl }}">Profile</a>',
                    'note' => 'Prepare $safeProfileUrl after validating the scheme and host allowlist.',
                ],
                [
                    'label' => 'Route-generated URL',
                    'code' => '<a href="{{ route("profiles.show", $user) }}">Profile</a>',
                    'note' => 'Prefer framework-generated URLs when linking to application routes.',
                ],
            ],
            'rich_text' => [
                [
                    'label' => 'Sanitized rich text boundary',
                    'code' => '{!! $sanitizedHtml !!}',
                    'note' => 'Only render raw HTML after sanitizer allowlists tags and attributes.',
                ],
                [
                    'label' => 'Plain-text fallback',
                    'code' => '{{ $comment->body }}',
                    'note' => 'Use escaped text when rich formatting is not a product requirement.',
                ],
            ],
            default => [
                [
                    'label' => 'Escaped Blade text',
                    'code' => '{{ $comment->body }}',
                    'note' => 'Blade escaping keeps user text visible without treating it as HTML.',
                ],
                [
                    'label' => 'Escaped component prop',
                    'code' => '<x-profile-card :title="$profile->display_name" />',
                    'note' => 'Keep the component responsible for escaped text rendering.',
                ],
            ],
        };
    }

    /**
     * Return the first rule to apply for the selected rendering context.
     */
    private function contextRule(string $context): string
    {
        return match ($context) {
            'html_attribute' => 'Escape attribute values and avoid building whole attributes from user input.',
            'javascript_data' => 'Serialize data with @json or Js::from instead of concatenating strings inside script tags.',
            'url' => 'Validate URL schemes with an allowlist before rendering href or src.',
            'rich_text' => 'Sanitize rich text with an allowlist before any raw HTML rendering.',
            default => 'Render user-provided text with escaped Blade output.',
        };
    }

    /**
     * Suggest payload tests for the selected context.
     *
     * @return array<int, string>
     */
    private function payloadTests(string $context): array
    {
        return match ($context) {
            'html_attribute' => ['" onmouseover="alert(1)', "' autofocus onfocus='alert(1)", '<img src=x onerror=alert(1)>'],
            'javascript_data' => ["</script><script>alert('xss')</script>", "';alert(1);//", '<img src=x onerror=alert(1)>'],
            'url' => ['javascript:alert(1)', 'data:text/html,<script>alert(1)</script>', 'https://example.com/profile'],
            'rich_text' => ['<script>alert(1)</script>', '<svg onload=alert(1)>', '<iframe srcdoc="<script>alert(1)</script>"></iframe>'],
            default => ['<script>alert(1)</script>', '<img src=x onerror=alert(1)>', 'plain visible text'],
        };
    }

    /**
     * Build concrete feature-test scenarios from context and detected flags.
     *
     * @param  array<int, string>  $riskFlags
     * @return array<int, array{name: string, payload: string, expected_assertion: string, purpose: string}>
     */
    private function testPlan(string $context, array $riskFlags): array
    {
        $payloads = $this->payloadTests($context);
        $primaryFlag = $riskFlags[0] ?? 'no-risk-flag';

        return [
            [
                'name' => sprintf('blocks_%s_payload_in_%s_context', str_replace('-', '_', $primaryFlag), $context),
                'payload' => $payloads[0],
                'expected_assertion' => $riskFlags === []
                    ? 'assertJsonPath("data.release_decision.status", "ready")'
                    : sprintf('assertJsonPath("data.risk_flags.0", "%s")', $primaryFlag),
                'purpose' => 'Prove the most relevant payload is classified before release.',
            ],
            [
                'name' => sprintf('escapes_visible_output_in_%s_context', $context),
                'payload' => $payloads[1] ?? $payloads[0],
                'expected_assertion' => 'assertJsonPath("data.escaped.body", escaped payload text)',
                'purpose' => 'Prove user input remains visible data instead of executable browser code.',
            ],
            [
                'name' => sprintf('returns_context_review_checklist_for_%s', $context),
                'payload' => $payloads[2] ?? $payloads[0],
                'expected_assertion' => 'assertJsonStructure(["data" => ["review_checklist", "variant_review", "interview_answer_outline"]])',
                'purpose' => 'Prove the reviewer receives context-specific follow-up work.',
            ],
        ];
    }

    /**
     * Count findings by severity for quick review decisions.
     *
     * @param  array<int, array{flag: string, severity: string, evidence: string, fix: string}>  $findings
     * @return array{high: int, medium: int, low: int}
     */
    private function severitySummary(array $findings): array
    {
        return [
            'high' => collect($findings)->where('severity', 'high')->count(),
            'medium' => collect($findings)->where('severity', 'medium')->count(),
            'low' => collect($findings)->where('severity', 'low')->count(),
        ];
    }

    /**
     * Convert severity counts into a simple release gate.
     *
     * @param  array{high: int, medium: int, low: int}  $summary
     * @return array{status: string, reason: string}
     */
    private function releaseDecision(array $summary): array
    {
        if ($summary['high'] > 0) {
            return [
                'status' => 'block',
                'reason' => 'High-risk executable browser payloads must be escaped, sanitized, or removed before release.',
            ];
        }

        if ($summary['medium'] > 0) {
            return [
                'status' => 'review',
                'reason' => 'Medium-risk active HTML or DOM-sink signals need reviewer confirmation and payload tests.',
            ];
        }

        return [
            'status' => 'ready',
            'reason' => 'No known executable browser payload pattern was detected in this preview.',
        ];
    }

    /**
     * Build a focused review checklist for the selected output context.
     *
     * @param  array<int, array{flag: string, severity: string, evidence: string, fix: string}>  $findings
     * @return array<int, array{label: string, status: string, file_hint: string}>
     */
    private function reviewChecklist(string $context, array $findings): array
    {
        $hasFindings = $findings !== [];
        $contextChecklist = match ($context) {
            'html_attribute' => [
                [
                    'label' => 'Confirm attributes are not assembled from untrusted strings.',
                    'status' => $hasFindings ? 'review' : 'ready',
                    'file_hint' => 'resources/views/**/*.blade.php attribute bindings',
                ],
            ],
            'javascript_data' => [
                [
                    'label' => 'Confirm server data reaches scripts through @json or Js::from.',
                    'status' => $hasFindings ? 'review' : 'ready',
                    'file_hint' => 'Blade script blocks and frontend hydration payloads',
                ],
            ],
            'url' => [
                [
                    'label' => 'Confirm href and src values use an allowed URL scheme.',
                    'status' => $hasFindings ? 'review' : 'ready',
                    'file_hint' => 'request validation rules and link-rendering views',
                ],
            ],
            'rich_text' => [
                [
                    'label' => 'Confirm rich text is sanitized before any raw HTML rendering.',
                    'status' => $hasFindings ? 'review' : 'ready',
                    'file_hint' => 'sanitizer service and `{!! !!}` Blade call sites',
                ],
            ],
            default => [
                [
                    'label' => 'Confirm user-provided text is rendered with escaped Blade output.',
                    'status' => $hasFindings ? 'review' : 'ready',
                    'file_hint' => 'resources/views/**/*.blade.php text output',
                ],
            ],
        };

        return [
            ...$contextChecklist,
            [
                'label' => 'Add payload tests for every reported risk flag.',
                'status' => $hasFindings ? 'required' : 'optional',
                'file_hint' => 'tests/Feature/*Xss*Test.php or the nearest feature test',
            ],
            [
                'label' => 'Document why CSP is defense-in-depth rather than the primary fix.',
                'status' => 'optional',
                'file_hint' => 'security review notes or pull-request checklist',
            ],
        ];
    }

    /**
     * Build prioritized remediation steps from severity and rendering context.
     *
     * @param  array<int, array{flag: string, severity: string, evidence: string, fix: string}>  $findings
     * @param  array{high: int, medium: int, low: int}  $summary
     * @return array<int, array{priority: int, action: string, owner: string, done_when: string}>
     */
    private function remediationSteps(string $context, array $findings, array $summary): array
    {
        $steps = [];

        if ($summary['high'] > 0) {
            $steps[] = [
                'priority' => 1,
                'action' => 'Block release and replace executable payload rendering with escaped output, sanitizer removal, or URL scheme allowlist.',
                'owner' => 'feature owner',
                'done_when' => 'High-risk flags disappear or are proven harmless by context-specific tests.',
            ];
        }

        if ($summary['medium'] > 0) {
            $steps[] = [
                'priority' => count($steps) + 1,
                'action' => 'Review active HTML and DOM-sink usage with a second engineer before relying on sanitizer or frontend rendering behavior.',
                'owner' => 'security reviewer',
                'done_when' => 'The DOM or rich-text path has a documented safe rendering boundary.',
            ];
        }

        $steps[] = [
            'priority' => count($steps) + 1,
            'action' => sprintf('Apply the `%s` context rule and update the nearest Blade, service, or frontend component.', $context),
            'owner' => 'implementer',
            'done_when' => $this->contextRule($context),
        ];

        $steps[] = [
            'priority' => count($steps) + 1,
            'action' => 'Add payload tests for reflected, stored, and DOM-style paths before marking the issue resolved.',
            'owner' => 'test owner',
            'done_when' => $findings === []
                ? 'Tests prove safe text remains safe and future regressions still run payload checks.'
                : sprintf('Tests cover these risk flags: %s.', collect($findings)->pluck('flag')->implode(', ')),
        ];

        return $steps;
    }

    /**
     * Build prompts that connect payload findings to reflected, stored, and DOM XSS review.
     *
     * @param  array<int, array{flag: string, severity: string, evidence: string, fix: string}>  $findings
     * @return array<int, array{variant: string, review_prompt: string, likely_files: string}>
     */
    private function variantReview(array $findings): array
    {
        $flags = collect($findings)->pluck('flag');

        return [
            [
                'variant' => 'reflected-xss',
                'review_prompt' => 'Can this payload come directly from query string, request input, validation error text, or flash message output?',
                'likely_files' => 'Form requests, controllers, redirect responses, Blade forms, and alert partials.',
            ],
            [
                'variant' => 'stored-xss',
                'review_prompt' => 'Can this payload be saved to the database and later rendered for another user?',
                'likely_files' => 'Models, persistence services, admin forms, profile/comment views, and moderation flows.',
            ],
            [
                'variant' => 'dom-xss',
                'review_prompt' => $flags->contains('unsafe-dom-sink')
                    ? 'DOM sink signal found. Replace innerHTML/document.write style updates with textContent, safe templates, or sanitized HTML.'
                    : 'Check whether frontend JavaScript later inserts this value into innerHTML, document.write, or unsafe template strings.',
                'likely_files' => 'Blade script blocks, frontend components, hydration payloads, and browser event handlers.',
            ],
        ];
    }

    /**
     * Build a concise interview answer outline from the preview result.
     *
     * @param  array<int, array{flag: string, severity: string, evidence: string, fix: string}>  $findings
     * @param  array{high: int, medium: int, low: int}  $summary
     * @return array<int, string>
     */
    private function interviewAnswerOutline(string $context, array $findings, array $summary): array
    {
        $riskSummary = $findings === []
            ? 'No executable browser payload pattern was detected in this sample.'
            : sprintf('The sample has %d high-risk and %d medium-risk XSS signal(s).', $summary['high'], $summary['medium']);

        return [
            'XSS means untrusted data reaches a browser context where it can execute instead of render as data.',
            sprintf('For this preview, the rendering context is `%s`; the first rule is: %s', $context, $this->contextRule($context)),
            $riskSummary,
            'The primary fix is context-aware escaping or sanitization at the rendering boundary; CSP is only defense-in-depth.',
            'I would prove the fix with payload tests for reflected, stored, and DOM-style rendering paths.',
        ];
    }

    /**
     * Allow only browser-safe URL schemes for preview output.
     */
    private function safeUrl(string $value): string
    {
        $trimmed = trim($value);
        $scheme = parse_url($trimmed, PHP_URL_SCHEME);

        if ($scheme === null || in_array(Str::lower((string) $scheme), ['http', 'https', 'mailto'], true)) {
            return e($trimmed);
        }

        return '#blocked-unsafe-url';
    }
}

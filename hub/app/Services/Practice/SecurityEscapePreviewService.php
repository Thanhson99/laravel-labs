<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class SecurityEscapePreviewService
{
    /**
     * Build an escaped preview and security checklist for learner-provided content.
     *
     * @param  array{title: string, body: string}  $content
     * @return array{title: string, body: string, escaped: array{title: string, body: string}, risk_flags: array<int, string>, blade_rules: array<int, string>, next_action: string}
     */
    public function preview(array $content): array
    {
        $title = trim($content['title']);
        $body = trim($content['body']);
        $riskFlags = $this->riskFlags($title.' '.$body);

        return [
            'title' => $title,
            'body' => $body,
            'escaped' => [
                'title' => e($title),
                'body' => e($body),
            ],
            'risk_flags' => $riskFlags,
            'blade_rules' => [
                'Use `{{ $value }}` for user-provided text.',
                'Avoid `{!! $value !!}` unless the HTML source is trusted and sanitized.',
                'Validate input shape before rendering it.',
                'Keep escaping decisions visible in the Blade view.',
            ],
            'next_action' => $riskFlags === []
                ? 'Render with escaped Blade output and keep the feature test focused on visible text.'
                : 'Render escaped output, then add a test proving script-like content is not executed as HTML.',
        ];
    }

    /**
     * Detect common script-like patterns that should be treated as unsafe user content.
     *
     * @return array<int, string>
     */
    private function riskFlags(string $value): array
    {
        $lowerValue = Str::lower($value);
        $flags = [];

        if (str_contains($lowerValue, '<script')) {
            $flags[] = 'script-tag';
        }

        if (str_contains($lowerValue, 'onerror=') || str_contains($lowerValue, 'onclick=')) {
            $flags[] = 'inline-event-handler';
        }

        if (str_contains($lowerValue, 'javascript:')) {
            $flags[] = 'javascript-url';
        }

        return $flags;
    }
}

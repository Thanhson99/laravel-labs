<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class CsrfProtectionPlanService
{
    /**
     * Build a practical CSRF protection plan for browser-facing flows.
     *
     * @param  array{flow_name: string, client_type: string, state_changing_method: string, has_csrf_token: bool, same_site: string, uses_cookie_auth: bool}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $flow = Str::studly($input['flow_name']);
        $risk = $this->riskScore($input);
        $recommendation = $this->recommendation($input, $risk);
        $controls = $this->controls($input);
        $testMatrix = $this->testMatrix($input);
        $featureTestSnippet = $this->featureTestSnippet($input, $flow);
        $interviewAnswer = $this->interviewAnswer();

        return [
            'flow' => $flow,
            'risk_score' => $risk,
            'recommendation' => $recommendation,
            'attack_flow' => $this->attackFlow($input),
            'controls' => $controls,
            'same_site_review' => $this->sameSiteReview($input),
            'test_matrix' => $testMatrix,
            'feature_test_snippet' => $featureTestSnippet,
            'review_questions' => $this->reviewQuestions($input),
            'interview_answer' => $interviewAnswer,
            'review_packet_markdown' => $this->reviewPacketMarkdown(
                $flow,
                $risk,
                $recommendation,
                $controls,
                $testMatrix,
                $featureTestSnippet,
                $interviewAnswer,
            ),
            'commands' => [
                'php artisan test --filter CsrfProtectionPlan',
                'php artisan route:list --path=csrf-protection-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * @param  array{client_type: string, state_changing_method: string, has_csrf_token: bool, same_site: string, uses_cookie_auth: bool}  $input
     * @return array{score: int, label: string, reasons: array<int, string>}
     */
    private function riskScore(array $input): array
    {
        $score = 10;
        $reasons = [];

        if ($input['uses_cookie_auth']) {
            $score += 25;
            $reasons[] = 'Browser cookies are sent automatically, so unwanted cross-site requests are relevant.';
        }

        if (! $input['has_csrf_token']) {
            $score += 40;
            $reasons[] = 'The state-changing flow has no CSRF token proof.';
        }

        if ($input['same_site'] === 'none') {
            $score += 20;
            $reasons[] = 'SameSite=None allows cross-site cookie sending and needs stronger CSRF evidence.';
        }

        if ($input['state_changing_method'] === 'get') {
            $score += 20;
            $reasons[] = 'GET should not change server state because it is easy to trigger cross-site.';
        }

        $score = min(100, $score);

        return [
            'score' => $score,
            'label' => match (true) {
                $score >= 75 => 'critical',
                $score >= 55 => 'high',
                $score >= 30 => 'medium',
                default => 'low',
            },
            'reasons' => $reasons,
        ];
    }

    /**
     * @param  array{has_csrf_token: bool, uses_cookie_auth: bool, state_changing_method: string}  $input
     * @param  array{label: string}  $risk
     */
    private function recommendation(array $input, array $risk): string
    {
        if ($input['state_changing_method'] === 'get') {
            return 'Move the action to POST, PUT, PATCH, or DELETE before relying on CSRF controls.';
        }

        if ($input['uses_cookie_auth'] && ! $input['has_csrf_token']) {
            return 'Add Laravel CSRF token validation before this cookie-authenticated state-changing flow ships.';
        }

        return "Risk is {$risk['label']}; keep token validation, SameSite cookies, origin checks for sensitive flows, and feature tests aligned.";
    }

    /**
     * @param  array{client_type: string}  $input
     * @return array<int, string>
     */
    private function attackFlow(array $input): array
    {
        return [
            "A victim is logged in to the {$input['client_type']} flow and their browser holds a session cookie.",
            'An attacker page tricks the browser into submitting a state-changing request to the trusted app.',
            'The browser automatically attaches eligible cookies, so the server may see an authenticated request.',
            'A valid CSRF token proves the request came through the intended app flow, not only from a browser with cookies.',
        ];
    }

    /**
     * @param  array{client_type: string, same_site: string, uses_cookie_auth: bool}  $input
     * @return array<int, array{control: string, purpose: string}>
     */
    private function controls(array $input): array
    {
        $controls = [
            [
                'control' => 'CSRF token',
                'purpose' => 'Bind the form or stateful AJAX request to the user session and reject missing or stale tokens.',
            ],
            [
                'control' => 'SameSite cookie',
                'purpose' => "Current setting: {$input['same_site']}. Use Lax or Strict where possible to reduce cross-site cookie sending.",
            ],
            [
                'control' => 'Safe HTTP method boundary',
                'purpose' => 'Keep GET read-only and require POST, PUT, PATCH, or DELETE for state changes.',
            ],
        ];

        if ($input['client_type'] === 'spa-stateful') {
            $controls[] = [
                'control' => 'Sanctum CSRF cookie bootstrap',
                'purpose' => 'Fetch `/sanctum/csrf-cookie`, send credentials, and include the XSRF header on stateful requests.',
            ];
        }

        if ($input['uses_cookie_auth']) {
            $controls[] = [
                'control' => 'Origin and Referer review',
                'purpose' => 'Use as additional evidence for highly sensitive cookie-authenticated actions, not as the only protection.',
            ];
        }

        return $controls;
    }

    /**
     * @param  array{same_site: string}  $input
     * @return array{setting: string, guidance: string, caution: string}
     */
    private function sameSiteReview(array $input): array
    {
        return match ($input['same_site']) {
            'strict' => [
                'setting' => 'Strict',
                'guidance' => 'Strongest default for same-site-only app flows.',
                'caution' => 'May break legitimate cross-site entry points after external redirects.',
            ],
            'none' => [
                'setting' => 'None',
                'guidance' => 'Use only when cross-site cookie behavior is truly required and Secure is enabled.',
                'caution' => 'Needs explicit CSRF token evidence because cookies can travel cross-site.',
            ],
            'missing' => [
                'setting' => 'Missing',
                'guidance' => 'Set SameSite deliberately instead of inheriting accidental browser defaults.',
                'caution' => 'Review framework, proxy, and browser behavior before production.',
            ],
            default => [
                'setting' => 'Lax',
                'guidance' => 'Good default for many session-based web apps.',
                'caution' => 'Still keep CSRF tokens for state-changing requests.',
            ],
        };
    }

    /**
     * @param  array{state_changing_method: string, has_csrf_token: bool}  $input
     * @return array<int, array{case: string, expected: string}>
     */
    private function testMatrix(array $input): array
    {
        $matrix = [
            [
                'case' => 'valid token on state-changing request',
                'expected' => 'Request succeeds and updates only the intended resource.',
            ],
            [
                'case' => 'missing CSRF token',
                'expected' => 'Laravel rejects the request with 419 for web flow or the configured API error shape.',
            ],
            [
                'case' => 'stale token after logout or session rotation',
                'expected' => 'Request is rejected and does not mutate server state.',
            ],
        ];

        if ($input['state_changing_method'] === 'get') {
            $matrix[] = [
                'case' => 'GET request attempts a state change',
                'expected' => 'Route is refactored to a non-GET method or blocked before merge.',
            ];
        }

        if (! $input['has_csrf_token']) {
            $matrix[] = [
                'case' => 'cross-site form post without token',
                'expected' => 'Request is rejected even when the victim has a valid session cookie.',
            ];
        }

        return $matrix;
    }

    /**
     * @param  array{state_changing_method: string}  $input
     */
    private function featureTestSnippet(array $input, string $flow): string
    {
        $method = $input['state_changing_method'] === 'get' ? 'post' : $input['state_changing_method'];
        $methodSuffix = Str::of($flow)->snake()->lower()->toString();

        return <<<PHP
public function test_{$methodSuffix}_requires_csrf_protection(): void
{
    \$user = User::factory()->create();

    \$this->actingAs(\$user)
        ->{$method}('/profile', ['name' => 'Changed'])
        ->assertStatus(419);

    \$this->withSession(['_token' => 'known-token'])
        ->actingAs(\$user)
        ->{$method}('/profile', ['_token' => 'known-token', 'name' => 'Changed'])
        ->assertRedirect();
}
PHP;
    }

    /**
     * @param  array{client_type: string, same_site: string, uses_cookie_auth: bool}  $input
     * @return array<int, string>
     */
    private function reviewQuestions(array $input): array
    {
        return [
            "Is this flow really {$input['client_type']}, or should it be stateless API auth instead?",
            "Which cookie carries identity, and what is its SameSite setting ({$input['same_site']})?",
            'Where is the CSRF token generated, rendered, sent, and verified?',
            $input['uses_cookie_auth']
                ? 'Do tests prove a browser with cookies but no token cannot change state?'
                : 'If this is bearer-token API auth, why is CSRF not the primary control?',
        ];
    }

    private function interviewAnswer(): string
    {
        return 'CSRF tricks a logged-in browser into sending an unwanted state-changing request. It matters most when authentication depends on cookies because browsers attach eligible cookies automatically. A CSRF token proves the request came from the application flow tied to the user session. SameSite cookies reduce when cookies travel cross-site, but they do not replace token validation for sensitive state changes.';
    }

    /**
     * @param  array{score: int, label: string, reasons: array<int, string>}  $risk
     * @param  array<int, array{control: string, purpose: string}>  $controls
     * @param  array<int, array{case: string, expected: string}>  $testMatrix
     */
    private function reviewPacketMarkdown(
        string $flow,
        array $risk,
        string $recommendation,
        array $controls,
        array $testMatrix,
        string $featureTestSnippet,
        string $interviewAnswer,
    ): string {
        $riskReasons = $risk['reasons'] === []
            ? ['No high-risk CSRF signal was detected, but state-changing flows still need tests.']
            : $risk['reasons'];

        return implode("\n", [
            "# CSRF Protection Packet: {$flow}",
            '',
            '## Risk',
            "- Level: {$risk['label']} ({$risk['score']}/100)",
            ...$this->markdownBullets($riskReasons),
            '',
            '## Recommendation',
            $recommendation,
            '',
            '## Controls',
            ...array_map(fn (array $item): string => "- {$item['control']}: {$item['purpose']}", $controls),
            '',
            '## Test Matrix',
            ...array_map(fn (array $item): string => "- {$item['case']}: {$item['expected']}", $testMatrix),
            '',
            '## Feature Test Snippet',
            '```php',
            $featureTestSnippet,
            '```',
            '',
            '## Interview Answer',
            $interviewAnswer,
        ]);
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function markdownBullets(array $items): array
    {
        return array_map(fn (string $item): string => "- {$item}", $items);
    }
}

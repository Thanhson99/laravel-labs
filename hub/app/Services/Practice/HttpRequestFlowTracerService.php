<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class HttpRequestFlowTracerService
{
    /**
     * Build a readable Laravel request-flow trace from learner input.
     *
     * @return array{request: array<string, string>, steps: array<int, array<string, string>>, next_practice: array<int, string>}
     */
    public function trace(string $method, string $path, string $accept): array
    {
        $normalizedMethod = mb_strtoupper($method);
        $normalizedPath = '/'.ltrim(trim($path), '/');
        $normalizedAccept = trim($accept) !== '' ? trim($accept) : 'text/html';

        return [
            'request' => [
                'method' => $normalizedMethod,
                'path' => $normalizedPath,
                'accept' => $normalizedAccept,
            ],
            'steps' => [
                $this->step('01', 'Public entry', 'The web server sends every Laravel request through `public/index.php`.'),
                $this->step('02', 'Bootstrap', '`bootstrap/app.php` creates the application, middleware stack, and route handling pipeline.'),
                $this->step('03', 'Route match', "Laravel matches {$normalizedMethod} {$normalizedPath} against the route files under `routes/`."),
                $this->step('04', 'Request validation', 'A Form Request should own validation when input rules are more than trivial.'),
                $this->step('05', 'Controller', 'The controller should orchestrate the request and delegate actual workflow decisions.'),
                $this->step('06', 'Service', 'A service should hold reusable workflow logic, transformations, and business decisions.'),
                $this->step('07', 'Response', $this->responseDescription($normalizedAccept)),
            ],
            'next_practice' => [
                'Open `routes/web/workbench.php` and find the page route.',
                'Open `routes/api/practice-actions.php` and find the JSON trace route.',
                'Change one trace step in `HttpRequestFlowTracerService` and run the focused test.',
            ],
        ];
    }

    /**
     * Create one trace step.
     *
     * @return array{number: string, title: string, explanation: string}
     */
    private function step(string $number, string $title, string $explanation): array
    {
        return [
            'number' => $number,
            'title' => $title,
            'explanation' => $explanation,
        ];
    }

    /**
     * Explain the response type based on the Accept header.
     */
    private function responseDescription(string $accept): string
    {
        if (str_contains(mb_strtolower($accept), 'application/json')) {
            return 'The endpoint should return a stable JSON shape, usually with `data`, `meta`, or `message` keys.';
        }

        return 'The endpoint can return a Blade view, redirect, streamed file, or another Symfony response type.';
    }
}

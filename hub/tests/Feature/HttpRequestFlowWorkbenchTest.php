<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class HttpRequestFlowWorkbenchTest extends TestCase
{
    /**
     * The HTTP request-flow workbench renders the code-reading loop.
     */
    public function test_http_request_flow_workbench_renders(): void
    {
        $response = $this->get('/workbench/http-request-flow');

        $response
            ->assertOk()
            ->assertSee('HTTP Request Flow Workbench')
            ->assertSee('Trace a Laravel request through real app layers.')
            ->assertSee('TraceHttpRequestFlowRequest')
            ->assertSee('HttpRequestFlowTracerService')
            ->assertSee('Trace request');
    }

    /**
     * The request-flow API returns a stable layer trace.
     */
    public function test_http_request_flow_api_returns_trace(): void
    {
        $response = $this->postJson('/api/practice/http-request-flow', [
            'method' => 'POST',
            'path' => '/api/practice/topics',
            'accept' => 'application/json',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.request.method', 'POST')
            ->assertJsonPath('data.request.path', '/api/practice/topics')
            ->assertJsonPath('data.steps.0.title', 'Public entry')
            ->assertJsonPath('data.steps.6.title', 'Response')
            ->assertJsonPath('data.next_practice.0', 'Open `routes/web/workbench.php` and find the page route.');
    }

    /**
     * Invalid trace payloads return Laravel validation errors.
     */
    public function test_http_request_flow_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/http-request-flow', [
            'method' => 'TRACE',
            'path' => '<script>',
            'accept' => 'application/xml',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['method', 'path', 'accept']);
    }
}

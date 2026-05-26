@extends('learning.layout', ['title' => 'Implementation Verification Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Verification plan</span>
        <h1>Verification commands for code generated from one question.</h1>
        <p>
            This plan uses the record workspace to produce focused tests, route checks,
            smoke requests, the full suite command, Pint, and a quality-gate payload.
        </p>
    </section>

    @if ($plan)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $plan['technology'] }}</span>
                    <span class="muted">{{ $plan['source']['path'] }}</span>
                </div>
                <h2>{{ $plan['title'] }}</h2>
                <a class="button" href="{{ route('practice.record-workspace', request()->query()) }}">Open record workspace</a>
                <a class="button" href="{{ route('api.practice.verification-plan', request()->query()) }}">Open verification API</a>
            </article>
        </section>

        <section class="section">
            <h2>Commands</h2>
            <div class="list">
                @foreach ($plan['commands'] as $command)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $command['label'] }}</span>
                        </div>
                        <pre><code>{{ $command['command'] }}</code></pre>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Smoke Request</h2>
                    <pre><code>{{ json_encode($plan['smoke_request'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
                <article class="panel">
                    <h2>Quality Gate Payload</h2>
                    <pre><code>{{ json_encode($plan['quality_gate_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No verification plan found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

@extends('learning.layout', ['title' => 'Configuration Readiness'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration</span>
        <h1>{{ $readiness['title'] }}</h1>
        <p>{{ $readiness['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Quality status: {{ $readiness['quality_gate']['status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-readiness') }}">Open readiness API</a>
            <a class="button primary" href="{{ route('practice.configuration-test-plan') }}">Open test plan</a>
            <a class="button" href="{{ route('practice.technology-quality-plan') }}">Open technology quality plan</a>
        </div>

        <div class="list">
            @foreach ($readiness['checks'] as $check)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $check['key'] }}</span>
                        <span class="muted">{{ $check['passed'] ? 'passed' : 'needs work' }}</span>
                        <span class="muted">{{ $check['file'] }}</span>
                    </div>
                    <h3>{{ $check['label'] }}</h3>
                    <p><code>{{ is_bool($check['value']) ? ($check['value'] ? 'true' : 'false') : $check['value'] }}</code></p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($readiness['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Next Practice Steps</h2>
        <div class="list">
            @foreach ($readiness['next_steps'] as $step)
                <article class="item">
                    <p>{{ $step }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection

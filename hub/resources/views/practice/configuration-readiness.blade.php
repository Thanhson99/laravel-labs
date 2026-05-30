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
        <h2>Security Misconfiguration Controls</h2>
        <div class="list">
            @foreach ($readiness['misconfiguration_controls'] as $control)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $control['area'] }}</span>
                        <span class="muted">{{ $control['owner'] }}</span>
                    </div>
                    <h3>{{ $control['expected_control'] }}</h3>
                    <p>{{ $control['risk'] }}</p>
                    <p class="muted">{{ $control['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Deployment Smoke Matrix</h2>
        <div class="list">
            @foreach ($readiness['deployment_smoke_matrix'] as $smoke)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $smoke['check'] }}</span>
                    </div>
                    <h3>{{ $smoke['fail_closed_action'] }}</h3>
                    <p>{{ $smoke['unsafe_signal'] }}</p>
                    <p class="muted">{{ $smoke['verify_with'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Release Blockers</h2>
        <div class="panel">
            <ul>
                @foreach ($readiness['release_blockers'] as $blocker)
                    <li>{{ $blocker }}</li>
                @endforeach
            </ul>
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

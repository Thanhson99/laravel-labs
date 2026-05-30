@extends('learning.layout', ['title' => 'Configuration Deployment Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration deploy</span>
        <h1>{{ $deploymentPlan['title'] }}</h1>
        <p>{{ $deploymentPlan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Quality status: {{ $deploymentPlan['quality_gate']['status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-deployment-plan') }}">Open deployment API</a>
            <a class="button primary" href="{{ route('practice.configuration-release-evidence') }}">Open release evidence</a>
            <a class="button" href="{{ route('practice.configuration-change-checklist') }}">Open change checklist</a>
        </div>

        <div class="list">
            @foreach ($deploymentPlan['deploy_steps'] as $step)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $step['step'] }}</span>
                    </div>
                    <h3>{{ $step['label'] }}</h3>
                    @if ($step['command'])
                        <p><code>{{ $step['command'] }}</code></p>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Preflight</h2>
        <div class="list">
            @foreach ($deploymentPlan['preflight'] as $item)
                <article class="item">
                    <h3>{{ $item['label'] }}</h3>
                    <p class="muted">{{ $item['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Smoke Checks</h2>
        <div class="list">
            @foreach ($deploymentPlan['smoke_checks'] as $check)
                <article class="item">
                    <h3><code>{{ $check['endpoint'] }}</code></h3>
                    <p>{{ $check['expected'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Security Misconfiguration Controls</h2>
        <div class="list">
            @foreach ($deploymentPlan['security_misconfiguration_controls'] as $control)
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
        <h2>Release Blockers</h2>
        <div class="panel">
            <ul>
                @foreach ($deploymentPlan['release_blockers'] as $blocker)
                    <li>{{ $blocker }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Rollback</h2>
        <div class="list">
            @foreach ($deploymentPlan['rollback_steps'] as $step)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $step['area'] }}</span>
                    </div>
                    <p>{{ $step['action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($deploymentPlan['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Configuration Remediation Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration remediation</span>
        <h1>{{ $remediationPlan['title'] }}</h1>
        <p>{{ $remediationPlan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $remediationPlan['task_count'] }} tasks from {{ $remediationPlan['risk_count'] }} risks</h2>
            <a class="button" href="{{ route('api.practice.configuration-remediation-plan') }}">Open remediation API</a>
            <a class="button primary" href="{{ route('practice.configuration-pull-request-plan') }}">Open PR plan</a>
            <a class="button" href="{{ route('practice.configuration-risk-register') }}">Open risk register</a>
        </div>

        <div class="list">
            @foreach ($remediationPlan['tasks'] as $task)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $task['severity'] }}</span>
                        <span class="muted">{{ $task['risk_key'] }}</span>
                    </div>
                    <h3>{{ $task['title'] }}</h3>
                    <p>{{ $task['action'] }}</p>

                    <h4>Target files</h4>
                    <ul>
                        @foreach ($task['target_files'] as $file)
                            <li><code>{{ $file }}</code></li>
                        @endforeach
                    </ul>

                    <h4>Verification</h4>
                    <ul>
                        @foreach ($task['verification'] as $command)
                            <li><code>{{ $command }}</code></li>
                        @endforeach
                    </ul>

                    <p class="muted">{{ $task['done_signal'] }}</p>
                    <a class="button" href="{{ $task['owner_route'] }}">Open owner stage</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Completion Criteria</h2>
        <div class="panel">
            <ul>
                @foreach ($remediationPlan['completion_criteria'] as $criterion)
                    <li>{{ $criterion }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($remediationPlan['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

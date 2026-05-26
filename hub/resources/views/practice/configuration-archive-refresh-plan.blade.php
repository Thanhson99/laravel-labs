@extends('learning.layout', ['title' => 'Configuration Archive Refresh Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration refresh</span>
        <h1>{{ $refreshPlan['title'] }}</h1>
        <p>{{ $refreshPlan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $refreshPlan['refresh_status'] }}: {{ $refreshPlan['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-archive-refresh-plan') }}">Open refresh API</a>
            <a class="button" href="{{ route('practice.configuration-session-archive') }}">Open session archive</a>
            <a class="button" href="{{ route('practice.configuration-maintenance-roadmap') }}">Open maintenance roadmap</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Refresh Triggers</h2>
        <div class="panel">
            <ul>
                @foreach ($refreshPlan['refresh_triggers'] as $trigger)
                    <li>{{ $trigger }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Refresh Tasks</h2>
        <div class="list">
            @foreach ($refreshPlan['refresh_tasks'] as $task)
                <article class="item">
                    <span class="badge">{{ $task['current_status'] }}</span>
                    <h3>{{ $task['evidence'] }}</h3>
                    <p>{{ $task['refresh_action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Rerun Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($refreshPlan['rerun_commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Remediation Triggers</h2>
        <div class="panel">
            <ul>
                @foreach ($refreshPlan['remediation_triggers'] as $trigger)
                    <li>{{ $trigger }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

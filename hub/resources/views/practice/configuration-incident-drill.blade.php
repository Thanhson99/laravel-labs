@extends('learning.layout', ['title' => 'Configuration Incident Drill'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $incidentDrill['incident_id'] }}</span>
        <h1>{{ $incidentDrill['title'] }}</h1>
        <p>{{ $incidentDrill['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $incidentDrill['runbook']['runbook_id'] }}: {{ $incidentDrill['runbook']['decision_record'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-incident-drill') }}">Open incident API</a>
            <a class="button" href="{{ route('practice.configuration-operations-runbook') }}">Open runbook</a>
            <a class="button" href="{{ route('practice.configuration-incident-postmortem') }}">Open postmortem</a>
            <a class="button primary" href="{{ route('practice.configuration-deployment-plan') }}">Open deployment plan</a>
        </div>
    </section>

    <section class="section">
        <h2>Scenario</h2>
        <div class="panel">
            <ul>
                @foreach ($incidentDrill['scenario'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Timeline</h2>
        <div class="list">
            @foreach ($incidentDrill['timeline'] as $event)
                <article class="item">
                    <span class="badge">Minute {{ $event['minute'] }}</span>
                    <h3>{{ $event['event'] }}</h3>
                    <p>{{ $event['action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Diagnosis Steps</h2>
        <div class="list">
            @foreach ($incidentDrill['diagnosis_steps'] as $step)
                <article class="item">
                    <span class="badge">Step {{ $step['step'] }}</span>
                    <h3>{{ $step['task'] }}</h3>
                    <p><code>{{ $step['command'] }}</code></p>
                    <p>{{ $step['success_signal'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Patch Plan</h2>
        <div class="panel">
            <ul>
                @foreach ($incidentDrill['patch_plan'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

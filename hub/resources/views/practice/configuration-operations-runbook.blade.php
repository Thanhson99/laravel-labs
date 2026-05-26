@extends('learning.layout', ['title' => 'Configuration Operations Runbook'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $runbook['runbook_id'] }}</span>
        <h1>{{ $runbook['title'] }}</h1>
        <p>{{ $runbook['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $runbook['decision_record']['record_id'] }}: {{ $runbook['decision_record']['status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-operations-runbook') }}">Open runbook API</a>
            <a class="button" href="{{ route('practice.configuration-decision-record') }}">Open decision record</a>
            <a class="button" href="{{ route('practice.configuration-incident-drill') }}">Open incident drill</a>
            <a class="button primary" href="{{ route('practice.configuration-deployment-plan') }}">Open deployment plan</a>
        </div>
    </section>

    <section class="section">
        <h2>Triggers</h2>
        <div class="panel">
            <ul>
                @foreach ($runbook['triggers'] as $trigger)
                    <li>{{ $trigger }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Diagnostics</h2>
        <div class="list">
            @foreach ($runbook['diagnostics'] as $item)
                <article class="item">
                    <span class="badge">Step {{ $item['step'] }}</span>
                    <h3>{{ $item['check'] }}</h3>
                    <p><code>{{ $item['command'] }}</code></p>
                    <p>{{ $item['expected'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Rollback</h2>
        <div class="panel">
            <ul>
                @foreach ($runbook['rollback'] as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Handoff</h2>
        <div class="panel">
            <ul>
                @foreach ($runbook['handoff'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Configuration Decision Record'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $decisionRecord['record_id'] }}</span>
        <h1>{{ $decisionRecord['title'] }}</h1>
        <p>{{ $decisionRecord['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $decisionRecord['status'] }}: {{ $decisionRecord['assessment']['score'] }} / 100</h2>
            <a class="button" href="{{ route('api.practice.configuration-decision-record') }}">Open decision API</a>
            <a class="button" href="{{ route('practice.configuration-assessment') }}">Open assessment</a>
            <a class="button" href="{{ route('practice.configuration-operations-runbook') }}">Open runbook</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Context</h2>
        <div class="panel">
            <ul>
                @foreach ($decisionRecord['context'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Decision</h2>
        <div class="panel">
            <ul>
                @foreach ($decisionRecord['decision'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Alternatives</h2>
        <div class="grid">
            @foreach ($decisionRecord['alternatives'] as $alternative)
                <article class="item">
                    <h3>{{ $alternative['option'] }}</h3>
                    <p>{{ $alternative['tradeoff'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Consequences</h2>
        <div class="panel">
            <ul>
                @foreach ($decisionRecord['consequences'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($decisionRecord['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

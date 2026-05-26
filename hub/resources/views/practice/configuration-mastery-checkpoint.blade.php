@extends('learning.layout', ['title' => 'Configuration Mastery Checkpoint'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration mastery</span>
        <h1>{{ $checkpoint['title'] }}</h1>
        <p>{{ $checkpoint['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $checkpoint['decision'] }} with {{ $checkpoint['score'] }} points</h2>
            <a class="button" href="{{ route('api.practice.configuration-mastery-checkpoint') }}">Open checkpoint API</a>
            <a class="button primary" href="{{ route('practice.configuration-spaced-review') }}">Open spaced review</a>
            <a class="button" href="{{ route('practice.configuration-interview-brief') }}">Open interview brief</a>
        </div>

        <div class="list">
            @foreach ($checkpoint['scorecard'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['points'] }} points</span>
                    </div>
                    <h3>{{ $item['criterion'] }}</h3>
                    <p>{{ $item['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Repeat Triggers</h2>
        <div class="panel">
            <ul>
                @foreach ($checkpoint['repeat_triggers'] as $trigger)
                    <li>{{ $trigger }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Next Actions</h2>
        <div class="list">
            @foreach ($checkpoint['next_actions'] as $action)
                <article class="item">
                    <p>{{ $action }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Handoff</h2>
        <article class="panel">
            <p><code>{{ $checkpoint['handoff']['next_route'] }}</code></p>
            <p><code>{{ $checkpoint['handoff']['evidence_route'] }}</code></p>
            <p><code>{{ $checkpoint['handoff']['interview_route'] }}</code></p>
        </article>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($checkpoint['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

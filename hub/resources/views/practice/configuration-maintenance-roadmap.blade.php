@extends('learning.layout', ['title' => 'Configuration Maintenance Roadmap'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration maintenance</span>
        <h1>{{ $roadmap['title'] }}</h1>
        <p>{{ $roadmap['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $roadmap['roadmap_status'] }}: {{ $roadmap['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-maintenance-roadmap') }}">Open roadmap API</a>
            <a class="button" href="{{ route('practice.configuration-archive-refresh-plan') }}">Open refresh plan</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Cadence</h2>
        <div class="list">
            @foreach ($roadmap['cadence'] as $item)
                <article class="item">
                    <span class="badge">{{ $item['window'] }}</span>
                    <h3>{{ $item['focus'] }}</h3>
                    <p>{{ $item['action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Owners</h2>
        <div class="list">
            @foreach ($roadmap['owners'] as $owner => $responsibility)
                <article class="item">
                    <h3>{{ $owner }}</h3>
                    <p>{{ $responsibility }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Health Signals</h2>
        <div class="panel">
            <ul>
                @foreach ($roadmap['health_signals'] as $signal)
                    <li>{{ $signal }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Escalation Paths</h2>
        <div class="list">
            @foreach ($roadmap['escalation_paths'] as $path)
                <article class="item">
                    <h3>{{ $path['trigger'] }}</h3>
                    <p>{{ $path['action'] }}</p>
                    <a class="button" href="{{ $path['route'] }}">Open remediation</a>
                </article>
            @endforeach
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Configuration Incident Postmortem'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $postmortem['postmortem_id'] }}</span>
        <h1>{{ $postmortem['title'] }}</h1>
        <p>{{ $postmortem['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $postmortem['incident']['incident_id'] }}: {{ $postmortem['incident']['runbook_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-incident-postmortem') }}">Open postmortem API</a>
            <a class="button" href="{{ route('practice.configuration-incident-drill') }}">Open incident drill</a>
            <a class="button primary" href="{{ route('practice.configuration-spaced-review') }}">Open spaced review</a>
        </div>
    </section>

    <section class="section">
        <h2>Impact</h2>
        <div class="panel">
            <ul>
                @foreach ($postmortem['impact'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Root Cause</h2>
        <div class="grid">
            @foreach ($postmortem['root_cause'] as $label => $description)
                <article class="item">
                    <h3>{{ $label }}</h3>
                    <p>{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Action Items</h2>
        <div class="list">
            @foreach ($postmortem['action_items'] as $item)
                <article class="item">
                    <span class="badge">{{ $item['owner'] }}</span>
                    <h3>{{ $item['task'] }}</h3>
                    <p><code>{{ $item['verification'] }}</code></p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Spaced Review Inputs</h2>
        <div class="panel">
            <ul>
                @foreach ($postmortem['spaced_review_inputs'] as $input)
                    <li>{{ $input }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

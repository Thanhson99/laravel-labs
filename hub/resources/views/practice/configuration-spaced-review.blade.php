@extends('learning.layout', ['title' => 'Configuration Spaced Review'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration review</span>
        <h1>{{ $spacedReview['title'] }}</h1>
        <p>{{ $spacedReview['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $spacedReview['decision'] }} with {{ $spacedReview['score'] }} points</h2>
            <a class="button" href="{{ route('api.practice.configuration-spaced-review') }}">Open review API</a>
            <a class="button primary" href="{{ route('practice.configuration-evidence-archive') }}">Open evidence archive</a>
            <a class="button" href="{{ route('practice.configuration-mastery-checkpoint') }}">Open mastery checkpoint</a>
            <a class="button" href="{{ route('practice.configuration-incident-postmortem') }}">Open postmortem</a>
        </div>

        <article class="panel">
            <h3>Incident Memory</h3>
            <p><code>{{ $spacedReview['incident_memory']['postmortem_id'] }}</code> / <code>{{ $spacedReview['incident_memory']['incident_id'] }}</code></p>
            <p>{{ $spacedReview['incident_memory']['root_cause'] }}</p>
        </article>

        <div class="list">
            @foreach ($spacedReview['review_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Day {{ $card['day'] }}</span>
                        <span class="muted">{{ $card['route'] }}</span>
                    </div>
                    <h3>{{ $card['focus'] }}</h3>
                    <p>{{ $card['recall_prompt'] }}</p>
                    <p>{{ $card['rebuild_task'] }}</p>
                    <p class="muted">{{ $card['defense_prompt'] }}</p>
                    <p>{{ $card['incident_prompt'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Promotion Criteria</h2>
        <div class="panel">
            <ul>
                @foreach ($spacedReview['promotion_criteria'] as $criterion)
                    <li>{{ $criterion }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($spacedReview['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

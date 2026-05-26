@extends('learning.layout', ['title' => 'Practice Challenge Promotion Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Challenge promotion</span>
        <h1>Challenge promotion turns reviewed evidence into the next learning decision.</h1>
        <p>
            This lab turns evidence review cards into promotion decisions,
            next routes, proof checklists, repeat triggers, and learner note prompts.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.challenge-promotion-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build promotion decisions</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['promotion_summary']['decision_count'] }} decisions</span>
                <span class="badge">{{ $lab['promotion_summary']['promoted_count'] }} promoted</span>
                <span class="badge">{{ $lab['promotion_summary']['repeat_count'] }} repeats</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.challenge-evidence-review-lab', request()->query()) }}">Open evidence review lab</a>
            <a class="button" href="{{ route('api.practice.challenge-promotion-lab', request()->query()) }}">Open promotion API</a>
        </article>
    </section>

    <section class="section">
        <h2>Promotion Rules</h2>
        <div class="list">
            @foreach ($lab['promotion_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Promotion Decisions</h2>
        <div class="list">
            @foreach ($lab['promotion_decisions'] as $decision)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $decision['step'] }}</span>
                        <span class="badge">{{ $decision['decision'] }}</span>
                    </div>
                    <h3>{{ $decision['technology_segment'] }}</h3>
                    <p><strong>Source route:</strong> <code>{{ $decision['source_route_name'] }}</code></p>
                    <p><strong>Next route:</strong> <code>{{ $decision['next_route_name'] }}</code></p>
                    <p><strong>Command:</strong> <code>{{ $decision['verification_command'] }}</code></p>

                    <h4>Promotion Proof</h4>
                    <ul>
                        @foreach ($decision['promotion_proof'] as $proof)
                            <li>{{ $proof }}</li>
                        @endforeach
                    </ul>

                    <h4>Repeat Triggers</h4>
                    <ul>
                        @foreach ($decision['repeat_triggers'] as $trigger)
                            <li>{{ $trigger }}</li>
                        @endforeach
                    </ul>

                    <p><strong>Learner note:</strong> {{ $decision['learner_note_prompt'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Challenge Promotion Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

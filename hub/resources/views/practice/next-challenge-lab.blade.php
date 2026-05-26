@extends('learning.layout', ['title' => 'Practice Next Challenge Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Next challenge</span>
        <h1>Next challenge lab turns the competency map into the next practice challenge.</h1>
        <p>
            This lab turns competency cards into challenge types, recommended routes,
            verification commands, evidence to submit, and challenge summaries.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.next-challenge-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build next challenges</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_competency_lab']['competency_count'] }} competencies</span>
                <span class="badge">{{ $lab['challenge_summary']['harder_lab_count'] }} harder</span>
                <span class="badge">{{ $lab['challenge_summary']['reinforcement_count'] }} reinforce</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.competency-map-lab', request()->query()) }}">Open competency map</a>
            <a class="button" href="{{ route('api.practice.next-challenge-lab', request()->query()) }}">Open challenge API</a>
        </article>
    </section>

    <section class="section">
        <h2>Challenge Rules</h2>
        <div class="list">
            @foreach ($lab['challenge_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Challenge Cards</h2>
        <div class="list">
            @foreach ($lab['challenge_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $card['challenge_type'] }}</span>
                        <span class="badge">{{ $card['readiness'] }}</span>
                    </div>
                    <h3>{{ $card['technology_segment'] }}</h3>
                    <p>{{ $card['why_this_challenge'] }}</p>
                    <p><strong>Route:</strong> <code>{{ $card['recommended_route'] }}</code></p>
                    <p><strong>Command:</strong> <code>{{ $card['verification_command'] }}</code></p>
                    <h4>Evidence To Submit</h4>
                    <ul>
                        @foreach ($card['evidence_to_submit'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Next Challenge Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

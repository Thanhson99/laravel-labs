@extends('learning.layout', ['title' => 'Practice Knowledge Gap Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Knowledge gap</span>
        <h1>Knowledge gap lab turns weak answers into the next coding task.</h1>
        <p>
            This lab turns interview defense cards into gap cards,
            practice actions, evidence to recheck, verification hints, and the next session plan.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.knowledge-gap-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build gap cards</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_defense_lab']['defense_card_count'] }} defense cards</span>
                @foreach ($lab['source_defense_lab']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.interview-defense-lab', request()->query()) }}">Open defense lab</a>
            <a class="button" href="{{ route('api.practice.knowledge-gap-lab', request()->query()) }}">Open gap API</a>
        </article>
    </section>

    <section class="section">
        <h2>Gap Rules</h2>
        <div class="list">
            @foreach ($lab['gap_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Gap Cards</h2>
        <div class="list">
            @foreach ($lab['gap_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $card['round'] }}</span>
                        <span class="badge">{{ $card['technology_segment'] }}</span>
                    </div>
                    <h3>{{ $card['gap'] }}</h3>
                    <p><strong>Practice:</strong> {{ $card['practice_action'] }}</p>
                    <p><strong>Review:</strong> {{ $card['review_prompt'] }}</p>
                    <p><strong>Verify:</strong> <code>{{ $card['verification_hint'] }}</code></p>
                    <h4>Evidence To Recheck</h4>
                    <ul>
                        @foreach ($card['evidence_to_recheck'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Next Session Plan</h2>
                <ul>
                    @foreach ($lab['next_session_plan'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Knowledge Gap Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

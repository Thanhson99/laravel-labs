@extends('learning.layout', ['title' => 'Practice Session Debrief Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Session debrief</span>
        <h1>Session debrief turns replay rounds into learning notes and next actions.</h1>
        <p>
            This lab turns replay rounds into debrief cards,
            result notes, lesson prompts, blocker checks, and next actions.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.session-debrief-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build debrief cards</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['debrief_summary']['card_count'] }} cards</span>
                <span class="badge">{{ $lab['debrief_summary']['passed_review_count'] }} archived</span>
                <span class="badge">{{ $lab['debrief_summary']['needs_retry_count'] }} retries</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.session-replay-lab', request()->query()) }}">Open replay lab</a>
            <a class="button" href="{{ route('api.practice.session-debrief-lab', request()->query()) }}">Open debrief API</a>
        </article>
    </section>

    <section class="section">
        <h2>Debrief Rules</h2>
        <div class="list">
            @foreach ($lab['debrief_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Debrief Cards</h2>
        <div class="list">
            @foreach ($lab['debrief_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $card['round'] }}</span>
                        <span class="badge">{{ $card['debrief_status'] }}</span>
                    </div>
                    <h3>{{ $card['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $card['route_name'] }}</code></p>
                    <p><strong>Command:</strong> <code>{{ $card['command'] }}</code></p>

                    <h4>Result Notes</h4>
                    <ul>
                        @foreach ($card['result_notes'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>

                    <h4>Lesson Prompts</h4>
                    <ol>
                        @foreach ($card['lesson_prompts'] as $prompt)
                            <li>{{ $prompt }}</li>
                        @endforeach
                    </ol>

                    <h4>Blocker Checks</h4>
                    <ul>
                        @foreach ($card['blocker_checks'] as $blocker)
                            <li>{{ $blocker }}</li>
                        @endforeach
                    </ul>

                    <p><strong>Next action:</strong> {{ $card['next_action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Session Debrief Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

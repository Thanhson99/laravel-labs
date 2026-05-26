@extends('learning.layout', ['title' => 'Practice Next Session Handoff Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Next session handoff</span>
        <h1>Next session handoff turns promotion decisions into the next coding session.</h1>
        <p>
            This lab turns promotion decisions into handoff cards,
            routes to open, preflight checklists, coding focus, done evidence, and note prompts.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.next-session-handoff-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build handoffs</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['handoff_summary']['handoff_count'] }} handoffs</span>
                <span class="badge">{{ $lab['handoff_summary']['promotion_handoffs'] }} promotions</span>
                <span class="badge">{{ $lab['handoff_summary']['repeat_handoffs'] }} repeats</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.challenge-promotion-lab', request()->query()) }}">Open promotion lab</a>
            <a class="button" href="{{ route('api.practice.next-session-handoff-lab', request()->query()) }}">Open handoff API</a>
        </article>
    </section>

    <section class="section">
        <h2>Handoff Rules</h2>
        <div class="list">
            @foreach ($lab['handoff_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Next Session Handoff Cards</h2>
        <div class="list">
            @foreach ($lab['handoff_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $card['step'] }}</span>
                        <span class="badge">{{ $card['handoff_type'] }}</span>
                    </div>
                    <h3>{{ $card['technology_segment'] }}</h3>
                    <p><strong>Open route:</strong> <code>{{ $card['open_route_name'] }}</code></p>
                    <p><strong>Carry over:</strong> <code>{{ $card['carry_over_route_name'] }}</code></p>
                    <p><strong>Goal:</strong> {{ $card['session_goal'] }}</p>

                    <h4>Preflight Checklist</h4>
                    <ol>
                        @foreach ($card['preflight_checklist'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ol>

                    <h4>Coding Focus</h4>
                    <ul>
                        @foreach ($card['coding_focus'] as $focus)
                            <li>{{ $focus }}</li>
                        @endforeach
                    </ul>

                    <h4>Done Evidence</h4>
                    <ul>
                        @foreach ($card['done_evidence'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>

                    <p><strong>Note:</strong> {{ $card['handoff_note_prompt'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Next Session Handoff Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

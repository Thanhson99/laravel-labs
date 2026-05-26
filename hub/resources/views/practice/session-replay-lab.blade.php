@extends('learning.layout', ['title' => 'Practice Session Replay Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Session replay</span>
        <h1>Session replay turns handoff cards into verified before-and-after coding rounds.</h1>
        <p>
            This lab turns next-session handoffs into replay rounds,
            before checks, coding runs, after checks, evidence capture, and retry policy.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.session-replay-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build replay rounds</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['replay_summary']['round_count'] }} rounds</span>
                <span class="badge">{{ $lab['replay_summary']['promotion_rounds'] }} promotions</span>
                <span class="badge">{{ $lab['replay_summary']['repeat_rounds'] }} repeats</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.next-session-handoff-lab', request()->query()) }}">Open handoff lab</a>
            <a class="button" href="{{ route('api.practice.session-replay-lab', request()->query()) }}">Open replay API</a>
        </article>
    </section>

    <section class="section">
        <h2>Replay Rules</h2>
        <div class="list">
            @foreach ($lab['replay_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Replay Rounds</h2>
        <div class="list">
            @foreach ($lab['replay_rounds'] as $round)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $round['round'] }}</span>
                        <span class="badge">{{ $round['handoff_type'] }}</span>
                    </div>
                    <h3>{{ $round['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $round['route_name'] }}</code></p>
                    <p><strong>Before:</strong> open <code>{{ $round['before_check']['route_name'] }}</code> and run <code>{{ $round['before_check']['command'] }}</code></p>
                    <p><strong>Goal:</strong> {{ $round['coding_run']['goal'] }}</p>

                    <h4>Coding Run</h4>
                    <ul>
                        @foreach ($round['coding_run']['focus_items'] as $focus)
                            <li>{{ $focus }}</li>
                        @endforeach
                    </ul>

                    <p><strong>After:</strong> open <code>{{ $round['after_check']['route_name'] }}</code> and run <code>{{ $round['after_check']['command'] }}</code></p>

                    <h4>Evidence Capture</h4>
                    <ul>
                        @foreach ($round['evidence_capture'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>

                    <p><strong>Retry policy:</strong> {{ $round['retry_policy'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Session Replay Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

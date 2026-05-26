@extends('learning.layout', ['title' => 'Practice Live Coding Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Live coding</span>
        <h1>Live coding lab turns a demo script into a timeboxed practice session.</h1>
        <p>
            This lab turns demo scripts into rounds, coding prompts, narration prompts,
            verification commands, scorecards, and failure recovery.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.live-coding-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build live coding session</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $session['demo_script']['day_count'] }} days</span>
                @foreach ($session['demo_script']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $session['title'] }}</h2>
            <p>{{ $session['demo_script']['goal'] }}</p>
            <a class="button" href="{{ route('practice.demo-script-lab', request()->query()) }}">Open demo script</a>
            <a class="button" href="{{ route('api.practice.live-coding-lab', request()->query()) }}">Open live coding API</a>
        </article>
    </section>

    <section class="section">
        <h2>Session Rules</h2>
        <div class="list">
            @foreach ($session['session_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Live Coding Rounds</h2>
        <div class="list">
            @foreach ($session['rounds'] as $round)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $round['round'] }}</span>
                        <span class="badge">{{ $round['timebox_minutes'] }} minutes</span>
                    </div>
                    <h3>{{ $round['technology_segment'] }}</h3>
                    <p><strong>Code:</strong> {{ $round['coding_prompt'] }}</p>
                    <p><strong>Narrate:</strong> {{ $round['narration_prompt'] }}</p>
                    <p><strong>Command:</strong> <code>{{ $round['verification_command'] }}</code></p>
                    <p><strong>Pass:</strong> {{ $round['pass_signal'] }}</p>
                    <p><strong>Evidence:</strong> {{ $round['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Scorecard</h2>
                <ul>
                    @foreach ($session['scorecard'] as $item)
                        <li>{{ $item['label'] }} - {{ $item['points'] }} points</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Failure Recovery</h2>
                <ul>
                    @foreach ($session['failure_recovery'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Live Coding Progress Payload</h2>
                <pre><code>{{ json_encode($session['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

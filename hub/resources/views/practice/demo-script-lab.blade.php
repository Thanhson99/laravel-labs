@extends('learning.layout', ['title' => 'Practice Demo Script Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Demo script</span>
        <h1>Demo script lab turns a weekly report into a practice presentation script.</h1>
        <p>
            This lab turns weekly reports into opening lines, demo steps,
            verification actions, evidence, and a handoff payload for code presentation practice.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.demo-script-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build demo script</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $script['weekly_report']['day_count'] }} days</span>
                @foreach ($script['weekly_report']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $script['title'] }}</h2>
            <p>{{ $script['demo_goal'] }}</p>
            <a class="button" href="{{ route('practice.weekly-report-lab', request()->query()) }}">Open weekly report</a>
            <a class="button" href="{{ route('api.practice.demo-script-lab', request()->query()) }}">Open demo script API</a>
        </article>
    </section>

    <section class="section">
        <h2>Opening Lines</h2>
        <div class="list">
            @foreach ($script['opening_lines'] as $line)
                <article class="item">
                    <p>{{ $line }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Demo Steps</h2>
        <div class="list">
            @foreach ($script['script_steps'] as $step)
                <article class="item">
                    <h3>{{ $step['segment'] }}</h3>
                    <p><strong>Say:</strong> {{ $step['say'] }}</p>
                    <p><strong>Do:</strong> {{ $step['do'] }}</p>
                    <p><strong>Verify:</strong> {{ $step['verify'] }}</p>
                    <p><strong>Evidence:</strong> {{ $step['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Rehearsal Checklist</h2>
                <ul>
                    @foreach ($script['rehearsal_checklist'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Demo Handoff Payload</h2>
                <pre><code>{{ json_encode($script['handoff_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

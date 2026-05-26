@extends('learning.layout', ['title' => 'Practice Weekly Report Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Weekly report</span>
        <h1>Weekly report lab summarizes the rotation into a progress report.</h1>
        <p>
            This lab turns the rotation into daily outputs, an evidence checklist,
            blocker prompts, a next-week plan, and a progress payload.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.weekly-report-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build weekly report</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $report['rotation']['day_count'] }} days</span>
                <span class="muted">{{ $report['rotation']['milestone_count'] }} milestones</span>
            </div>
            <h2>{{ $report['title'] }}</h2>
            <a class="button" href="{{ route('practice.rotation-lab', request()->query()) }}">Open rotation lab</a>
            <a class="button" href="{{ route('api.practice.weekly-report-lab', request()->query()) }}">Open weekly report API</a>
        </article>
    </section>

    <section class="section">
        <h2>Daily Outputs</h2>
        <div class="list">
            @foreach ($report['daily_outputs'] as $output)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Day {{ $output['day'] }}</span>
                        <span class="badge">{{ $output['technology'] }}</span>
                    </div>
                    <h3>{{ $output['focus'] }}</h3>
                    <p>{{ $output['output'] }}</p>
                    <p>{{ $output['evidence_to_attach'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Evidence Checklist</h2>
                <ul>
                    @foreach ($report['evidence_checklist'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Weekly Report Progress Payload</h2>
                <pre><code>{{ json_encode($report['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

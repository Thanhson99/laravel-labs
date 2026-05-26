@extends('learning.layout', ['title' => 'Practice Release Readiness Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Release readiness</span>
        <h1>Release readiness lab turns refactor output into a handoff checklist.</h1>
        <p>
            This lab turns refactor tasks into release notes, smoke checks,
            rollback notes, verification evidence, and a handoff checklist.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.release-readiness-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build release checklist</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_refactor_lab']['task_count'] }} tasks</span>
                @foreach ($lab['source_refactor_lab']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.refactor-lab', request()->query()) }}">Open refactor lab</a>
            <a class="button" href="{{ route('api.practice.release-readiness-lab', request()->query()) }}">Open release API</a>
        </article>
    </section>

    <section class="section">
        <h2>Release Rules</h2>
        <div class="list">
            @foreach ($lab['release_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Release Items</h2>
        <div class="list">
            @foreach ($lab['release_items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $item['round'] }}</span>
                        <span class="badge">{{ $item['changed_area'] }}</span>
                    </div>
                    <h3>{{ $item['technology_segment'] }}</h3>
                    <p><strong>Release note:</strong> {{ $item['release_note'] }}</p>
                    <p><strong>Smoke:</strong> {{ $item['smoke_check'] }}</p>
                    <p><strong>Rollback:</strong> {{ $item['rollback_note'] }}</p>
                    <p><strong>Command:</strong> <code>{{ $item['verification_command'] }}</code></p>
                    <p><strong>Evidence:</strong> {{ $item['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Handoff Checklist</h2>
                <ul>
                    @foreach ($lab['handoff_checklist'] as $check)
                        <li>{{ $check }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Release Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Practice Sprint'])

@section('content')
    <section class="hero">
        <span class="badge">Practice sprint</span>
        <h1>Practice sprint turns the syllabus into concrete coding work.</h1>
        <p>
            The sprint uses technology phases from the syllabus, creates a small queue for each phase,
            and links directly to workspaces and verification plans.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.sprint') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker, test...">
        </label>
        <label>
            Family
            <input type="search" name="family" value="{{ $filters['family'] }}" placeholder="laravel">
        </label>
        <label>
            Language
            <input type="search" name="language" value="{{ $filters['language'] }}" placeholder="en">
        </label>
        <label>
            Phase limit
            <input type="number" name="phase_limit" min="1" max="10" value="{{ $filters['phase_limit'] }}">
        </label>
        <label>
            Tasks per phase
            <input type="number" name="tasks_per_phase" min="1" max="10" value="{{ $filters['tasks_per_phase'] }}">
        </label>
        <button class="button primary" type="submit">Build sprint</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $sprint['meta']['task_count'] }} tasks, {{ $sprint['meta']['estimated_minutes'] }} minutes</h2>
            <a class="button" href="{{ route('api.practice.sprint', request()->query()) }}">Open sprint API</a>
            <a class="button" href="{{ route('practice.syllabus', request()->query()) }}">Open syllabus</a>
        </div>

        <div class="list">
            @foreach ($sprint['phases'] as $phase)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Phase {{ $phase['phase'] }}</span>
                        <span class="badge">{{ $phase['technology'] }}</span>
                        <span class="muted">{{ $phase['estimated_minutes'] }} min</span>
                    </div>
                    <h3>{{ $phase['title'] }}</h3>
                    <a class="button" href="{{ route('practice.technology-board', $phase['board_query']) }}">Open technology board</a>
                    <a class="button" href="{{ route('practice.queue', array_filter($phase['queue_query'])) }}">Open phase queue</a>
                    @if ($phase['exercise']['slug'])
                        <a class="button" href="{{ route('practice.show', $phase['exercise']['slug']) }}">Open exercise</a>
                    @endif

                    <div class="list">
                        @foreach ($phase['tasks'] as $task)
                            <article class="item">
                                <div class="meta">
                                    <span class="badge">Task {{ $task['position'] }}</span>
                                    <span class="muted">{{ $task['estimated_minutes'] }} min</span>
                                    <span class="muted">{{ $task['source']['path'] }}</span>
                                </div>
                                <h4>{{ $task['question'] }}</h4>
                                <a class="button primary" href="{{ route('practice.record-workspace', $task['workspace_query']) }}">Open workspace</a>
                                <a class="button" href="{{ route('practice.verification-plan', $task['verification_query']) }}">Open verification plan</a>
                            </article>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Sprint Progress Payload</h2>
            <pre><code>{{ json_encode($sprint['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

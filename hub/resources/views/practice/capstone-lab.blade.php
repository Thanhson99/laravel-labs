@extends('learning.layout', ['title' => 'Practice Capstone Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Capstone lab</span>
        <h1>Capstone lab turns multiple records into a focused Laravel mini-project.</h1>
        <p>
            This lab uses the technology board and practice queue to produce a mission,
            source coverage, task list, deliverables, and artifact links.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.capstone-lab') }}">
        <label>
            Technology
            <input type="search" name="technology" value="{{ $filters['technology'] }}" placeholder="api-validation">
        </label>
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Limit
            <input type="number" name="limit" min="1" max="10" value="{{ $filters['limit'] }}">
        </label>
        <button class="button primary" type="submit">Build capstone</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $capstone['technology'] }}</span>
                <span class="muted">{{ $capstone['meta']['task_count'] }} tasks</span>
                <span class="muted">{{ $capstone['meta']['estimated_minutes'] }} min</span>
            </div>
            <h2>{{ $capstone['title'] }}</h2>
            <p>{{ $capstone['mission'] }}</p>
            <a class="button" href="{{ route('practice.technology-board', ['technology' => $capstone['technology'], 'family' => $filters['family'], 'language' => $filters['language'], 'search' => $filters['search']]) }}">Open technology board</a>
            <a class="button" href="{{ route('api.practice.capstone-lab', request()->query()) }}">Open capstone API</a>
        </article>
    </section>

    <section class="section">
        <h2>Source Coverage</h2>
        <div class="list">
            @foreach ($capstone['source_coverage'] as $source)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $source['record_count'] }} records</span>
                        <code>{{ $source['path'] }}</code>
                    </div>
                    <h3>{{ $source['title'] }}</h3>
                    @if ($source['sample_workspace_query'])
                        <a class="button" href="{{ route('practice.record-workspace', $source['sample_workspace_query']) }}">Open sample workspace</a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Capstone Tasks</h2>
        <div class="list">
            @foreach ($capstone['tasks'] as $task)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Task {{ $task['position'] }}</span>
                        <span class="muted">{{ $task['estimated_minutes'] }} min</span>
                        <code>{{ $task['source']['path'] }}</code>
                    </div>
                    <h3>{{ $task['question'] }}</h3>
                    <p>{{ $task['deliverable'] }}</p>
                    <a class="button primary" href="{{ route('practice.record-workspace', $task['workspace_query']) }}">Open workspace</a>
                    <a class="button" href="{{ route('practice.tdd-lab', $task['workspace_query']) }}">Open TDD lab</a>
                    <a class="button" href="{{ route('practice.portfolio-lab', $task['workspace_query']) }}">Open portfolio lab</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Deliverables</h2>
                <ul>
                    @foreach ($capstone['deliverables'] as $deliverable)
                        <li>{{ $deliverable }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Capstone Progress Payload</h2>
                <pre><code>{{ json_encode($capstone['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

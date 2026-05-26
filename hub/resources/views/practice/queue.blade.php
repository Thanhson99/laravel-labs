@extends('learning.layout', ['title' => 'Practice Queue'])

@section('content')
    <section class="hero">
        <span class="badge">Practice queue</span>
        <h1>A practice queue from source questions to coding workspaces.</h1>
        <p>
            The queue turns content and question records into ordered coding work,
            and each item opens a record workspace for Laravel practice.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.queue') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker, test...">
        </label>
        <label>
            Family
            <input type="search" name="family" value="{{ $filters['family'] }}" placeholder="laravel">
        </label>
        <label>
            Technology
            <input type="search" name="technology" value="{{ $filters['technology'] }}" placeholder="api-validation">
        </label>
        <label>
            Limit
            <input type="number" name="limit" min="1" max="50" value="{{ $filters['limit'] }}">
        </label>
        <button class="button primary" type="submit">Build queue</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $queue['meta']['count'] }} tasks, {{ $queue['meta']['estimated_minutes'] }} minutes</h2>
            <a class="button" href="{{ route('api.practice.queue', request()->query()) }}">Open queue API</a>
        </div>
        <div class="list">
            @forelse ($queue['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">#{{ $item['position'] }}</span>
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['estimated_minutes'] }} min</span>
                        <span class="muted">{{ $item['source']['path'] }}</span>
                    </div>
                    <h3>{{ $item['question'] }}</h3>
                    <p>{{ $item['task'] }}</p>
                    <a class="button primary" href="{{ route('practice.record-workspace', $item['workspace_query']) }}">Open record workspace</a>
                    <a class="button" href="{{ route('learning.sources.show', $item['source']['key']) }}">Open source JSON</a>
                </article>
            @empty
                <article class="item">
                    <h3>No practice queue items</h3>
                    <p class="muted">Try broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Queue Progress Payload</h2>
            <pre><code>{{ json_encode($queue['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

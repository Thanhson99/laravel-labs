@extends('learning.layout', ['title' => 'Technology Implementation Lab'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $lab['technology'] }}</span>
        <h1>{{ $lab['title'] }}</h1>
        <p>
            This lab turns JSON-backed content records into an ordered implementation path:
            read the source, create files, wire routes and services, then verify the result.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-implementation-lab', $lab['technology']) }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, policy, upload...">
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
            Limit
            <input type="number" name="limit" value="{{ $filters['limit'] }}" min="1" max="100">
        </label>
        <button class="button primary" type="submit">Build lab</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $lab['meta']['task_count'] }} implementation tasks</h2>
            @if ($lab['related_workbench'] && $lab['related_workbench']['route_name'])
                <a class="button primary" href="{{ route($lab['related_workbench']['route_name']) }}">{{ $lab['related_workbench']['label'] }}</a>
            @endif
            <a class="button" href="{{ route('practice.technology-code-examples.show', [$lab['technology']] + request()->query()) }}">Open code examples</a>
            <a class="button primary" href="{{ route('practice.technology-commit-plan', [$lab['technology']] + request()->query()) }}">Open commit plan</a>
            <a class="button" href="{{ route('api.practice.technology-implementation-lab', [$lab['technology']] + request()->query()) }}">Open lab API</a>
        </div>

        <div class="list">
            @foreach ($lab['next_actions'] as $action)
                <article class="item">
                    <h3>{{ $action['label'] }}</h3>
                    <p>{{ $action['purpose'] }}</p>
                    <a class="button primary" href="{{ $action['path'] }}">Open next step</a>
                    @if ($action['api_path'])
                        <a class="button" href="{{ $action['api_path'] }}">Open API</a>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="list">
            @foreach ($lab['phases'] as $phase)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $phase['label'] }}</span>
                    </div>
                    <h3>{{ $phase['goal'] }}</h3>
                    <ul>
                        @foreach ($phase['tasks'] as $task)
                            <li>{{ $task }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Source Workspaces</h2>
        <div class="list">
            @forelse ($lab['source_examples']['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['record_id'] }}</span>
                        <span class="muted">{{ $item['source']['path'] }}</span>
                    </div>
                    <h3>{{ $item['content']['title'] }}</h3>
                    <p>{{ $item['task'] }}</p>
                    <a class="button primary" href="{{ route('practice.record-workspace', $item['workspace_query']) }}">Open workspace</a>
                </article>
            @empty
                <article class="item">
                    <h3>No implementation tasks found</h3>
                    <p class="muted">Try a broader search or another technology key.</p>
                </article>
            @endforelse
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="list">
            @foreach ($lab['commands'] as $command)
                <article class="item">
                    <pre><code>{{ $command }}</code></pre>
                </article>
            @endforeach
        </div>
    </section>
@endsection

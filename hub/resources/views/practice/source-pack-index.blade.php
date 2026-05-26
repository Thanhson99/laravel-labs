@extends('learning.layout', ['title' => 'Source Practice Packs'])

@section('content')
    <section class="hero">
        <span class="badge">Source packs</span>
        <h1>Choose a source file and turn it into a practice pack.</h1>
        <p>
            This list reads JSON sources, counts records, detects technologies,
            and opens packs or workspaces for Laravel practice tied to each source file.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.source-packs.index') }}">
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
            Limit
            <input type="number" name="limit" min="1" max="100" value="{{ $filters['limit'] }}">
        </label>
        <button class="button primary" type="submit">Find source packs</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $index['meta']['count'] }} source packs</h2>
            <a class="button" href="{{ route('api.practice.source-packs.index', request()->query()) }}">Open source packs API</a>
        </div>
        <div class="list">
            @forelse ($index['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['source']['family'] }}</span>
                        @if ($item['source']['language'])
                            <span class="badge">{{ $item['source']['language'] }}</span>
                        @endif
                        <span class="muted">{{ $item['record_count'] }} records</span>
                    </div>
                    <h3>{{ $item['source']['title'] }}</h3>
                    <p><code>{{ $item['source']['path'] }}</code></p>
                    <div class="meta">
                        @foreach ($item['technologies'] as $technology)
                            <span class="badge">{{ $technology['technology'] }}: {{ $technology['record_count'] }}</span>
                        @endforeach
                    </div>
                    <a class="button primary" href="{{ route('practice.source-pack', $item['source']['key']) }}">Open source pack</a>
                    @if ($item['sample_workspace_query'])
                        <a class="button" href="{{ route('practice.record-workspace', $item['sample_workspace_query']) }}">Open sample workspace</a>
                    @endif
                    <a class="button" href="{{ route('learning.sources.show', $item['source']['key']) }}">Open source JSON</a>
                </article>
            @empty
                <article class="item">
                    <h3>No source packs found</h3>
                    <p class="muted">Try broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

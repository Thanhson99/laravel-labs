@extends('learning.layout', ['title' => 'Technology Practice Board'])

@section('content')
    <section class="hero">
        <span class="badge">Technology board</span>
        <h1>Technology practice board for source-backed Laravel drills.</h1>
        <p>
            Choose a technology, inspect related JSON sources, and open a source pack,
            queue, or record workspace for Laravel practice aligned with that content.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-board') }}">
        <label>
            Technology
            <input type="search" name="technology" value="{{ $filters['technology'] }}" placeholder="api-validation">
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
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, queue...">
        </label>
        <button class="button primary" type="submit">Build board</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $board['meta']['source_count'] }} sources, {{ $board['meta']['record_count'] }} records</h2>
            <a class="button" href="{{ route('practice.queue', array_filter($board['queue_query'])) }}">Open queue</a>
            <a class="button" href="{{ route('api.practice.technology-board', request()->query()) }}">Open board API</a>
        </div>
        <div class="list">
            @forelse ($board['sources'] as $source)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $board['technology'] }}</span>
                        <span class="muted">{{ $source['record_count'] }} records</span>
                    </div>
                    <h3>{{ $source['source']['title'] }}</h3>
                    <p><code>{{ $source['source']['path'] }}</code></p>
                    <a class="button primary" href="{{ route('practice.source-pack', $source['source']['key']) }}">Open source pack</a>
                    <a class="button" href="{{ route('learning.sources.show', $source['source']['key']) }}">Open JSON</a>
                    <div class="list section">
                        @foreach ($source['sample_records'] as $record)
                            <article class="panel">
                                <h3>{{ $record['title'] }}</h3>
                                <p class="muted">{{ $record['task'] }}</p>
                                <a class="button" href="{{ route('practice.record-workspace', $record['workspace_query']) }}">Open workspace</a>
                            </article>
                        @endforeach
                    </div>
                </article>
            @empty
                <article class="item">
                    <h3>No records found</h3>
                    <p class="muted">Try another technology or broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

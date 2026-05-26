@extends('learning.layout', ['title' => 'Question Drill Set'])

@section('content')
    <section class="hero">
        <span class="badge">Question drills</span>
        <h1>Each question becomes a small Laravel coding drill.</h1>
        <p>
            These drills come from question and content sources, attach related technologies,
            and link to the content drill so implementation can start immediately.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.question-drills') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, test, docker...">
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
            Technology
            <input type="search" name="technology" value="{{ $filters['technology'] }}" placeholder="api-validation">
        </label>
        <button class="button primary" type="submit">Build question drills</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $drills['meta']['count'] }} drill cards</h2>
            <a class="button" href="{{ route('api.practice.question-drills', request()->query()) }}">Open drills API</a>
        </div>
        <div class="list">
            @forelse ($drills['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">#{{ $item['position'] }}</span>
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['source']['path'] }}</span>
                    </div>
                    <h3>{{ $item['question'] }}</h3>
                    <p>{{ $item['task'] }}</p>
                    <a class="button primary" href="{{ route('practice.content-drill', $item['drill_query']) }}">Open coding drill</a>
                    <a class="button" href="{{ route('practice.show', $item['practice']['slug']) }}">{{ $item['practice']['title'] }}</a>
                    <a class="button" href="{{ route('learning.sources.show', $item['source']['key']) }}">Open source JSON</a>
                </article>
            @empty
                <article class="item">
                    <h3>No drill cards found</h3>
                    <p class="muted">Try broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

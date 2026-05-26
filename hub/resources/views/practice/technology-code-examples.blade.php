@extends('learning.layout', ['title' => 'Technology Code Examples'])

@section('content')
    <section class="hero">
        <span class="badge">Code examples</span>
        <h1>Code samples generated from the technologies inside JSON content.</h1>
        <p>
            This page reads learning records, infers the related technology, and turns each technology group
            into concrete Laravel files a learner can read, copy, test, and implement.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-code-examples') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, policy, cache, upload...">
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
        <button class="button primary" type="submit">Build examples</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $examples['meta']['technology_count'] }} technologies with code from {{ $examples['meta']['record_count'] }} records</h2>
            <a class="button" href="{{ route('api.practice.technology-code-examples', request()->query()) }}">Open examples API</a>
        </div>

        <div class="list">
            @forelse ($examples['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['record_count'] }} records</span>
                        <span class="muted">{{ $item['source']['path'] }}</span>
                    </div>
                    <h3>{{ $item['content']['title'] }}</h3>
                    <p>{{ $item['task'] }}</p>
                    <a class="button primary" href="{{ route('practice.technology-learning-pipeline', [$item['technology']] + request()->query()) }}">Open pipeline</a>
                    <a class="button primary" href="{{ route('practice.technology-code-examples.show', [$item['technology']] + request()->query()) }}">Open technology detail</a>
                    <a class="button primary" href="{{ route('practice.record-workspace', $item['workspace_query']) }}">Open workspace</a>
                    @if ($item['practice']['slug'])
                        <a class="button" href="{{ route('practice.show', $item['practice']['slug']) }}">Open native exercise</a>
                    @endif
                </article>

                @foreach ($item['snippets'] as $snippet)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $item['technology'] }}</span>
                            <span class="muted">{{ $snippet['label'] }}</span>
                            <code>{{ $snippet['file'] }}</code>
                        </div>
                        <pre><code>{{ $snippet['code'] }}</code></pre>
                    </article>
                @endforeach
            @empty
                <article class="item">
                    <h3>No code examples found</h3>
                    <p class="muted">Try broader filters or remove the search value.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

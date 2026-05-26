@extends('learning.layout', ['title' => 'Technology Code Example Detail'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $examples['technology'] }}</span>
        <h1>Record-level code examples for {{ $examples['technology'] }}.</h1>
        <p>
            Each record below comes from JSON learning content and is turned into a focused Laravel implementation
            task with matching starter snippets and a workspace link.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-code-examples.show', $examples['technology']) }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="validation, token, cache...">
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
        <button class="button primary" type="submit">Build detail</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $examples['meta']['record_count'] }} source records</h2>
            <a class="button" href="{{ route('practice.technology-code-examples', request()->query()) }}">All technologies</a>
            <a class="button primary" href="{{ route('practice.technology-implementation-lab', [$examples['technology']] + request()->query()) }}">Open implementation lab</a>
            <a class="button" href="{{ route('api.practice.technology-code-examples.show', [$examples['technology']] + request()->query()) }}">Open detail API</a>
        </div>

        <div class="list">
            @forelse ($examples['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['record_id'] }}</span>
                        <span class="muted">{{ $item['source']['path'] }}</span>
                    </div>
                    <h3>{{ $item['content']['title'] }}</h3>
                    <p>{{ $item['task'] }}</p>
                    <a class="button primary" href="{{ route('practice.record-workspace', $item['workspace_query']) }}">Open record workspace</a>
                    @if ($item['practice']['slug'])
                        <a class="button" href="{{ route('practice.show', $item['practice']['slug']) }}">Open native exercise</a>
                    @endif
                </article>

                @foreach ($item['snippets'] as $snippet)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $snippet['label'] }}</span>
                            <code>{{ $snippet['file'] }}</code>
                        </div>
                        <pre><code>{{ $snippet['code'] }}</code></pre>
                    </article>
                @endforeach
            @empty
                <article class="item">
                    <h3>No records found for this technology</h3>
                    <p class="muted">Try a broader search, a different family, or another technology key.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

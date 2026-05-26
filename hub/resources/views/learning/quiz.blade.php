@extends('learning.layout', ['title' => 'Practice Quiz'])

@section('content')
    <section class="hero">
        <span class="badge">Practice mode</span>
        <h1>Practice questions generated from the integrated source content.</h1>
        <p>
            Generate a focused quiz from portal content. Use filters to practice by language,
            content group, or the keyword you are currently reviewing.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('learning.quiz') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="auth, queue, OOP, security...">
        </label>
        <label>
            Language
            <select name="language">
                @foreach ($languages as $language)
                    <option value="{{ $language }}" @selected($filters['language'] === $language)>{{ strtoupper($language) }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Family
            <select name="family">
                <option value="">All</option>
                @foreach ($families as $family)
                    <option value="{{ $family }}" @selected($filters['family'] === $family)>{{ $family }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Limit
            <input type="number" name="limit" min="1" max="50" value="{{ $filters['limit'] ?? 10 }}">
        </label>
        <button class="button primary" type="submit">Generate</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $quiz['meta']['count'] }} cards from {{ $quiz['meta']['available'] }} available records</h2>
            <a class="button" href="{{ route('api.learning.quiz', request()->query()) }}">Open quiz API</a>
        </div>

        <div class="list">
            @forelse ($quiz['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">#{{ $item['position'] }}</span>
                        <span class="badge">{{ $item['source']['family'] }}</span>
                        @if ($item['source']['language'])
                            <span class="badge">{{ $item['source']['language'] }}</span>
                        @endif
                        <a class="muted" href="{{ route('learning.sources.show', $item['source']['key']) }}">{{ $item['source']['path'] }}</a>
                    </div>
                    <h3>{{ $item['prompt'] }}</h3>
                    @if ($item['context'])
                        <p>{{ $item['context'] }}</p>
                    @endif
                    @if ($item['answer'])
                        <details>
                            <summary class="button">Reveal answer</summary>
                            <p>{{ $item['answer'] }}</p>
                        </details>
                    @endif
                    <a class="button primary" href="{{ route('practice.content-drill', ['record_id' => $item['id'], 'source_key' => $item['source']['key']]) }}">Build coding drill</a>
                    @if ($item['code'])
                        <pre><code>{{ $item['code'] }}</code></pre>
                    @endif
                </article>
            @empty
                <article class="item">
                    <h3>No practice cards found</h3>
                    <p class="muted">Try fewer filters or a broader keyword.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

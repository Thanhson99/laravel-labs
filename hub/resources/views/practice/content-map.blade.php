@extends('learning.layout', ['title' => 'Content To Practice Map'])

@section('content')
    <section class="hero">
        <span class="badge">Content mapped to code</span>
        <h1>Learning content is connected directly to Laravel coding work.</h1>
        <p>
            This page reads content and question sources, detects related technologies,
            and recommends native Laravel exercises you can code immediately.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.content-map') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="auth, api, docker, test...">
        </label>
        <label>
            Family
            <select name="family">
                <option value="">All</option>
                @foreach (['laravel', 'php', 'interview', 'vibe-coding'] as $family)
                    <option value="{{ $family }}" @selected($filters['family'] === $family)>{{ $family }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Language
            <select name="language">
                <option value="">All</option>
                @foreach (['en', 'vi'] as $language)
                    <option value="{{ $language }}" @selected($filters['language'] === $language)>{{ $language }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Technology
            <select name="technology">
                <option value="">All</option>
                @foreach ($result['meta']['technologies'] as $technology)
                    <option value="{{ $technology }}" @selected($filters['technology'] === $technology)>{{ $technology }}</option>
                @endforeach
            </select>
        </label>
        <button class="button primary" type="submit">Map to practice</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>Mapped Practice Tasks</h2>
            <a class="button" href="{{ route('api.practice.content-map', request()->query()) }}">Open map API</a>
        </div>
        <div class="list">
            @forelse ($result['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['source']['path'] }}</span>
                    </div>
                    <h3>{{ $item['content']['title'] }}</h3>
                    <p>{{ $item['task'] }}</p>
                    <p class="muted">{{ $item['source']['title'] }}</p>
                    @if ($item['practice']['slug'])
                        <a class="button" href="{{ route('practice.show', $item['practice']['slug']) }}">{{ $item['practice']['title'] }}</a>
                    @endif
                    <a class="button primary" href="{{ route('practice.content-drill', ['source_key' => $item['source']['key'], 'technology' => $item['technology']]) }}">Build drill</a>
                    <a class="button" href="{{ route('learning.sources.show', $item['source']['key']) }}">Open source JSON</a>
                </article>
            @empty
                <article class="item">
                    <h3>No mapped content</h3>
                    <p class="muted">Try another family, language, technology, or search term.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

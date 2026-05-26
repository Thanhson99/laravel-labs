@extends('learning.layout', ['title' => 'Technology Pipelines'])

@section('content')
    <section class="hero">
        <span class="badge">Technology pipelines</span>
        <h1>Technology pipelines from JSON content.</h1>
        <p>
            Browse every inferred technology from the content records, then open the full
            code-to-evidence learning pipeline for that technology.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-pipelines') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, cache...">
        </label>
        <label>
            Family
            <input type="search" name="family" value="{{ $filters['family'] }}" placeholder="laravel">
        </label>
        <label>
            Language
            <input type="search" name="language" value="{{ $filters['language'] }}" placeholder="en">
        </label>
        <button class="button primary" type="submit">Find pipelines</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $pipelines['meta']['pipeline_count'] }} pipelines from {{ $pipelines['meta']['record_count'] }} records</h2>
            <a class="button" href="{{ route('api.practice.technology-pipelines', request()->query()) }}">Open pipelines API</a>
            <a class="button primary" href="{{ route('practice.technology-quality-plan', request()->query()) }}">Open quality plan</a>
            <a class="button" href="{{ route('practice.technology-matrix', request()->query()) }}">Open matrix</a>
        </div>

        <div class="list">
            @forelse ($pipelines['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['record_count'] }} records</span>
                        <span class="muted">{{ $item['source_count'] }} sources</span>
                    </div>
                    <h3>{{ $item['practice']['title'] }}</h3>
                    <p>{{ $item['sample']['title'] }}</p>
                    <p class="muted">{{ implode(', ', $item['sources']) }}</p>
                    <a class="button primary" href="{{ $item['pipeline_route'] }}">Open pipeline</a>
                    <a class="button" href="{{ $item['code_examples_route'] }}">Open code examples</a>
                    <a class="button" href="{{ $item['api_pipeline_route'] }}">Open pipeline API</a>
                </article>
            @empty
                <article class="item">
                    <h3>No technology pipelines found</h3>
                    <p class="muted">Try broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

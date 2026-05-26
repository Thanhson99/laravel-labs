@extends('learning.layout', ['title' => 'Technology Practice Matrix'])

@section('content')
    <section class="hero">
        <span class="badge">Technology matrix</span>
        <h1>Technology map from source files to coding exercises.</h1>
        <p>
            The matrix groups content and question records by technology,
            then connects each technology to exercises, drills, and source files for Laravel practice.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-matrix') }}">
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
        <button class="button primary" type="submit">Build matrix</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $matrix['meta']['technology_count'] }} technologies from {{ $matrix['meta']['record_count'] }} records</h2>
            <a class="button" href="{{ route('api.practice.technology-matrix', request()->query()) }}">Open matrix API</a>
            <a class="button primary" href="{{ route('practice.technology-pipelines', request()->query()) }}">Open pipelines</a>
            <a class="button primary" href="{{ route('practice.technology-quality-plan', request()->query()) }}">Open quality plan</a>
            <a class="button primary" href="{{ route('practice.technology-code-examples', request()->query()) }}">Open code examples</a>
        </div>
        <div class="list">
            @forelse ($matrix['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['record_count'] }} records</span>
                        <span class="muted">{{ $item['source_count'] }} sources</span>
                    </div>
                    <h3>{{ $item['practice']['title'] }}</h3>
                    <p>{{ $item['sample']['title'] }}</p>
                    <p class="muted">{{ implode(', ', $item['sources']) }}</p>
                    @if ($item['practice']['slug'])
                        <a class="button" href="{{ route('practice.show', $item['practice']['slug']) }}">Open exercise</a>
                    @endif
                    <a class="button primary" href="{{ route('practice.technology-learning-pipeline', [$item['technology']] + array_filter($filters)) }}">Open pipeline</a>
                    <a class="button primary" href="{{ route('practice.content-drill', $item['drill_query']) }}">Open sample drill</a>
                    <a class="button" href="{{ route('practice.question-drills', ['technology' => $item['technology']] + array_filter($filters)) }}">Drills for technology</a>
                    <a class="button" href="{{ route('practice.source-pack', $item['sample']['source_key']) }}">Source pack</a>
                </article>
            @empty
                <article class="item">
                    <h3>No technology records found</h3>
                    <p class="muted">Try broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

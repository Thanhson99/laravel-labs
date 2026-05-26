@extends('learning.layout', ['title' => 'Technology Portfolio Artifact'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $artifact['technology'] }}</span>
        <h1>{{ $artifact['title'] }}</h1>
        <p>
            This artifact turns content-backed implementation work into a reusable portfolio note:
            source coverage, changed files, verification, talking points, and a README template.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-portfolio-artifact', $artifact['technology']) }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, policy, cache...">
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
        <button class="button primary" type="submit">Build portfolio</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Portfolio headline</span>
            </div>
            <h2>{{ $artifact['portfolio']['headline'] }}</h2>
            <a class="button" href="{{ route('practice.technology-commit-plan', [$artifact['technology']] + request()->query()) }}">Open commit plan</a>
            <a class="button primary" href="{{ route('practice.technology-interview-pack', [$artifact['technology']] + request()->query()) }}">Open interview pack</a>
            <a class="button" href="{{ route('api.practice.technology-portfolio-artifact', [$artifact['technology']] + request()->query()) }}">Open portfolio API</a>
        </article>
    </section>

    <section class="section">
        <h2>Summary</h2>
        <div class="list">
            @foreach ($artifact['portfolio']['summary'] as $item)
                <article class="item">
                    <p>{{ $item }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Source Coverage</h2>
        <div class="list">
            @foreach ($artifact['portfolio']['source_coverage'] as $source)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $source['record_id'] }}</span>
                        <span class="muted">{{ $source['source_path'] }}</span>
                    </div>
                    <h3>{{ $source['title'] }}</h3>
                    <p>{{ $source['task'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Interview Talking Points</h2>
        <div class="list">
            @foreach ($artifact['portfolio']['interview_talking_points'] as $point)
                <article class="item">
                    <p>{{ $point }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>README Template</h2>
        <article class="item">
            <pre><code>{{ $artifact['portfolio']['readme_template'] }}</code></pre>
        </article>
    </section>
@endsection

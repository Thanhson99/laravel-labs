@extends('learning.layout', ['title' => 'Technology Learning Pipeline'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $pipeline['technology'] }}</span>
        <h1>{{ $pipeline['title'] }}</h1>
        <p>
            This page connects the full content-backed practice flow for one technology:
            code examples, implementation, commit, portfolio, interview, assessment, remediation, mastery, review, and archive.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-learning-pipeline', $pipeline['technology']) }}">
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
        <button class="button primary" type="submit">Build pipeline</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $pipeline['record_count'] }} records in this pipeline</h2>
            <a class="button" href="{{ route('api.practice.technology-learning-pipeline', [$pipeline['technology']] + request()->query()) }}">Open pipeline API</a>
        </div>
        <div class="list">
            @foreach ($pipeline['stages'] as $stage)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $stage['step'] }}</span>
                    </div>
                    <h3>{{ $stage['label'] }}</h3>
                    <p>{{ $stage['purpose'] }}</p>
                    <a class="button primary" href="{{ $stage['route'] }}">Open stage</a>
                    <a class="button" href="{{ $stage['api_route'] }}">Open API</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Sample Records</h2>
        <div class="list">
            @forelse ($pipeline['sample_records'] as $record)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $record['record_id'] }}</span>
                        <span class="muted">{{ $record['source_path'] }}</span>
                    </div>
                    <h3>{{ $record['title'] }}</h3>
                    <p>{{ $record['task'] }}</p>
                </article>
            @empty
                <article class="item">
                    <h3>No records found</h3>
                    <p class="muted">Try a broader search or a different technology key.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

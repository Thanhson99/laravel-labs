@extends('learning.layout', ['title' => 'Technology Evidence Archive'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $archive['technology'] }}</span>
        <h1>{{ $archive['title'] }}</h1>
        <p>
            This archive turns spaced review work into retrieval keys, proof bundles,
            reuse targets, and prompts for later study or interview preparation.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-evidence-archive', $archive['technology']) }}">
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
        <button class="button primary" type="submit">Build archive</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Archive</span>
                <code>{{ $archive['archive_id'] }}</code>
            </div>
            <a class="button" href="{{ route('practice.technology-spaced-review', [$archive['technology']] + request()->query()) }}">Open spaced review</a>
            <a class="button" href="{{ route('api.practice.technology-evidence-archive', [$archive['technology']] + request()->query()) }}">Open archive API</a>
        </article>
    </section>

    <section class="section">
        <h2>Retrieval Keys</h2>
        <div class="list">
            @foreach ($archive['retrieval_keys'] as $key)
                <article class="item">
                    <code>{{ $key }}</code>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Proof Bundle</h2>
        <div class="list">
            @foreach ($archive['proof_bundle'] as $proof)
                <article class="item">
                    <h3>{{ $proof['label'] }}</h3>
                    <p>{{ $proof['detail'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Reuse Targets</h2>
        <div class="list">
            @foreach ($archive['reuse_targets'] as $target)
                <article class="item">
                    <p>{{ $target }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Retrieval Prompts</h2>
        <div class="list">
            @foreach ($archive['retrieval_prompts'] as $prompt)
                <article class="item">
                    <p>{{ $prompt }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection

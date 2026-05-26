@extends('learning.layout', ['title' => 'Content Practice Syllabus'])

@section('content')
    <section class="hero">
        <span class="badge">Practice syllabus</span>
        <h1>A practice syllabus generated from source content and technologies.</h1>
        <p>
            The syllabus groups technology phases, related source packs, queues, and sample workspaces
            so each learning step maps to Laravel implementation work.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.syllabus') }}">
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
        <label>
            Limit
            <input type="number" name="limit" min="1" max="100" value="{{ $filters['limit'] }}">
        </label>
        <button class="button primary" type="submit">Build syllabus</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $syllabus['meta']['phase_count'] }} phases from {{ $syllabus['meta']['record_count'] }} records</h2>
            <a class="button" href="{{ route('api.practice.syllabus', request()->query()) }}">Open syllabus API</a>
        </div>
        <div class="list">
            @foreach ($syllabus['phases'] as $phase)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Phase {{ $phase['phase'] }}</span>
                        <span class="badge">{{ $phase['technology'] }}</span>
                        <span class="muted">{{ $phase['record_count'] }} records</span>
                    </div>
                    <h3>{{ $phase['title'] }}</h3>
                    <a class="button primary" href="{{ route('practice.technology-board', ['technology' => $phase['technology'], 'family' => $filters['family'], 'language' => $filters['language'], 'search' => $filters['search']]) }}">Open technology board</a>
                    <a class="button" href="{{ route('practice.queue', array_filter($phase['queue_query'])) }}">Open queue</a>
                    @if ($phase['exercise']['slug'])
                        <a class="button" href="{{ route('practice.show', $phase['exercise']['slug']) }}">Open exercise</a>
                    @endif
                    <ul>
                        @foreach ($phase['done_when'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Source Packs</h2>
        <div class="list">
            @foreach ($syllabus['source_packs'] as $pack)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $pack['source']['family'] }}</span>
                        <span class="muted">{{ $pack['record_count'] }} records</span>
                    </div>
                    <h3>{{ $pack['source']['title'] }}</h3>
                    <p><code>{{ $pack['source']['path'] }}</code></p>
                    <a class="button" href="{{ route('practice.source-pack', $pack['source']['key']) }}">Open source pack</a>
                    @if ($pack['sample_workspace_query'])
                        <a class="button" href="{{ route('practice.record-workspace', $pack['sample_workspace_query']) }}">Open sample workspace</a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
@endsection

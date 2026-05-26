@extends('learning.layout', ['title' => 'Technology Quality Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Quality plan</span>
        <h1>Quality gates for technology pipelines.</h1>
        <p>
            Turn each JSON-backed technology pipeline into concrete verification commands,
            acceptance checks, and a quality-gate baseline before portfolio or interview work.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-quality-plan') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, test...">
        </label>
        <label>
            Family
            <input type="search" name="family" value="{{ $filters['family'] }}" placeholder="laravel">
        </label>
        <label>
            Language
            <input type="search" name="language" value="{{ $filters['language'] }}" placeholder="en">
        </label>
        <button class="button primary" type="submit">Build quality plan</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $qualityPlan['meta']['quality_plan_count'] }} quality plans: {{ $qualityPlan['meta']['minimum_status'] }}</h2>
            <a class="button" href="{{ route('api.practice.technology-quality-plan', request()->query()) }}">Open quality API</a>
            <a class="button" href="{{ route('practice.technology-pipelines', request()->query()) }}">Open pipelines</a>
        </div>

        <div class="list">
            @forelse ($qualityPlan['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['quality_gate']['status'] }}</span>
                        <span class="muted">{{ $item['record_count'] }} records</span>
                    </div>
                    <h3>{{ $item['technology'] }} verification baseline</h3>
                    <p>{{ $item['risk_note'] }}</p>

                    <h4>Commands</h4>
                    <ul>
                        @foreach ($item['commands'] as $command)
                            <li><code>{{ $command }}</code></li>
                        @endforeach
                    </ul>

                    <h4>Acceptance checks</h4>
                    <ul>
                        @foreach ($item['acceptance_checks'] as $check)
                            <li>{{ $check }}</li>
                        @endforeach
                    </ul>

                    <a class="button primary" href="{{ $item['pipeline_route'] }}">Open pipeline</a>
                    <a class="button" href="{{ $item['api_pipeline_route'] }}">Open pipeline API</a>
                </article>
            @empty
                <article class="item">
                    <h3>No quality plans found</h3>
                    <p class="muted">Try broader filters.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

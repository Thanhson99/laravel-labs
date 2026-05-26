@extends('learning.layout', ['title' => 'Technology Skill Assessment'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $assessment['technology'] }}</span>
        <h1>{{ $assessment['title'] }}</h1>
        <p>
            This assessment scores source understanding, Laravel layer placement,
            implementation evidence, verification discipline, and communication.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-skill-assessment', $assessment['technology']) }}">
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
        <button class="button primary" type="submit">Build assessment</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Score</span>
                <span class="muted">Pass score {{ $assessment['pass_score'] }} / {{ $assessment['max_score'] }}</span>
            </div>
            <p>{{ $assessment['readiness']['ready_signal'] }}</p>
            <p class="muted">{{ $assessment['readiness']['repeat_signal'] }}</p>
            <a class="button" href="{{ route('practice.technology-interview-pack', [$assessment['technology']] + request()->query()) }}">Open interview pack</a>
            <a class="button primary" href="{{ route('practice.technology-remediation-plan', [$assessment['technology']] + request()->query()) }}">Open remediation plan</a>
            <a class="button" href="{{ route('api.practice.technology-skill-assessment', [$assessment['technology']] + request()->query()) }}">Open assessment API</a>
        </article>
    </section>

    <section class="section">
        <h2>Rubric</h2>
        <div class="list">
            @foreach ($assessment['rubric'] as $row)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $row['points'] }} points</span>
                    </div>
                    <h3>{{ $row['criterion'] }}</h3>
                    <p>{{ $row['evidence'] }}</p>
                    <p class="muted">{{ $row['pass_signal'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Improvement Tasks</h2>
        <div class="list">
            @foreach ($assessment['improvement_tasks'] as $task)
                <article class="item">
                    <p>{{ $task }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection

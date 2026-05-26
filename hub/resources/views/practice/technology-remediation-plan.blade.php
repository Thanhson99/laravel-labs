@extends('learning.layout', ['title' => 'Technology Remediation Plan'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $plan['technology'] }}</span>
        <h1>{{ $plan['title'] }}</h1>
        <p>
            This plan turns the assessment rubric into concrete repair tasks, focus files,
            verification commands, and next routes for another practice pass.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-remediation-plan', $plan['technology']) }}">
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
        <button class="button primary" type="submit">Build remediation</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Assessment</span>
                <span class="muted">Pass score {{ $plan['assessment']['pass_score'] }} / {{ $plan['assessment']['max_score'] }}</span>
            </div>
            <p>{{ $plan['assessment']['readiness']['repeat_signal'] }}</p>
            <a class="button" href="{{ route('practice.technology-skill-assessment', [$plan['technology']] + request()->query()) }}">Open assessment</a>
            <a class="button primary" href="{{ route('practice.technology-mastery-checkpoint', [$plan['technology']] + request()->query()) }}">Open mastery checkpoint</a>
            <a class="button" href="{{ route('api.practice.technology-remediation-plan', [$plan['technology']] + request()->query()) }}">Open remediation API</a>
        </article>
    </section>

    <section class="section">
        <h2>Repair Tasks</h2>
        <div class="list">
            @foreach ($plan['tasks'] as $task)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $task['step'] }}</span>
                        <code>{{ $task['focus_file'] }}</code>
                    </div>
                    <h3>{{ $task['label'] }}</h3>
                    <p>{{ $task['problem'] }}</p>
                    <p>{{ $task['action'] }}</p>
                    <p class="muted">{{ $task['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="list">
            @foreach ($plan['commands'] as $command)
                <article class="item">
                    <pre><code>{{ $command }}</code></pre>
                </article>
            @endforeach
        </div>
    </section>
@endsection

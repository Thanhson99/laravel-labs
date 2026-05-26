@extends('learning.layout', ['title' => 'Practice Refactor Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Refactor</span>
        <h1>Refactor lab turns bug fixes into tested code improvement work.</h1>
        <p>
            This lab turns bug-fix drills into refactor goals, safe steps,
            guardrails, verification commands, evidence, and architecture checks.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.refactor-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build refactor tasks</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_bug_fix_lab']['drill_count'] }} drills</span>
                @foreach ($lab['source_bug_fix_lab']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.bug-fix-lab', request()->query()) }}">Open bug-fix lab</a>
            <a class="button" href="{{ route('api.practice.refactor-lab', request()->query()) }}">Open refactor API</a>
        </article>
    </section>

    <section class="section">
        <h2>Refactor Rules</h2>
        <div class="list">
            @foreach ($lab['refactor_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Refactor Tasks</h2>
        <div class="list">
            @foreach ($lab['refactor_tasks'] as $task)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $task['round'] }}</span>
                        <span class="badge">{{ $task['target_file'] }}</span>
                    </div>
                    <h3>{{ $task['technology_segment'] }}</h3>
                    <p>{{ $task['refactor_goal'] }}</p>
                    <h4>Safe Steps</h4>
                    <ol>
                        @foreach ($task['safe_steps'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>
                    <h4>Guardrails</h4>
                    <ul>
                        @foreach ($task['guardrails'] as $guardrail)
                            <li>{{ $guardrail }}</li>
                        @endforeach
                    </ul>
                    <p><strong>Command:</strong> <code>{{ $task['verification_command'] }}</code></p>
                    <p><strong>Evidence:</strong> {{ $task['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Architecture Checks</h2>
                <ul>
                    @foreach ($lab['architecture_checks'] as $check)
                        <li>{{ $check }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Refactor Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Practice Challenge Execution Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Challenge execution</span>
        <h1>Challenge execution turns next challenge recommendations into concrete coding steps.</h1>
        <p>
            This lab turns next challenge cards into execution steps,
            route names, verification commands, evidence, exit criteria, and a session summary.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.challenge-execution-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build execution plan</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_challenge_lab']['challenge_count'] }} challenges</span>
                <span class="badge">{{ $lab['session_summary']['total_estimated_minutes'] }} minutes</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.next-challenge-lab', request()->query()) }}">Open next challenge lab</a>
            <a class="button" href="{{ route('api.practice.challenge-execution-lab', request()->query()) }}">Open execution API</a>
        </article>
    </section>

    <section class="section">
        <h2>Execution Rules</h2>
        <div class="list">
            @foreach ($lab['execution_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Execution Steps</h2>
        <div class="list">
            @foreach ($lab['execution_steps'] as $step)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $step['step'] }}</span>
                        <span class="badge">{{ $step['challenge_type'] }}</span>
                        <span class="badge">{{ $step['estimated_minutes'] }} minutes</span>
                    </div>
                    <h3>{{ $step['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $step['route_name'] }}</code></p>
                    <h4>Execution Order</h4>
                    <ol>
                        @foreach ($step['execution_order'] as $order)
                            <li>{{ $order }}</li>
                        @endforeach
                    </ol>
                    <p><strong>Command:</strong> <code>{{ $step['verification_command'] }}</code></p>
                    <h4>Evidence To Submit</h4>
                    <ul>
                        @foreach ($step['evidence_to_submit'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>
                    <h4>Exit Criteria</h4>
                    <ul>
                        @foreach ($step['exit_criteria'] as $criteria)
                            <li>{{ $criteria }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Challenge Execution Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

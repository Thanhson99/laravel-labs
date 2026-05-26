@extends('learning.layout', ['title' => 'Practice Checkpoint Exam Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Checkpoint exam</span>
        <h1>Checkpoint exam lab validates one technology through a timed coding task.</h1>
        <p>
            This lab turns mentor feedback into a timed checkpoint,
            warmup questions, coding tasks, oral review, and pass criteria.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.checkpoint-exam-lab') }}">
        <label>
            Technology
            <input type="search" name="technology" value="{{ $filters['technology'] }}" placeholder="api-validation">
        </label>
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Limit
            <input type="number" name="limit" min="1" max="10" value="{{ $filters['limit'] }}">
        </label>
        <button class="button primary" type="submit">Build exam</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $exam['technology'] }}</span>
                <span class="muted">{{ $exam['duration_minutes'] }} min</span>
            </div>
            <h2>{{ $exam['title'] }}</h2>
            <a class="button" href="{{ route('practice.mentor-feedback-lab', request()->query()) }}">Open mentor feedback</a>
            <a class="button" href="{{ route('api.practice.checkpoint-exam-lab', request()->query()) }}">Open exam API</a>
        </article>
    </section>

    <section class="section">
        <h2>Warmup Questions</h2>
        <div class="list">
            @foreach ($exam['warmup_questions'] as $question)
                <article class="item"><p>{{ $question }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Coding Tasks</h2>
        <div class="list">
            @foreach ($exam['coding_tasks'] as $task)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Task {{ $task['position'] }}</span>
                        <code>{{ $task['source_path'] }}</code>
                    </div>
                    <h3>{{ $task['prompt'] }}</h3>
                    <a class="button primary" href="{{ route('practice.record-workspace', $task['workspace_query']) }}">Open workspace</a>
                    <a class="button" href="{{ route('practice.tdd-lab', $task['workspace_query']) }}">Open TDD lab</a>
                    <ul>
                        @foreach ($task['expected_evidence'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Pass Criteria</h2>
                <ul>
                    @foreach ($exam['pass_criteria'] as $criterion)
                        <li>{{ $criterion }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Checkpoint Progress Payload</h2>
                <pre><code>{{ json_encode($exam['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

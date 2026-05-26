@extends('learning.layout', ['title' => 'Practice Mentor Feedback Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Mentor feedback lab</span>
        <h1>Mentor feedback lab turns capstone work into guided review.</h1>
        <p>
            This lab turns capstone tasks into task-level feedback,
            risk notes, mentor questions, review focus, and action items.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.mentor-feedback-lab') }}">
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
        <button class="button primary" type="submit">Build feedback</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $feedback['technology'] }}</span>
                <span class="muted">{{ $feedback['meta']['task_count'] }} tasks</span>
            </div>
            <h2>{{ $feedback['title'] }}</h2>
            <p>{{ $feedback['mission'] }}</p>
            <a class="button" href="{{ route('practice.capstone-lab', request()->query()) }}">Open capstone lab</a>
            <a class="button" href="{{ route('api.practice.mentor-feedback-lab', request()->query()) }}">Open feedback API</a>
        </article>
    </section>

    <section class="section">
        <h2>Task Feedback</h2>
        <div class="list">
            @foreach ($feedback['feedback_items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Task {{ $item['position'] }}</span>
                        <code>{{ $item['source_path'] }}</code>
                    </div>
                    <h3>{{ $item['question'] }}</h3>
                    <p>{{ $item['mentor_comment'] }}</p>
                    <p>{{ $item['risk'] }}</p>
                    <p>{{ $item['action_item'] }}</p>
                    <a class="button primary" href="{{ route('practice.record-workspace', $item['workspace_query']) }}">Open workspace</a>
                    <a class="button" href="{{ route('practice.tdd-lab', $item['workspace_query']) }}">Open TDD lab</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Mentor Questions</h2>
                <ul>
                    @foreach ($feedback['mentor_questions'] as $question)
                        <li>{{ $question }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Mentor Feedback Progress Payload</h2>
                <pre><code>{{ json_encode($feedback['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

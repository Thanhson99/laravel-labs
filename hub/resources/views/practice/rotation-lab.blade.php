@extends('learning.layout', ['title' => 'Practice Rotation Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Rotation lab</span>
        <h1>Rotation lab turns the mastery path into a daily practice schedule.</h1>
        <p>
            This lab turns the mastery path into daily practice entries,
            each with a technology, focus, lab link, and required output.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.rotation-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build rotation</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $rotation['meta']['day_count'] }} days</span>
                <span class="muted">{{ $rotation['meta']['milestone_count'] }} milestones</span>
            </div>
            <h2>{{ $rotation['title'] }}</h2>
            <a class="button" href="{{ route('practice.mastery-path-lab', request()->query()) }}">Open mastery path</a>
            <a class="button" href="{{ route('api.practice.rotation-lab', request()->query()) }}">Open rotation API</a>
        </article>
    </section>

    <section class="section">
        <h2>Daily Rotation</h2>
        <div class="list">
            @foreach ($rotation['schedule'] as $day)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Day {{ $day['day'] }}</span>
                        <span class="badge">{{ $day['technology'] }}</span>
                    </div>
                    <h3>{{ $day['focus'] }}</h3>
                    <p>{{ $day['output'] }}</p>
                    <a class="button primary" href="{{ route('practice.capstone-lab', $day['capstone_query']) }}">Open capstone</a>
                    <a class="button" href="{{ route('practice.checkpoint-exam-lab', $day['checkpoint_query']) }}">Open checkpoint</a>
                    <a class="button" href="{{ route('practice.mentor-feedback-lab', $day['mentor_feedback_query']) }}">Open mentor feedback</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Rotation Progress Payload</h2>
            <pre><code>{{ json_encode($rotation['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

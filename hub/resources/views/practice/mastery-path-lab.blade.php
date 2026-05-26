@extends('learning.layout', ['title' => 'Practice Mastery Path Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Mastery path</span>
        <h1>Mastery path lab turns the syllabus into a multi-technology practice path.</h1>
        <p>
            This lab turns the syllabus into technology milestones,
            each with a capstone, checkpoint exam, mentor feedback, and portfolio evidence.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.mastery-path-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Phase limit
            <input type="number" name="phase_limit" min="1" max="10" value="{{ $filters['phase_limit'] }}">
        </label>
        <label>
            Tasks per phase
            <input type="number" name="tasks_per_phase" min="1" max="10" value="{{ $filters['tasks_per_phase'] }}">
        </label>
        <button class="button primary" type="submit">Build mastery path</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $path['meta']['milestone_count'] }} milestones</span>
                <span class="muted">{{ $path['meta']['record_count'] }} records</span>
            </div>
            <h2>{{ $path['title'] }}</h2>
            <a class="button" href="{{ route('practice.syllabus', request()->query()) }}">Open syllabus</a>
            <a class="button" href="{{ route('api.practice.mastery-path-lab', request()->query()) }}">Open mastery path API</a>
        </article>
    </section>

    <section class="section">
        <h2>Milestones</h2>
        <div class="list">
            @foreach ($path['milestones'] as $milestone)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Phase {{ $milestone['phase'] }}</span>
                        <span class="badge">{{ $milestone['technology'] }}</span>
                        <span class="muted">{{ $milestone['record_count'] }} records</span>
                    </div>
                    <h3>{{ $milestone['title'] }}</h3>
                    <a class="button primary" href="{{ route('practice.capstone-lab', $milestone['capstone_query']) }}">Open capstone</a>
                    <a class="button" href="{{ route('practice.checkpoint-exam-lab', $milestone['checkpoint_query']) }}">Open checkpoint</a>
                    <a class="button" href="{{ route('practice.mentor-feedback-lab', $milestone['mentor_feedback_query']) }}">Open mentor feedback</a>
                    <ul>
                        @foreach ($milestone['done_when'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Source Packs</h2>
                <ul>
                    @foreach ($path['source_packs'] as $pack)
                        <li><code>{{ $pack['source']['path'] }}</code></li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Mastery Path Progress Payload</h2>
                <pre><code>{{ json_encode($path['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

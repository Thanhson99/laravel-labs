@extends('learning.layout', ['title' => 'Laravel Practice Hub'])

@section('content')
    <section class="hero">
        <span class="badge">Code-first practice</span>
        <h1>This Laravel project is a hands-on practice app, not a JSON viewer.</h1>
        <p>
            Learning content is reference material. The main workflow is to open a coding exercise,
            implement it inside the Laravel app, run commands and tests, then use quizzes or the question bank for review.
        </p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Start Here</h2>
            <div class="nav">
                <a class="button primary" href="{{ route('practice.index') }}">Open practice workspace</a>
                <a class="button" href="{{ route('learning.labs') }}">Generate extra labs</a>
                <a class="button" href="{{ route('learning.quiz') }}">Review with quiz</a>
            </div>
        </div>
        <div class="grid">
            @foreach ($workflow as $step)
                <a class="panel" href="{{ $step['href'] }}">
                    <h3>{{ $step['title'] }}</h3>
                    <p class="muted">{{ $step['body'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Practice Exercises In This Laravel Project</h2>
            <a class="button" href="{{ route('practice.index') }}">Open all exercises</a>
        </div>
        <div class="list">
            @foreach ($featuredExercises as $exercise)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $exercise['track'] }}</span>
                        <span class="muted">native Laravel practice</span>
                    </div>
                    <h3>{{ $exercise['title'] }}</h3>
                    <p>{{ $exercise['objective'] }}</p>
                    <p class="muted">{{ $exercise['why'] }}</p>
                    <div class="nav">
                        <a class="button" href="{{ route('practice.show', $exercise['slug']) }}">Open exercise</a>
                        <a class="button" href="{{ route('practice.index', ['track' => $exercise['track']]) }}">Open track</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Technology Tracks</h2>
        <div class="grid">
            @foreach ($practiceTracks as $track)
                <a class="panel" href="{{ route('practice.index', ['track' => $track['slug']]) }}">
                    <span class="badge">{{ $track['slug'] }}</span>
                    <h3>{{ $track['name'] }}</h3>
                    <p class="muted">{{ $track['summary'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Technology Integrated For Practice</h2>
        <div class="grid">
            @foreach ($integrations as $integration)
                <article class="panel">
                    <div class="meta">
                        <span class="badge {{ $integration['status'] }}">{{ $integration['status'] }}</span>
                        <h3>{{ $integration['label'] }}</h3>
                    </div>
                    <p class="muted">{{ $integration['note'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Reference Tools After Coding</h2>
        <div class="grid">
            <article class="panel">
                <h3>Generated Labs</h3>
                <p class="muted">Generate extra lab prompts from the reference content after you finish native exercises.</p>
                <a class="button" href="{{ route('learning.labs') }}">Open generated labs</a>
            </article>
            <article class="panel">
                <h3>Quiz</h3>
                <p class="muted">Review the same topic after coding.</p>
                <a class="button" href="{{ route('learning.quiz') }}">Open quiz</a>
            </article>
            <article class="panel">
                <h3>Question Reference</h3>
                <p class="muted">Search the original question bank when you need theory context.</p>
                <a class="button" href="{{ route('learning.questions') }}">Open reference</a>
            </article>
            <article class="panel">
                <h3>Analytics</h3>
                <p class="muted">Inspect coverage and code-heavy topics in the reference content.</p>
                <a class="button" href="{{ route('learning.analytics') }}">Open analytics</a>
            </article>
        </div>
    </section>
@endsection

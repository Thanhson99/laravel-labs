@extends('learning.layout', ['title' => $plan['title']])

@section('content')
    <section class="hero">
        <span class="badge">Practice Session</span>
        <h1>{{ $plan['title'] }}</h1>
        <p>
            Choose a track, code a small exercise, run tests, then refactor.
            This page contains native Laravel exercises rather than JSON-rendered tasks.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.sessions.today') }}">
        <label>
            Track
            <select name="track">
                <option value="">All tracks</option>
                @foreach ($tracks as $track)
                    <option value="{{ $track['slug'] }}" @selected($selectedTrack === $track['slug'])>{{ $track['name'] }}</option>
                @endforeach
            </select>
        </label>
        <button class="button primary" type="submit">Build session</button>
        <a class="button" href="{{ route('api.practice.session-plan', array_filter(['track' => $selectedTrack])) }}">Open session API</a>
    </form>

    <section class="section">
        <h2>Session Exercises</h2>
        <div class="list">
            @forelse ($plan['exercises'] as $exercise)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $exercise['track'] }}</span>
                    </div>
                    <h3><a href="{{ route('practice.show', $exercise['slug']) }}">{{ $exercise['title'] }}</a></h3>
                    <p>{{ $exercise['objective'] }}</p>
                    <a class="button" href="{{ route('practice.show', $exercise['slug']) }}">Open exercise</a>
                </article>
            @empty
                <article class="item">
                    <h3>No session exercises</h3>
                    <p class="muted">Choose another track.</p>
                </article>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Commands</h2>
                <pre><code>@foreach ($plan['commands'] as $command){{ $command }}
@endforeach</code></pre>
            </article>
            <article class="panel">
                <h2>Done When</h2>
                <ul>
                    @foreach ($plan['done_when'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
        </div>
    </section>
@endsection

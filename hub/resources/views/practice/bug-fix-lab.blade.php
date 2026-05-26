@extends('learning.layout', ['title' => 'Practice Bug Fix Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Bug fix</span>
        <h1>Bug fix lab turns live coding rounds into layer-aware debugging work.</h1>
        <p>
            This lab turns live coding rounds into bug reports, diagnosis steps,
            patch targets, verification commands, pass signals, and evidence.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.bug-fix-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build bug-fix drills</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_session']['round_count'] }} rounds</span>
                @foreach ($lab['source_session']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.live-coding-lab', request()->query()) }}">Open live coding lab</a>
            <a class="button" href="{{ route('api.practice.bug-fix-lab', request()->query()) }}">Open bug-fix API</a>
        </article>
    </section>

    <section class="section">
        <h2>Debugging Rules</h2>
        <div class="list">
            @foreach ($lab['debugging_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Bug Drills</h2>
        <div class="list">
            @foreach ($lab['bug_drills'] as $drill)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $drill['round'] }}</span>
                        <span class="badge">{{ $drill['patch_target'] }}</span>
                    </div>
                    <h3>{{ $drill['technology_segment'] }}</h3>
                    <p>{{ $drill['bug_report'] }}</p>
                    <ol>
                        @foreach ($drill['diagnosis_steps'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>
                    <p><strong>Command:</strong> <code>{{ $drill['verification_command'] }}</code></p>
                    <p><strong>Pass:</strong> {{ $drill['pass_signal'] }}</p>
                    <p><strong>Evidence:</strong> {{ $drill['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Review Questions</h2>
                <ul>
                    @foreach ($lab['review_questions'] as $question)
                        <li>{{ $question }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Bug Fix Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

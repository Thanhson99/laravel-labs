@extends('learning.layout', ['title' => 'Practice Competency Map Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Competency map</span>
        <h1>Competency map lab turns mastery evidence into a skill map.</h1>
        <p>
            This lab turns mastery evidence into competency levels,
            proof summaries, next actions, map summaries, and a progress payload.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.competency-map-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build competency map</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_mastery_lab']['evidence_card_count'] }} evidence cards</span>
                <span class="badge">{{ $lab['map_summary']['ready_count'] }} ready</span>
                <span class="badge">{{ $lab['map_summary']['needs_practice_count'] }} reinforce</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.mastery-evidence-lab', request()->query()) }}">Open mastery evidence</a>
            <a class="button" href="{{ route('api.practice.competency-map-lab', request()->query()) }}">Open competency API</a>
        </article>
    </section>

    <section class="section">
        <h2>Competency Rules</h2>
        <div class="list">
            @foreach ($lab['competency_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Competencies</h2>
        <div class="list">
            @foreach ($lab['competencies'] as $competency)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $competency['readiness'] }}</span>
                        <span class="badge">{{ $competency['evidence_score'] }} score</span>
                    </div>
                    <h3>{{ $competency['technology_segment'] }}</h3>
                    <p>{{ $competency['proof_summary'] }}</p>
                    <h4>Levels</h4>
                    <ul>
                        @foreach ($competency['competency_levels'] as $level)
                            <li>{{ $level['skill'] }}: {{ $level['level'] }}</li>
                        @endforeach
                    </ul>
                    <p><strong>Next:</strong> {{ $competency['next_action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Competency Map Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

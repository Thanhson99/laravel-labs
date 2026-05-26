@extends('learning.layout', ['title' => 'Practice Mastery Evidence Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Mastery evidence</span>
        <h1>Mastery evidence lab converts repetition schedules into proof of skill.</h1>
        <p>
            This lab turns spaced repetition checkpoints into evidence cards,
            evidence scores, missing evidence, next harder labs, and a progress payload.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.mastery-evidence-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build mastery evidence</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_repetition_lab']['review_count'] }} reviews</span>
                @foreach ($lab['source_repetition_lab']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.spaced-repetition-lab', request()->query()) }}">Open spaced repetition lab</a>
            <a class="button" href="{{ route('api.practice.mastery-evidence-lab', request()->query()) }}">Open evidence API</a>
        </article>
    </section>

    <section class="section">
        <h2>Mastery Rules</h2>
        <div class="list">
            @foreach ($lab['mastery_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Evidence Cards</h2>
        <div class="list">
            @foreach ($lab['evidence_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $card['status'] }}</span>
                        <span class="badge">{{ $card['evidence_score'] }} score</span>
                    </div>
                    <h3>{{ $card['technology_segment'] }}</h3>
                    <p><strong>Review days:</strong> {{ implode(', ', $card['review_days']) }}</p>
                    <h4>Proof Items</h4>
                    <ul>
                        @foreach ($card['proof_items'] as $proof)
                            <li>{{ $proof }}</li>
                        @endforeach
                    </ul>
                    <h4>Missing Evidence</h4>
                    <ul>
                        @foreach ($card['missing_evidence'] as $missing)
                            <li>{{ $missing }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Next Harder Labs</h2>
                <ul>
                    @foreach ($lab['next_harder_labs'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Mastery Evidence Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

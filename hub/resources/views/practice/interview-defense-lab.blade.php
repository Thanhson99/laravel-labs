@extends('learning.layout', ['title' => 'Practice Interview Defense Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Interview defense</span>
        <h1>Interview defense lab turns release evidence into technical answers.</h1>
        <p>
            This lab turns release readiness artifacts into defense cards,
            answer outlines, evidence to cite, follow-up risks, and a scoring rubric.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.interview-defense-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build defense cards</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_release_lab']['release_item_count'] }} release items</span>
                @foreach ($lab['source_release_lab']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.release-readiness-lab', request()->query()) }}">Open release lab</a>
            <a class="button" href="{{ route('api.practice.interview-defense-lab', request()->query()) }}">Open defense API</a>
        </article>
    </section>

    <section class="section">
        <h2>Defense Rules</h2>
        <div class="list">
            @foreach ($lab['defense_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Defense Cards</h2>
        <div class="list">
            @foreach ($lab['defense_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Round {{ $card['round'] }}</span>
                        <span class="badge">{{ $card['technology_segment'] }}</span>
                    </div>
                    <h3>{{ $card['question'] }}</h3>
                    <h4>Answer Outline</h4>
                    <ol>
                        @foreach ($card['answer_outline'] as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ol>
                    <h4>Evidence To Cite</h4>
                    <ul>
                        @foreach ($card['evidence_to_cite'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>
                    <p><strong>Follow-up risk:</strong> {{ $card['follow_up_risk'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Scoring Rubric</h2>
                <ul>
                    @foreach ($lab['scoring_rubric'] as $item)
                        <li>{{ $item['label'] }} - {{ $item['points'] }} points</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Defense Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

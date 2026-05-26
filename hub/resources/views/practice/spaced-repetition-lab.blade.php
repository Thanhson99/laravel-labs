@extends('learning.layout', ['title' => 'Practice Spaced Repetition Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Spaced repetition</span>
        <h1>Spaced repetition lab turns knowledge gaps into a recurring coding schedule.</h1>
        <p>
            This lab turns gap cards into day 1, 3, and 7 review schedules
            with coding actions, recall prompts, verification hints, and promotion criteria.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.spaced-repetition-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build review schedule</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_gap_lab']['gap_count'] }} gaps</span>
                @foreach ($lab['source_gap_lab']['technology_coverage'] as $technology)
                    <span class="badge">{{ $technology }}</span>
                @endforeach
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.knowledge-gap-lab', request()->query()) }}">Open knowledge gap lab</a>
            <a class="button" href="{{ route('api.practice.spaced-repetition-lab', request()->query()) }}">Open repetition API</a>
        </article>
    </section>

    <section class="section">
        <h2>Review Rules</h2>
        <div class="list">
            @foreach ($lab['review_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Schedule</h2>
        <div class="list">
            @foreach ($lab['review_schedule'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Day {{ $item['day'] }}</span>
                        <span class="badge">{{ $item['technology_segment'] }}</span>
                    </div>
                    <h3>{{ $item['review_goal'] }}</h3>
                    <p><strong>Code:</strong> {{ $item['coding_action'] }}</p>
                    <p><strong>Recall:</strong> {{ $item['recall_prompt'] }}</p>
                    <p><strong>Verify:</strong> <code>{{ $item['verification_hint'] }}</code></p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Promotion Criteria</h2>
                <ul>
                    @foreach ($lab['promotion_criteria'] as $criterion)
                        <li>{{ $criterion }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Spaced Repetition Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </div>
    </section>
@endsection

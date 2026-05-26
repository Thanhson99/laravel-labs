@extends('learning.layout', ['title' => 'Technology Spaced Review'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $review['technology'] }}</span>
        <h1>{{ $review['title'] }}</h1>
        <p>
            This review schedule converts a mastery checkpoint into day 1, day 3,
            and day 7 recall, rebuild, and defense practice.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-spaced-review', $review['technology']) }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, policy, cache...">
        </label>
        <label>
            Family
            <input type="search" name="family" value="{{ $filters['family'] }}" placeholder="laravel">
        </label>
        <label>
            Language
            <input type="search" name="language" value="{{ $filters['language'] }}" placeholder="en">
        </label>
        <label>
            Limit
            <input type="number" name="limit" value="{{ $filters['limit'] }}" min="1" max="100">
        </label>
        <button class="button primary" type="submit">Build review</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Checkpoint</span>
            </div>
            <p>{{ $review['checkpoint']['decision']['promote_when'] }}</p>
            <a class="button" href="{{ route('practice.technology-mastery-checkpoint', [$review['technology']] + request()->query()) }}">Open mastery checkpoint</a>
            <a class="button primary" href="{{ route('practice.technology-evidence-archive', [$review['technology']] + request()->query()) }}">Open evidence archive</a>
            <a class="button" href="{{ route('api.practice.technology-spaced-review', [$review['technology']] + request()->query()) }}">Open review API</a>
        </article>
    </section>

    <section class="section">
        <h2>Review Cards</h2>
        <div class="list">
            @foreach ($review['cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Day {{ $card['day'] }}</span>
                    </div>
                    <h3>{{ $card['label'] }}</h3>
                    <p>{{ $card['recall_prompt'] }}</p>
                    <p>{{ $card['coding_action'] }}</p>
                    <p class="muted">{{ $card['evidence_recheck'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Promotion Criteria</h2>
        <div class="list">
            @foreach ($review['promotion_criteria'] as $criterion)
                <article class="item">
                    <p>{{ $criterion }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection

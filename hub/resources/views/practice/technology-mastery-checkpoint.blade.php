@extends('learning.layout', ['title' => 'Technology Mastery Checkpoint'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $checkpoint['technology'] }}</span>
        <h1>{{ $checkpoint['title'] }}</h1>
        <p>
            This checkpoint turns remediation evidence into a promote-or-repeat decision,
            proof checklist, next challenge, and next-session handoff.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-mastery-checkpoint', $checkpoint['technology']) }}">
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
        <button class="button primary" type="submit">Build checkpoint</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Decision</span>
            </div>
            <p>{{ $checkpoint['decision']['promote_when'] }}</p>
            <p class="muted">{{ $checkpoint['decision']['repeat_when'] }}</p>
            <a class="button" href="{{ route('practice.technology-remediation-plan', [$checkpoint['technology']] + request()->query()) }}">Open remediation plan</a>
            <a class="button primary" href="{{ route('practice.technology-spaced-review', [$checkpoint['technology']] + request()->query()) }}">Open spaced review</a>
            <a class="button" href="{{ route('api.practice.technology-mastery-checkpoint', [$checkpoint['technology']] + request()->query()) }}">Open checkpoint API</a>
        </article>
    </section>

    <section class="section">
        <h2>Proof Checklist</h2>
        <div class="list">
            @foreach ($checkpoint['proof_checklist'] as $item)
                <article class="item">
                    <p>{{ $item }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Next Challenge</h2>
        <article class="item">
            <h3>{{ $checkpoint['next_challenge']['label'] }}</h3>
            <p>{{ $checkpoint['next_challenge']['success_signal'] }}</p>
            <code>{{ $checkpoint['next_challenge']['route'] }}</code>
        </article>
    </section>

    <section class="section">
        <h2>Next Session Handoff</h2>
        <article class="item">
            <p>{{ $checkpoint['handoff']['next_session_goal'] }}</p>
            <p>{{ $checkpoint['handoff']['first_action'] }}</p>
            <p class="muted">{{ $checkpoint['handoff']['evidence_to_keep'] }}</p>
        </article>
    </section>
@endsection

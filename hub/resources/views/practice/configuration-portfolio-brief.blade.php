@extends('learning.layout', ['title' => 'Configuration Portfolio Brief'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration portfolio</span>
        <h1>{{ $brief['title'] }}</h1>
        <p>{{ $brief['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $brief['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-portfolio-brief') }}">Open brief API</a>
            <a class="button" href="{{ route('practice.configuration-evidence-reuse-plan') }}">Open reuse plan</a>
            <a class="button" href="{{ route('practice.configuration-portfolio-review') }}">Open portfolio review</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>

        <article class="panel">
            <h3>{{ $brief['headline'] }}</h3>
            <p>{{ $brief['portfolio_paragraph'] }}</p>
        </article>
    </section>

    <section class="section">
        <h2>Talking Points</h2>
        <div class="panel">
            <ul>
                @foreach ($brief['talking_points'] as $point)
                    <li>{{ $point }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Proof Table</h2>
        <div class="list">
            @foreach ($brief['proof_table'] as $proof)
                <article class="item">
                    <span class="badge">{{ $proof['audience'] }}</span>
                    <h3>{{ $proof['use'] }}</h3>
                    <p><code>{{ $proof['source_key'] }}</code></p>
                    <p>{{ $proof['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Checklist</h2>
        <div class="panel">
            <ul>
                @foreach ($brief['review_checklist'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

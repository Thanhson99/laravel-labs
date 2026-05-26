@extends('learning.layout', ['title' => 'Configuration Portfolio Review'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration review</span>
        <h1>{{ $review['title'] }}</h1>
        <p>{{ $review['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $review['result'] }}: {{ $review['score'] }} / 100</h2>
            <a class="button" href="{{ route('api.practice.configuration-portfolio-review') }}">Open review API</a>
            <a class="button" href="{{ route('practice.configuration-portfolio-brief') }}">Open portfolio brief</a>
            <a class="button" href="{{ route('practice.configuration-publication-checklist') }}">Open publication checklist</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Rubric</h2>
        <div class="grid">
            @foreach ($review['rubric'] as $item)
                <article class="item">
                    <span class="badge">{{ $item['points'] }} / {{ $item['max_points'] }}</span>
                    <h3>{{ $item['criterion'] }}</h3>
                    <p>{{ $item['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Notes</h2>
        <div class="panel">
            <ul>
                @foreach ($review['review_notes'] as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Action Items</h2>
        <div class="panel">
            <ul>
                @foreach ($review['action_items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

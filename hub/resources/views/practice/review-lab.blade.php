@extends('learning.layout', ['title' => 'Practice Review Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Review lab</span>
        <h1>Review lab for checking code after TDD practice.</h1>
        <p>
            This review lab uses the TDD lab to turn one source record into a checklist
            for route, validation, controller, service, test, and verification evidence.
        </p>
    </section>

    @if ($review)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $review['technology'] }}</span>
                    <span class="muted">{{ $review['source']['path'] }}</span>
                </div>
                <h2>{{ $review['title'] }}</h2>
                <p>{{ $review['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.tdd-lab', request()->query()) }}">Open TDD lab</a>
                <a class="button" href="{{ route('practice.verification-plan', request()->query()) }}">Open verification plan</a>
                <a class="button" href="{{ route('api.practice.review-lab', request()->query()) }}">Open review API</a>
            </article>
        </section>

        <section class="section">
            <h2>Review Checklist</h2>
            <div class="list">
                @foreach ($review['review_items'] as $item)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $item['label'] }}</span>
                            <code>{{ $item['file'] }}</code>
                        </div>
                        <p>{{ $item['question'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Verification Commands</h2>
                    @foreach ($review['commands'] as $command)
                        <pre><code>{{ $command['command'] }}</code></pre>
                    @endforeach
                </article>
                <article class="panel">
                    <h2>Review Progress Payload</h2>
                    <pre><code>{{ json_encode($review['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No review lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

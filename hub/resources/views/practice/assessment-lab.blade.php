@extends('learning.layout', ['title' => 'Practice Assessment Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Assessment lab</span>
        <h1>Assessment lab scores Laravel practice work with a clear rubric.</h1>
        <p>
            This lab turns pull request output into a 100-point rubric for traceability,
            Laravel layers, behavior tests, verification evidence, and review readiness.
        </p>
    </section>

    @if ($assessment)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $assessment['technology'] }}</span>
                    <span class="muted">{{ $assessment['source']['path'] }}</span>
                </div>
                <h2>{{ $assessment['title'] }}</h2>
                <p>{{ $assessment['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.pull-request-lab', request()->query()) }}">Open PR lab</a>
                <a class="button" href="{{ route('practice.remediation-lab', request()->query()) }}">Open remediation lab</a>
                <a class="button" href="{{ route('api.practice.assessment-lab', request()->query()) }}">Open assessment API</a>
            </article>
        </section>

        <section class="section">
            <div class="topbar">
                <h2>Rubric: {{ $assessment['score_total'] }} points</h2>
            </div>
            <div class="list">
                @foreach ($assessment['rubric'] as $criterion)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $criterion['points'] }} pts</span>
                            <span class="badge">{{ $criterion['label'] }}</span>
                        </div>
                        <p>{{ $criterion['evidence'] }}</p>
                        <p class="muted">{{ $criterion['self_check'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Evidence</h2>
                    <pre><code>{{ json_encode($assessment['evidence'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
                <article class="panel">
                    <h2>Assessment Progress Payload</h2>
                    <pre><code>{{ json_encode($assessment['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No assessment lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

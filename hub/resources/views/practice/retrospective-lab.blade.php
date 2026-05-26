@extends('learning.layout', ['title' => 'Practice Retrospective Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Retrospective lab</span>
        <h1>Retrospective lab turns assessment scores into next learning actions.</h1>
        <p>
            This lab turns assessment output into wins, weak spots, next actions,
            next lab links, and a progress payload for continued improvement.
        </p>
    </section>

    @if ($retrospective)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $retrospective['technology'] }}</span>
                    <span class="muted">{{ $retrospective['source']['path'] }}</span>
                </div>
                <h2>{{ $retrospective['title'] }}</h2>
                <p>{{ $retrospective['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.assessment-lab', request()->query()) }}">Open assessment lab</a>
                <a class="button" href="{{ route('practice.remediation-lab', request()->query()) }}">Open remediation lab</a>
                <a class="button" href="{{ route('api.practice.retrospective-lab', request()->query()) }}">Open retrospective API</a>
            </article>
        </section>

        <section class="section">
            <h2>Wins</h2>
            <div class="list">
                @foreach ($retrospective['wins'] as $win)
                    <article class="item">
                        <p>{{ $win }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <h2>Weak Spots And Next Actions</h2>
            <div class="list">
                @foreach ($retrospective['weak_spots'] as $spot)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $spot['label'] }}</span>
                        </div>
                        <p>{{ $spot['prompt'] }}</p>
                        <p>{{ $spot['next_action'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Next Labs</h2>
                    @foreach ($retrospective['next_labs'] as $lab)
                        <p><a class="button" href="{{ route($lab['route'], array_filter($lab['query'])) }}">{{ $lab['label'] }}</a></p>
                    @endforeach
                </article>
                <article class="panel">
                    <h2>Retrospective Progress Payload</h2>
                    <pre><code>{{ json_encode($retrospective['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No retrospective lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

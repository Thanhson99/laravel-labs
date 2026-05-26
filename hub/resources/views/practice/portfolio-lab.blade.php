@extends('learning.layout', ['title' => 'Practice Portfolio Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Portfolio lab</span>
        <h1>Portfolio lab turns practice work into a reusable learning artifact.</h1>
        <p>
            This lab turns retrospective output into a headline, source reference,
            practiced skills, evidence, a writeup template, and the next improvement.
        </p>
    </section>

    @if ($portfolio)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $portfolio['technology'] }}</span>
                    <span class="muted">{{ $portfolio['source']['path'] }}</span>
                </div>
                <h2>{{ $portfolio['title'] }}</h2>
                <p>{{ $portfolio['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.retrospective-lab', request()->query()) }}">Open retrospective lab</a>
                <a class="button" href="{{ route('practice.assessment-lab', request()->query()) }}">Open assessment lab</a>
                <a class="button" href="{{ route('api.practice.portfolio-lab', request()->query()) }}">Open portfolio API</a>
            </article>
        </section>

        <section class="section">
            <h2>Portfolio Entry</h2>
            <article class="item">
                <h3>{{ $portfolio['portfolio_entry']['headline'] }}</h3>
                <p>{{ $portfolio['portfolio_entry']['problem'] }}</p>
                <p><code>{{ $portfolio['portfolio_entry']['source_reference'] }}</code></p>
                <h4>Skills Practiced</h4>
                <ul>
                    @foreach ($portfolio['portfolio_entry']['skills_practiced'] as $skill)
                        <li>{{ $skill }}</li>
                    @endforeach
                </ul>
                <h4>Evidence</h4>
                <ul>
                    @foreach ($portfolio['portfolio_entry']['evidence'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                <h4>Next Improvement</h4>
                <p>{{ $portfolio['portfolio_entry']['next_improvement'] }}</p>
            </article>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Writeup Template</h2>
                    <ul>
                        @foreach ($portfolio['writeup_template'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
                <article class="panel">
                    <h2>Portfolio Progress Payload</h2>
                    <pre><code>{{ json_encode($portfolio['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No portfolio lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

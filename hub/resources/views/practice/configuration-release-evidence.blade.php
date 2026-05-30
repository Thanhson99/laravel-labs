@extends('learning.layout', ['title' => 'Configuration Release Evidence'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration evidence</span>
        <h1>{{ $releaseEvidence['title'] }}</h1>
        <p>{{ $releaseEvidence['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $releaseEvidence['release_summary']['headline'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-release-evidence') }}">Open evidence API</a>
            <a class="button primary" href="{{ route('practice.configuration-interview-brief') }}">Open interview brief</a>
            <a class="button" href="{{ route('practice.configuration-deployment-plan') }}">Open deployment plan</a>
        </div>

        <article class="panel">
            <p>Quality status: <strong>{{ $releaseEvidence['quality_gate']['status'] }}</strong></p>
            <p>Risk level: <strong>{{ $releaseEvidence['release_summary']['risk_level'] }}</strong></p>
            <h3>Review focus</h3>
            <ul>
                @foreach ($releaseEvidence['release_summary']['review_focus'] as $focus)
                    <li>{{ $focus }}</li>
                @endforeach
            </ul>

            <h3>Security review focus</h3>
            <ul>
                @foreach ($releaseEvidence['release_summary']['security_review_focus'] as $focus)
                    <li>{{ $focus }}</li>
                @endforeach
            </ul>
        </article>
    </section>

    <section class="section">
        <h2>Proof Points</h2>
        <div class="list">
            @foreach ($releaseEvidence['proof_points'] as $point)
                <article class="item">
                    <h3>{{ $point['label'] }}</h3>
                    <p class="muted">{{ $point['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>API Evidence</h2>
        <div class="list">
            @foreach ($releaseEvidence['api_evidence'] as $evidence)
                <article class="item">
                    <h3><code>{{ $evidence['endpoint'] }}</code></h3>
                    <p>{{ $evidence['expected'] }}</p>
                    <p class="muted">{{ $evidence['capture'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Rollback Evidence</h2>
        <div class="list">
            @foreach ($releaseEvidence['rollback_summary'] as $rollback)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $rollback['area'] }}</span>
                    </div>
                    <p>{{ $rollback['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Portfolio Notes</h2>
        <div class="list">
            @foreach ($releaseEvidence['portfolio_notes'] as $note)
                <article class="item">
                    <p>{{ $note }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($releaseEvidence['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

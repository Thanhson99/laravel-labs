@extends('learning.layout', ['title' => 'Configuration Evidence Archive'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration archive</span>
        <h1>{{ $archive['title'] }}</h1>
        <p>{{ $archive['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $archive['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-evidence-archive') }}">Open archive API</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
            <a class="button" href="{{ route('practice.configuration-spaced-review') }}">Open spaced review</a>
            <a class="button" href="{{ route('practice.configuration-archive-retrieval') }}">Open retrieval drill</a>
        </div>

        <article class="panel">
            <h3>Retrieval Keys</h3>
            <ul>
                @foreach ($archive['retrieval_keys'] as $key)
                    <li><code>{{ $key }}</code></li>
                @endforeach
            </ul>
        </article>

        <article class="panel">
            <h3>Incident Archive</h3>
            <p><code>{{ $archive['incident_archive']['incident_id'] }}</code> / <code>{{ $archive['incident_archive']['postmortem_id'] }}</code></p>
            <p>{{ $archive['incident_archive']['root_cause'] }}</p>
            <a class="button" href="{{ $archive['incident_archive']['recovery_route'] }}">Open recovery route</a>
        </article>
    </section>

    <section class="section">
        <h2>Proof Bundle</h2>
        <div class="list">
            @foreach ($archive['proof_bundle'] as $proof)
                <article class="item">
                    <h3>{{ $proof['label'] }}</h3>
                    <p>{{ $proof['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Reuse Targets</h2>
        <div class="list">
            @foreach ($archive['reuse_targets'] as $target)
                <article class="item">
                    <p>{{ $target }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Retrieval Prompts</h2>
        <div class="list">
            @foreach ($archive['retrieval_prompts'] as $prompt)
                <article class="item">
                    <p>{{ $prompt }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Incident Review Inputs</h2>
        <div class="list">
            @foreach ($archive['incident_archive']['review_inputs'] as $input)
                <article class="item">
                    <p>{{ $input }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($archive['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Configuration Learning Pipeline'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration pipeline</span>
        <h1>{{ $pipeline['title'] }}</h1>
        <p>{{ $pipeline['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $pipeline['stage_count'] }} stages: {{ $pipeline['quality_gate']['status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-learning-pipeline') }}">Open pipeline API</a>
            <a class="button primary" href="{{ route('practice.configuration-dashboard') }}">Open dashboard</a>
            <a class="button" href="{{ route('practice.configuration-evidence-archive') }}">Open archive</a>
        </div>

        <div class="list">
            @foreach ($pipeline['stages'] as $stage)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $stage['step'] }}</span>
                    </div>
                    <h3>{{ $stage['label'] }}</h3>
                    <p>{{ $stage['purpose'] }}</p>
                    <a class="button primary" href="{{ $stage['route'] }}">Open stage</a>
                    <a class="button" href="{{ $stage['api_route'] }}">Open API</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Archive</h2>
        <article class="panel">
            <p><code>{{ $pipeline['archive']['archive_id'] }}</code></p>
            <h3>Retrieval keys</h3>
            <ul>
                @foreach ($pipeline['archive']['retrieval_keys'] as $key)
                    <li><code>{{ $key }}</code></li>
                @endforeach
            </ul>
            <h3>Reuse targets</h3>
            <ul>
                @foreach ($pipeline['archive']['reuse_targets'] as $target)
                    <li>{{ $target }}</li>
                @endforeach
            </ul>
        </article>
    </section>
@endsection

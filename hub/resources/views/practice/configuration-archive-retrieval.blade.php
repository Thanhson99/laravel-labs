@extends('learning.layout', ['title' => 'Configuration Archive Retrieval'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration retrieval</span>
        <h1>{{ $retrieval['title'] }}</h1>
        <p>{{ $retrieval['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $retrieval['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-archive-retrieval') }}">Open retrieval API</a>
            <a class="button" href="{{ route('practice.configuration-evidence-archive') }}">Open evidence archive</a>
            <a class="button" href="{{ route('practice.configuration-evidence-reuse-plan') }}">Open reuse plan</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Retrieval Cases</h2>
        <div class="list">
            @foreach ($retrieval['retrieval_cases'] as $case)
                <article class="item">
                    <span class="badge">{{ $case['use_case'] }}</span>
                    <h3>{{ $case['prompt'] }}</h3>
                    <p><code>{{ $case['key'] }}</code></p>
                    <p>{{ $case['proof'] }}</p>
                    <p class="muted">{{ $case['reuse_target'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Quality Checks</h2>
        <div class="panel">
            <ul>
                @foreach ($retrieval['quality_checks'] as $check)
                    <li>{{ $check }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($retrieval['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

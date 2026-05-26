@extends('learning.layout', ['title' => 'Configuration Evidence Reuse Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration reuse</span>
        <h1>{{ $reusePlan['title'] }}</h1>
        <p>{{ $reusePlan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $reusePlan['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-evidence-reuse-plan') }}">Open reuse API</a>
            <a class="button" href="{{ route('practice.configuration-archive-retrieval') }}">Open retrieval drill</a>
            <a class="button" href="{{ route('practice.configuration-portfolio-brief') }}">Open portfolio brief</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Reuse Tasks</h2>
        <div class="list">
            @foreach ($reusePlan['reuse_tasks'] as $task)
                <article class="item">
                    <span class="badge">{{ $task['audience'] }}</span>
                    <h3>{{ $task['output'] }}</h3>
                    <p><code>{{ $task['source_key'] }}</code></p>
                    <p>{{ $task['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Handoff Notes</h2>
        <div class="panel">
            <ul>
                @foreach ($reusePlan['handoff_notes'] as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Quality Checks</h2>
        <div class="panel">
            <ul>
                @foreach ($reusePlan['quality_checks'] as $check)
                    <li>{{ $check }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

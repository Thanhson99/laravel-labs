@extends('learning.layout', ['title' => 'Configuration Session Archive'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration session archive</span>
        <h1>{{ $archive['title'] }}</h1>
        <p>{{ $archive['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $archive['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-session-archive') }}">Open archive API</a>
            <a class="button" href="{{ route('practice.configuration-session-debrief') }}">Open debrief</a>
            <a class="button" href="{{ route('practice.configuration-archive-refresh-plan') }}">Open refresh plan</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Evidence Tags</h2>
        <div class="panel">
            <ul>
                @foreach ($archive['evidence_tags'] as $tag)
                    <li><code>{{ $tag }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Archive Entries</h2>
        <div class="list">
            @foreach ($archive['archive_entries'] as $entry)
                <article class="item">
                    <span class="badge">{{ $entry['status'] }}</span>
                    <h3>{{ $entry['label'] }}</h3>
                    <p>{{ $entry['note'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Retrieval Prompts</h2>
        <div class="panel">
            <ul>
                @foreach ($archive['retrieval_prompts'] as $prompt)
                    <li>{{ $prompt }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Reuse Paths</h2>
        <div class="panel">
            <ul>
                @foreach ($archive['reuse_paths'] as $path)
                    <li>{{ $path }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

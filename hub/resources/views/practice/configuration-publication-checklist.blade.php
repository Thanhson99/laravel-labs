@extends('learning.layout', ['title' => 'Configuration Publication Checklist'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration publication</span>
        <h1>{{ $checklist['title'] }}</h1>
        <p>{{ $checklist['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $checklist['publication_status'] }}: {{ $checklist['score'] }} / 100</h2>
            <a class="button" href="{{ route('api.practice.configuration-publication-checklist') }}">Open checklist API</a>
            <a class="button" href="{{ route('practice.configuration-portfolio-review') }}">Open portfolio review</a>
            <a class="button" href="{{ route('practice.configuration-handoff-packet') }}">Open handoff packet</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Channels</h2>
        <div class="list">
            @foreach ($checklist['channels'] as $channel)
                <article class="item">
                    <span class="badge">{{ $channel['name'] }}</span>
                    <h3>{{ $channel['output'] }}</h3>
                    <p>{{ $channel['proof_required'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Pre-Publish Checks</h2>
        <div class="panel">
            <ul>
                @foreach ($checklist['pre_publish_checks'] as $check)
                    <li>{{ $check }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Do Not Publish If</h2>
        <div class="panel">
            <ul>
                @foreach ($checklist['do_not_publish_if'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

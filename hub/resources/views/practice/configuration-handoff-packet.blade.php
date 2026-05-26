@extends('learning.layout', ['title' => 'Configuration Handoff Packet'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration handoff</span>
        <h1>{{ $packet['title'] }}</h1>
        <p>{{ $packet['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $packet['handoff_status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-handoff-packet') }}">Open handoff API</a>
            <a class="button" href="{{ route('practice.configuration-publication-checklist') }}">Open publication checklist</a>
            <a class="button" href="{{ route('practice.configuration-next-session-plan') }}">Open next session</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Handoff Summary</h2>
        <div class="panel">
            <ul>
                @foreach ($packet['handoff_summary'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Packet Links</h2>
        <div class="list">
            @foreach ($packet['packet_links'] as $link)
                <article class="item">
                    <h3>{{ $link['label'] }}</h3>
                    <a class="button" href="{{ $link['route'] }}">Open</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Required Evidence</h2>
        <div class="list">
            @foreach ($packet['required_evidence'] as $evidence)
                <article class="item">
                    <span class="badge">{{ $evidence['channel'] }}</span>
                    <p>{{ $evidence['proof'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Next Actions</h2>
        <div class="panel">
            <ul>
                @foreach ($packet['next_actions'] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Configuration Session Debrief'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration debrief</span>
        <h1>{{ $debrief['title'] }}</h1>
        <p>{{ $debrief['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $debrief['session_result'] }}: {{ $debrief['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-session-debrief') }}">Open debrief API</a>
            <a class="button" href="{{ route('practice.configuration-next-session-plan') }}">Open next session</a>
            <a class="button" href="{{ route('practice.configuration-session-archive') }}">Open session archive</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>
    </section>

    <section class="section">
        <h2>Completed Outputs</h2>
        <div class="panel">
            <ul>
                @foreach ($debrief['completed_outputs'] as $output)
                    <li>{{ $output }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Evidence Review</h2>
        <div class="list">
            @foreach ($debrief['evidence_review'] as $item)
                <article class="item">
                    <span class="badge">{{ $item['status'] }}</span>
                    <h3>{{ $item['deliverable'] }}</h3>
                    <p>{{ $item['review_note'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Blockers</h2>
        <div class="panel">
            <ul>
                @foreach ($debrief['blockers'] as $blocker)
                    <li>{{ $blocker }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Next Actions</h2>
        <div class="panel">
            <ul>
                @foreach ($debrief['next_actions'] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

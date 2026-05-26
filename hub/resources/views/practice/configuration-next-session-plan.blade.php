@extends('learning.layout', ['title' => 'Configuration Next Session Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration next session</span>
        <h1>{{ $plan['title'] }}</h1>
        <p>{{ $plan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $plan['session_status'] }}: {{ $plan['archive_id'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-next-session-plan') }}">Open session API</a>
            <a class="button" href="{{ route('practice.configuration-handoff-packet') }}">Open handoff packet</a>
            <a class="button" href="{{ route('practice.configuration-session-debrief') }}">Open debrief</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>

        <article class="panel">
            <h3>Session Goal</h3>
            <p>{{ $plan['session_goal'] }}</p>
        </article>
    </section>

    <section class="section">
        <h2>Preflight</h2>
        <div class="panel">
            <ul>
                @foreach ($plan['preflight'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Practice Blocks</h2>
        <div class="list">
            @foreach ($plan['practice_blocks'] as $block)
                <article class="item">
                    <span class="badge">{{ $block['minutes'] }} minutes</span>
                    <h3>{{ $block['focus'] }}</h3>
                    <p>{{ $block['task'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Deliverables</h2>
        <div class="panel">
            <ul>
                @foreach ($plan['deliverables'] as $deliverable)
                    <li>{{ $deliverable }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Stop Criteria</h2>
        <div class="panel">
            <ul>
                @foreach ($plan['stop_criteria'] as $criterion)
                    <li>{{ $criterion }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

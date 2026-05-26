@extends('learning.layout', ['title' => 'Configuration Interview Brief'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration interview</span>
        <h1>{{ $interviewBrief['title'] }}</h1>
        <p>{{ $interviewBrief['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Quality status: {{ $interviewBrief['quality_gate']['status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-interview-brief') }}">Open brief API</a>
            <a class="button primary" href="{{ route('practice.configuration-mastery-checkpoint') }}">Open mastery checkpoint</a>
            <a class="button" href="{{ route('practice.configuration-release-evidence') }}">Open release evidence</a>
        </div>

        <div class="list">
            @foreach ($interviewBrief['questions'] as $question)
                <article class="item">
                    <h3>{{ $question['question'] }}</h3>
                    <ul>
                        @foreach ($question['answer_outline'] as $outline)
                            <li>{{ $outline }}</li>
                        @endforeach
                    </ul>
                    <p class="muted">{{ $question['follow_up'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Evidence To Cite</h2>
        <div class="list">
            @foreach ($interviewBrief['evidence_to_cite'] as $evidence)
                <article class="item">
                    <h3>{{ $evidence['label'] }}</h3>
                    <p class="muted">{{ $evidence['source'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Rehearsal Checklist</h2>
        <div class="panel">
            <ul>
                @foreach ($interviewBrief['rehearsal_checklist'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($interviewBrief['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

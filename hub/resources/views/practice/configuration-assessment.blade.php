@extends('learning.layout', ['title' => 'Configuration Assessment'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration Assessment</span>
        <h1>{{ $assessment['title'] }}</h1>
        <p>{{ $assessment['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $assessment['result_label'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-assessment') }}">Open assessment API</a>
            <a class="button" href="{{ route('practice.configuration-pull-request-plan') }}">Open PR plan</a>
            <a class="button" href="{{ route('practice.configuration-decision-record') }}">Open decision record</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>

        <article class="panel">
            <p><strong>Score:</strong> {{ $assessment['score'] }} / 100</p>
            <p><strong>Result:</strong> {{ $assessment['result'] }}</p>
            <p><strong>Quality status:</strong> {{ $assessment['status']['quality'] }}</p>
        </article>
    </section>

    <section class="section">
        <h2>Rubric</h2>
        <div class="grid">
            @foreach ($assessment['rubric'] as $item)
                <article class="item">
                    <span class="badge">{{ $item['points'] }} / {{ $item['max_points'] }}</span>
                    <h3>{{ $item['criterion'] }}</h3>
                    <p>{{ $item['evidence'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Readiness Signals</h2>
        <div class="panel">
            <ul>
                @foreach ($assessment['readiness_signals'] as $signal)
                    <li>{{ $signal }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Improvement Tasks</h2>
        <div class="panel">
            <ul>
                @foreach ($assessment['improvement_tasks'] as $task)
                    <li>{{ $task }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($assessment['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

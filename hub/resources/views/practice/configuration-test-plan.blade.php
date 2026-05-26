@extends('learning.layout', ['title' => 'Configuration Test Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration tests</span>
        <h1>{{ $testPlan['title'] }}</h1>
        <p>{{ $testPlan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Target: {{ $testPlan['target_test'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-test-plan') }}">Open test plan API</a>
            <a class="button primary" href="{{ route('practice.configuration-change-checklist') }}">Open change checklist</a>
            <a class="button" href="{{ route('practice.configuration-readiness') }}">Open readiness</a>
        </div>

        <div class="list">
            @foreach ($testPlan['test_groups'] as $group)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $group['name'] }}</span>
                        <span class="muted">{{ count($group['checks']) }} checks</span>
                    </div>
                    <h3>{{ $group['name'] }}</h3>
                    <ul>
                        @foreach ($group['assertions'] as $assertion)
                            <li><code>{{ $assertion }}</code></li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Starter Snippet</h2>
        <pre><code>{{ $testPlan['snippet'] }}</code></pre>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($testPlan['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

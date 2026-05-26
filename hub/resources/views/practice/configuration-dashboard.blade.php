@extends('learning.layout', ['title' => 'Configuration Practice Dashboard'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration dashboard</span>
        <h1>{{ $dashboard['title'] }}</h1>
        <p>{{ $dashboard['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $dashboard['status']['quality'] }} across {{ $dashboard['status']['stage_count'] }} stages</h2>
            <a class="button" href="{{ route('api.practice.configuration-dashboard') }}">Open dashboard API</a>
            <a class="button primary" href="{{ route('practice.configuration-risk-register') }}">Open risk register</a>
            <a class="button" href="{{ route('practice.configuration-learning-pipeline') }}">Open pipeline</a>
        </div>

        <article class="panel">
            <h3>Next recommended stage</h3>
            <p>{{ $dashboard['next_stage']['label'] }}: {{ $dashboard['next_stage']['purpose'] }}</p>
            <a class="button primary" href="{{ $dashboard['next_stage']['route'] }}">Open next stage</a>
            <p class="muted"><code>{{ $dashboard['status']['archive_id'] }}</code></p>
        </article>
    </section>

    <section class="section">
        <h2>Stage Groups</h2>
        <div class="list">
            @foreach ($dashboard['stage_groups'] as $group)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $group['name'] }}</span>
                        <span class="muted">{{ count($group['stages']) }} stages</span>
                    </div>
                    <h3>{{ $group['name'] }}</h3>
                    <ul>
                        @foreach ($group['stages'] as $stage)
                            <li><a href="{{ $stage['route'] }}">{{ $stage['label'] }}</a></li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Archive Reuse</h2>
        <div class="list">
            @foreach ($dashboard['archive']['reuse_targets'] as $target)
                <article class="item">
                    <p>{{ $target }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($dashboard['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

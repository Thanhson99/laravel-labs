@extends('learning.layout', ['title' => 'Source Practice Pack'])

@section('content')
    <section class="hero">
        <span class="badge">Source practice pack</span>
        <h1>{{ $pack['source']['title'] }}</h1>
        <p>
            File <code>{{ $pack['source']['path'] }}</code> has {{ $pack['record_count'] }} records converted into
            technology practice paths inside Laravel.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Source</h2>
                <p><code>{{ $pack['source']['path'] }}</code></p>
                <p class="muted">{{ $pack['source']['family'] }} / {{ $pack['source']['topic'] }} / {{ $pack['source']['language'] }}</p>
                <a class="button" href="{{ route('learning.sources.show', $pack['source']['key']) }}">Open source JSON</a>
                <a class="button" href="{{ route('api.practice.source-pack', $pack['source']['key']) }}">Open pack API</a>
            </article>
            <article class="panel">
                <h2>Commands</h2>
                <pre><code>@foreach ($pack['commands'] as $command){{ $command }}
@endforeach</code></pre>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Workflow</h2>
        <div class="list">
            @foreach ($pack['workflow'] as $index => $step)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $index + 1 }}</span>
                    </div>
                    <p>{{ $step }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>{{ $pack['technology_count'] }} Technology Paths</h2>
        <div class="list">
            @foreach ($pack['technologies'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['record_count'] }} records</span>
                    </div>
                    <h3>{{ $item['practice']['title'] }}</h3>
                    <p>{{ $item['sample']['task'] }}</p>
                    <a class="button primary" href="{{ route('practice.content-drill', $item['drill_query']) }}">Open sample drill</a>
                    @if ($item['practice']['slug'])
                        <a class="button" href="{{ route('practice.show', $item['practice']['slug']) }}">Open exercise</a>
                    @endif
                    <a class="button" href="{{ route('practice.question-drills', ['source_key' => $pack['source']['key'], 'technology' => $item['technology']]) }}">Question drills</a>
                </article>
            @endforeach
        </div>
    </section>
@endsection

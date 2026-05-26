@extends('learning.layout', ['title' => $exercise['title']])

@section('content')
    <section class="hero">
        <span class="badge">{{ $track['name'] ?? $exercise['track'] }}</span>
        <h1>{{ $exercise['title'] }}</h1>
        <p>{{ $exercise['objective'] }}</p>
    </section>

    <section class="section">
        <article class="item">
            <h2>Why This Matters</h2>
            <p>{{ $exercise['why'] }}</p>
            @if (isset($exercise['workbench']))
                <a class="button primary" href="{{ route($exercise['workbench']['route']) }}">{{ $exercise['workbench']['label'] }}</a>
            @endif
            @if (isset($exercise['page']))
                <a class="button primary" href="{{ route($exercise['page']['route']) }}">{{ $exercise['page']['label'] }}</a>
            @endif
        </article>
    </section>

    <section class="section">
        <h2>Tasks</h2>
        <div class="list">
            @foreach ($exercise['steps'] as $index => $step)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Task {{ $index + 1 }}</span>
                    </div>
                    <p>{{ $step }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Files To Touch</h2>
                <ul>
                    @foreach ($exercise['files'] as $file)
                        <li><code>{{ $file }}</code></li>
                    @endforeach
                </ul>
            </article>
            <article class="panel">
                <h2>Commands</h2>
                <pre><code>@foreach ($exercise['commands'] as $command){{ $command }}
@endforeach</code></pre>
            </article>
            <article class="panel">
                <h2>Done When</h2>
                <ul>
                    @foreach ($exercise['acceptance'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
        </div>
    </section>

    @if (isset($exercise['api']))
        <section class="section">
            <h2>Practice API</h2>
            <article class="panel">
                <div class="meta">
                    <span class="badge">{{ $exercise['api']['method'] }}</span>
                    <code>{{ $exercise['api']['path'] }}</code>
                </div>
                <pre><code>{{ json_encode($exercise['api']['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </section>
    @endif

    <section class="section">
        <h2>Starter Code</h2>
        <pre><code>{{ $exercise['starter_code'] }}</code></pre>
    </section>
@endsection

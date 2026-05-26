@extends('learning.layout', ['title' => 'Content Practice Drill'])

@section('content')
    <section class="hero">
        <span class="badge">Content drill</span>
        <h1>Turn one source record into a concrete Laravel coding drill.</h1>
        <p>
            This drill keeps the source record traceable, then produces target Laravel files,
            implementation steps, commands, and done criteria.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.content-drill') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker, test...">
        </label>
        <label>
            Source key
            <input type="search" name="source_key" value="{{ $filters['source_key'] }}" placeholder="laravel-api-integration-en-json">
        </label>
        <label>
            Record id
            <input type="search" name="record_id" value="{{ $filters['record_id'] }}" placeholder="laravel-api-integration-en-json-item-1">
        </label>
        <label>
            Technology
            <input type="search" name="technology" value="{{ $filters['technology'] }}" placeholder="api-validation">
        </label>
        <button class="button primary" type="submit">Build drill</button>
    </form>

    @if ($drill)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $drill['technology'] }}</span>
                    <span class="muted">{{ $drill['source']['path'] }}</span>
                </div>
                <h2>{{ $drill['title'] }}</h2>
                <p>{{ $drill['goal'] }}</p>
                <a class="button" href="{{ route('learning.sources.show', $drill['source']['key']) }}">Open source JSON</a>
                @if ($drill['practice']['slug'])
                    <a class="button primary" href="{{ route('practice.show', $drill['practice']['slug']) }}">{{ $drill['practice']['title'] }}</a>
                @endif
                <a class="button" href="{{ route('practice.implementation-blueprint', request()->query()) }}">Build implementation blueprint</a>
                <a class="button primary" href="{{ route('practice.starter-kit', request()->query()) }}">Open starter kit</a>
                <a class="button" href="{{ route('api.practice.content-drill', request()->query()) }}">Open drill API</a>
            </article>
        </section>

        @if ($drill['related_workbench'])
            <section class="section">
                <article class="item">
                    <div class="meta">
                        <span class="badge">Runnable workbench</span>
                        <code>{{ $drill['related_workbench']['path'] }}</code>
                    </div>
                    <h2>{{ $drill['related_workbench']['label'] }}</h2>
                    <p>{{ $drill['related_workbench']['concept'] }}</p>
                    @if ($drill['related_workbench']['route_name'])
                        <a class="button primary" href="{{ route($drill['related_workbench']['route_name']) }}">Open related workbench</a>
                    @endif
                </article>
            </section>
        @endif

        <section class="section">
            <h2>Implementation Steps</h2>
            <div class="list">
                @foreach ($drill['implementation_steps'] as $index => $step)
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
            <div class="grid">
                <article class="panel">
                    <h2>Files</h2>
                    <ul>
                        @foreach ($drill['files'] as $file)
                            <li><code>{{ $file }}</code></li>
                        @endforeach
                    </ul>
                </article>
                <article class="panel">
                    <h2>Commands</h2>
                    <pre><code>@foreach ($drill['commands'] as $command){{ $command }}
@endforeach</code></pre>
                </article>
                <article class="panel">
                    <h2>Done When</h2>
                    <ul>
                        @foreach ($drill['acceptance'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </section>

        <section class="section">
            <h2>Starter Code</h2>
            <div class="list">
                @foreach ($drill['starter_snippets'] as $snippet)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ $snippet['label'] }}</span>
                            <code>{{ $snippet['file'] }}</code>
                        </div>
                        <pre><code>{{ $snippet['code'] }}</code></pre>
                    </article>
                @endforeach
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No drill found</h2>
                <p class="muted">Try another source key, search term, or technology.</p>
            </article>
        </section>
    @endif
@endsection

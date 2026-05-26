@extends('learning.layout', ['title' => 'Record Practice Workspace'])

@section('content')
    <section class="hero">
        <span class="badge">Record workspace</span>
        <h1>One question, one Laravel coding workspace.</h1>
        <p>
            This workspace includes the source record, drill, blueprint, checklist, progress payload,
            and starter snippets for one content or question record.
        </p>
    </section>

    @if ($workspace)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $workspace['technology'] }}</span>
                    <span class="muted">{{ $workspace['source']['path'] }}</span>
                </div>
                <h2>{{ $workspace['content']['title'] }}</h2>
                <p>{{ $workspace['goal'] }}</p>
                <a class="button" href="{{ route('learning.sources.show', $workspace['source']['key']) }}">Open source JSON</a>
                @if ($workspace['practice']['slug'])
                    <a class="button" href="{{ route('practice.show', $workspace['practice']['slug']) }}">Open exercise</a>
                @endif
                <a class="button primary" href="{{ route('practice.verification-plan', request()->query()) }}">Open verification plan</a>
                <a class="button" href="{{ route('api.practice.record-workspace', request()->query()) }}">Open workspace API</a>
            </article>
        </section>

        <section class="section">
            <h2>Implementation Names</h2>
            <div class="grid">
                @foreach ($workspace['blueprint']['names'] as $label => $value)
                    <article class="panel">
                        <span class="badge">{{ $label }}</span>
                        <p><code>{{ $value }}</code></p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <h2>Checklist</h2>
            <div class="list">
                @foreach ($workspace['checklist']['items'] as $index => $item)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Step {{ $index + 1 }}</span>
                            <span class="muted">{{ $item['label'] }}</span>
                        </div>
                        <p>{{ $item['detail'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <h2>Starter Snippets</h2>
            <div class="list">
                @foreach ($workspace['starter_kit']['snippets'] as $snippet)
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
                <h2>No record workspace found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

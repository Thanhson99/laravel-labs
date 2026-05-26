@extends('learning.layout', ['title' => 'Content Analytics'])

@section('content')
    <section class="hero">
        <span class="badge">Analytics</span>
        <h1>Analytics for the integrated learning content.</h1>
        <p>
            This page shows source files, records, code snippets, languages,
            content groups, and the topics with the strongest source coverage.
        </p>
    </section>

    <section class="grid" aria-label="Analytics summary">
        @foreach ($report['summary'] as $label => $value)
            <article class="panel stat">
                <span class="muted">{{ str_replace('_', ' ', $label) }}</span>
                <strong>{{ number_format($value) }}</strong>
            </article>
        @endforeach
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Content Groups</h2>
            <a class="button" href="{{ route('api.learning.analytics') }}">Open analytics API</a>
        </div>
        <div class="grid">
            <article class="panel">
                <h3>Families</h3>
                @foreach ($report['families'] as $family)
                    <p><strong>{{ $family['key'] }}</strong> <span class="muted">{{ $family['count'] }} records</span></p>
                @endforeach
            </article>
            <article class="panel">
                <h3>Languages</h3>
                @foreach ($report['languages'] as $language)
                    <p><strong>{{ strtoupper($language['key']) }}</strong> <span class="muted">{{ $language['count'] }} records</span></p>
                @endforeach
            </article>
            <article class="panel">
                <h3>Record Types</h3>
                @foreach ($report['types'] as $type)
                    <p><strong>{{ $type['key'] }}</strong> <span class="muted">{{ $type['count'] }} records</span></p>
                @endforeach
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Largest Sources</h2>
        <div class="list">
            @foreach ($report['source_density'] as $source)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $source['family'] }}</span>
                        @if ($source['language'])
                            <span class="badge">{{ $source['language'] }}</span>
                        @endif
                        <span class="muted">{{ $source['records'] }} records</span>
                        <span class="muted">{{ $source['code_snippets'] }} code snippets</span>
                    </div>
                    <h3><a href="{{ route('learning.sources.show', $source['source_key']) }}">{{ $source['title'] }}</a></h3>
                    <p class="muted">{{ $source['source_path'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Code-Heavy Sources</h2>
        <div class="list">
            @foreach ($report['code_density'] as $source)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $source['family'] }}</span>
                        @if ($source['language'])
                            <span class="badge">{{ $source['language'] }}</span>
                        @endif
                        <span class="muted">{{ $source['code_snippets'] }} snippets</span>
                    </div>
                    <h3><a href="{{ route('learning.sources.show', $source['source_key']) }}">{{ $source['source_path'] }}</a></h3>
                </article>
            @endforeach
        </div>
    </section>
@endsection

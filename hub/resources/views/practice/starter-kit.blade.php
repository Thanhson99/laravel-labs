@extends('learning.layout', ['title' => 'Implementation Starter Kit'])

@section('content')
    <section class="hero">
        <span class="badge">Starter kit</span>
        <h1>Starter snippets for implementing the practice task.</h1>
        <p>
            The starter kit uses the guided checklist and blueprint to generate skeleton code for the test,
            request, controller, and service you can implement inside Laravel.
        </p>
    </section>

    @if ($starterKit)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $starterKit['checklist']['blueprint']['drill']['technology'] }}</span>
                    <span class="muted">{{ $starterKit['checklist']['blueprint']['drill']['source']['path'] }}</span>
                </div>
                <h2>{{ $starterKit['title'] }}</h2>
                <p>{{ $starterKit['checklist']['blueprint']['drill']['goal'] }}</p>
                <a class="button" href="{{ route('practice.guided-checklist', request()->query()) }}">Open checklist</a>
                <a class="button primary" href="{{ route('practice.record-workspace', request()->query()) }}">Open record workspace</a>
                <a class="button" href="{{ route('api.practice.starter-kit', request()->query()) }}">Open starter API</a>
            </article>
        </section>

        <section class="section">
            <h2>Usage</h2>
            <div class="list">
                @foreach ($starterKit['usage'] as $index => $step)
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
            <h2>Starter Snippets</h2>
            <div class="list">
                @foreach ($starterKit['snippets'] as $snippet)
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
                <h2>No starter kit found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

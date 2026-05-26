@extends('learning.layout', ['title' => 'Guided Implementation Checklist'])

@section('content')
    <section class="hero">
        <span class="badge">Guided checklist</span>
        <h1>A TDD checklist for turning one question into Laravel code.</h1>
        <p>
            This checklist comes from the implementation blueprint and orders the work:
            read the source, write the test, create request/controller/service, add the route, and verify.
        </p>
    </section>

    @if ($checklist)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $checklist['blueprint']['drill']['technology'] }}</span>
                    <span class="muted">{{ $checklist['blueprint']['drill']['source']['path'] }}</span>
                </div>
                <h2>{{ $checklist['title'] }}</h2>
                <p>{{ $checklist['blueprint']['drill']['goal'] }}</p>
                <a class="button" href="{{ route('practice.implementation-blueprint', request()->query()) }}">Open blueprint</a>
                <a class="button primary" href="{{ route('practice.starter-kit', request()->query()) }}">Open starter kit</a>
                <a class="button" href="{{ route('api.practice.guided-checklist', request()->query()) }}">Open checklist API</a>
            </article>
        </section>

        <section class="section">
            <h2>Implementation Steps</h2>
            <div class="list">
                @foreach ($checklist['items'] as $index => $item)
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
            <article class="panel">
                <h2>Progress Payload</h2>
                <pre><code>{{ json_encode($checklist['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No checklist found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

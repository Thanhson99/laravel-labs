@extends('learning.layout', ['title' => 'Implementation Blueprint'])

@section('content')
    <section class="hero">
        <span class="badge">Implementation blueprint</span>
        <h1>Name the files, classes, routes, and tests for one concrete question.</h1>
        <p>
            The blueprint uses the source record, detected technology, and matching drill to start implementation with a clear direction.
        </p>
    </section>

    @if ($blueprint)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $blueprint['drill']['technology'] }}</span>
                    <span class="muted">{{ $blueprint['drill']['source']['path'] }}</span>
                </div>
                <h2>{{ $blueprint['title'] }}</h2>
                <p>{{ $blueprint['drill']['goal'] }}</p>
                <a class="button" href="{{ route('practice.content-drill', request()->query()) }}">Open drill</a>
                <a class="button primary" href="{{ route('practice.guided-checklist', request()->query()) }}">Open guided checklist</a>
                <a class="button" href="{{ route('api.practice.implementation-blueprint', request()->query()) }}">Open blueprint API</a>
            </article>
        </section>

        <section class="section">
            <h2>Names To Create</h2>
            <div class="grid">
                @foreach ($blueprint['names'] as $label => $value)
                    <article class="panel">
                        <span class="badge">{{ $label }}</span>
                        <p><code>{{ $value }}</code></p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Sequence</h2>
                    <ul>
                        @foreach ($blueprint['sequence'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ul>
                </article>
                <article class="panel">
                    <h2>Commands</h2>
                    <pre><code>@foreach ($blueprint['commands'] as $command){{ $command }}
@endforeach</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No blueprint found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

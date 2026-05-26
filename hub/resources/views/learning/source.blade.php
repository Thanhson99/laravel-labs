@extends('learning.layout', ['title' => $source['title']])

@section('content')
    <section class="hero">
        <span class="badge">{{ $source['family'] }}</span>
        <h1>{{ $source['title'] }}</h1>
        <p>{{ $source['path'] }}</p>
    </section>

    <section class="section">
        <h2>Extracted Records</h2>
        <div class="list">
            @foreach (collect($questions)->where('source_key', $source['key'])->values() as $question)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $question['type'] }}</span>
                        @if ($question['language'])
                            <span class="badge">{{ $question['language'] }}</span>
                        @endif
                    </div>
                    <h3>{{ $question['title'] }}</h3>
                    @if ($question['body'])
                        <p>{{ $question['body'] }}</p>
                    @endif
                    @if ($question['answer'])
                        <p><strong>Answer:</strong> {{ $question['answer'] }}</p>
                    @endif
                    @if ($question['code'])
                        <pre><code>{{ $question['code'] }}</code></pre>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Raw JSON</h2>
        <pre class="raw-json"><code>{{ json_encode($source['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
    </section>
@endsection

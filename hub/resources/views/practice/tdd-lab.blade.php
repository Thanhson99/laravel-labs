@extends('learning.layout', ['title' => 'Practice TDD Lab'])

@section('content')
    <section class="hero">
        <span class="badge">TDD lab</span>
        <h1>Red-Green-Refactor lab turns one question into Laravel code.</h1>
        <p>
            This lab uses the starter kit and verification plan to turn one source record
            into a loop of failing test, minimal implementation, and verification.
        </p>
    </section>

    @if ($lab)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $lab['technology'] }}</span>
                    <span class="muted">{{ $lab['source']['path'] }}</span>
                </div>
                <h2>{{ $lab['title'] }}</h2>
                <p>{{ $lab['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.record-workspace', request()->query()) }}">Open workspace</a>
                <a class="button" href="{{ route('practice.starter-kit', request()->query()) }}">Open starter kit</a>
                <a class="button" href="{{ route('practice.verification-plan', request()->query()) }}">Open verification plan</a>
                <a class="button" href="{{ route('api.practice.tdd-lab', request()->query()) }}">Open TDD API</a>
            </article>
        </section>

        <section class="section">
            <h2>Route And Files</h2>
            <div class="grid">
                <article class="panel">
                    <h3>{{ $lab['route']['method'] }} {{ $lab['route']['path'] }}</h3>
                    <p class="muted">Implement the route after the failing test exists.</p>
                </article>
                <article class="panel">
                    <h3>Files</h3>
                    <ul>
                        @foreach ($lab['files'] as $file)
                            <li><code>{{ $file }}</code></li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </section>

        <section class="section">
            <h2>Red Green Refactor</h2>
            <div class="list">
                @foreach ($lab['cycle'] as $step)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">{{ strtoupper($step['stage']) }}</span>
                        </div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['goal'] }}</p>

                        @if (isset($step['snippet']) && $step['snippet'])
                            <p><code>{{ $step['snippet']['file'] }}</code></p>
                            <pre><code>{{ $step['snippet']['code'] }}</code></pre>
                            <pre><code>{{ $step['command'] }}</code></pre>
                        @endif

                        @if (isset($step['snippets']))
                            @foreach ($step['snippets'] as $snippet)
                                <p><code>{{ $snippet['file'] }}</code></p>
                                <pre><code>{{ $snippet['code'] }}</code></pre>
                            @endforeach
                            <pre><code>{{ json_encode($step['smoke_request'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        @endif

                        @if (isset($step['commands']))
                            @foreach ($step['commands'] as $command)
                                <pre><code>{{ $command['command'] }}</code></pre>
                            @endforeach
                            <pre><code>{{ json_encode($step['quality_gate_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <article class="panel">
                <h2>TDD Progress Payload</h2>
                <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </article>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No TDD lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

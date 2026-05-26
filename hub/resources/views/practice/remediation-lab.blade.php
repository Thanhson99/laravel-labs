@extends('learning.layout', ['title' => 'Practice Remediation Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Remediation lab</span>
        <h1>Remediation lab turns review findings into concrete code fixes.</h1>
        <p>
            This lab turns review checklist items into Laravel-layer fixes:
            route, request, controller, service, test, and verification evidence.
        </p>
    </section>

    @if ($remediation)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $remediation['technology'] }}</span>
                    <span class="muted">{{ $remediation['source']['path'] }}</span>
                </div>
                <h2>{{ $remediation['title'] }}</h2>
                <p>{{ $remediation['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.review-lab', request()->query()) }}">Open review lab</a>
                <a class="button" href="{{ route('practice.tdd-lab', request()->query()) }}">Open TDD lab</a>
                <a class="button" href="{{ route('api.practice.remediation-lab', request()->query()) }}">Open remediation API</a>
            </article>
        </section>

        <section class="section">
            <h2>Fix Tasks</h2>
            <div class="list">
                @foreach ($remediation['tasks'] as $task)
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Fix {{ $task['position'] }}</span>
                            <span class="badge">{{ $task['label'] }}</span>
                            <code>{{ $task['file'] }}</code>
                        </div>
                        <p>{{ $task['problem_to_check'] }}</p>
                        <p>{{ $task['fix_action'] }}</p>
                        <pre><code>{{ $task['verification'] }}</code></pre>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Quality Gate Payload</h2>
                    <pre><code>{{ json_encode($remediation['quality_gate_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
                <article class="panel">
                    <h2>Remediation Progress Payload</h2>
                    <pre><code>{{ json_encode($remediation['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No remediation lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

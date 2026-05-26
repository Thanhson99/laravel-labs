@extends('learning.layout', ['title' => 'Practice Pull Request Lab'])

@section('content')
    <section class="hero">
        <span class="badge">PR lab</span>
        <h1>PR lab packages practice work like a real Laravel change.</h1>
        <p>
            This lab turns remediation work into a branch name, commit message,
            pull request summary, changed files, verification evidence, and review checklist.
        </p>
    </section>

    @if ($pullRequest)
        <section class="section">
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $pullRequest['technology'] }}</span>
                    <span class="muted">{{ $pullRequest['source']['path'] }}</span>
                </div>
                <h2>{{ $pullRequest['title'] }}</h2>
                <p>{{ $pullRequest['record']['title'] }}</p>
                <a class="button" href="{{ route('practice.remediation-lab', request()->query()) }}">Open remediation lab</a>
                <a class="button" href="{{ route('practice.review-lab', request()->query()) }}">Open review lab</a>
                <a class="button" href="{{ route('api.practice.pull-request-lab', request()->query()) }}">Open PR API</a>
            </article>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Branch</h2>
                    <pre><code>{{ $pullRequest['branch'] }}</code></pre>
                </article>
                <article class="panel">
                    <h2>Commit Message</h2>
                    <pre><code>{{ $pullRequest['commit_message'] }}</code></pre>
                </article>
            </div>
        </section>

        <section class="section">
            <h2>Pull Request Draft</h2>
            <article class="item">
                <h3>{{ $pullRequest['pull_request']['title'] }}</h3>
                <h4>Summary</h4>
                <ul>
                    @foreach ($pullRequest['pull_request']['summary'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                <h4>Changed Files</h4>
                <ul>
                    @foreach ($pullRequest['pull_request']['changed_files'] as $file)
                        <li><code>{{ $file }}</code></li>
                    @endforeach
                </ul>
                <h4>Verification</h4>
                @foreach ($pullRequest['pull_request']['verification'] as $command)
                    <pre><code>{{ $command }}</code></pre>
                @endforeach
                <h4>Review Checklist</h4>
                <ul>
                    @foreach ($pullRequest['pull_request']['review_checklist'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
        </section>

        <section class="section">
            <div class="grid">
                <article class="panel">
                    <h2>Quality Gate Payload</h2>
                    <pre><code>{{ json_encode($pullRequest['quality_gate_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
                <article class="panel">
                    <h2>PR Progress Payload</h2>
                    <pre><code>{{ json_encode($pullRequest['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </article>
            </div>
        </section>
    @else
        <section class="section">
            <article class="item">
                <h2>No PR lab found</h2>
                <p class="muted">Try a valid source key, record id, or technology.</p>
            </article>
        </section>
    @endif
@endsection

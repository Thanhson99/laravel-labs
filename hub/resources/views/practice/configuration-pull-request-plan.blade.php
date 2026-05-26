@extends('learning.layout', ['title' => 'Configuration Pull Request Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration PR</span>
        <h1>{{ $pullRequestPlan['title'] }}</h1>
        <p>{{ $pullRequestPlan['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $pullRequestPlan['branch'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-pull-request-plan') }}">Open PR API</a>
            <a class="button" href="{{ route('practice.configuration-assessment') }}">Open assessment</a>
            <a class="button" href="{{ route('practice.configuration-remediation-plan') }}">Open remediation plan</a>
        </div>

        <article class="panel">
            <p><code>{{ $pullRequestPlan['commit_message'] }}</code></p>
            <h3>PR summary</h3>
            <ul>
                @foreach ($pullRequestPlan['pr_summary'] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </article>
    </section>

    <section class="section">
        <h2>Changed Files</h2>
        <div class="list">
            @foreach ($pullRequestPlan['changed_files'] as $file)
                <article class="item">
                    <p><code>{{ $file }}</code></p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Checklist</h2>
        <div class="panel">
            <ul>
                @foreach ($pullRequestPlan['review_checklist'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Verification</h2>
        <div class="panel">
            <ul>
                @foreach ($pullRequestPlan['verification'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

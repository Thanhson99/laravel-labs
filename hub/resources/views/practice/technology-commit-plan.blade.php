@extends('learning.layout', ['title' => 'Technology Commit Plan'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $plan['technology'] }}</span>
        <h1>{{ $plan['title'] }}</h1>
        <p>
            This plan turns a technology implementation lab into branch, commit, evidence, changed files,
            verification commands, and review checks.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-commit-plan', $plan['technology']) }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, policy, cache...">
        </label>
        <label>
            Family
            <input type="search" name="family" value="{{ $filters['family'] }}" placeholder="laravel">
        </label>
        <label>
            Language
            <input type="search" name="language" value="{{ $filters['language'] }}" placeholder="en">
        </label>
        <label>
            Limit
            <input type="number" name="limit" value="{{ $filters['limit'] }}" min="1" max="100">
        </label>
        <button class="button primary" type="submit">Build commit plan</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Branch</span>
            </div>
            <pre><code>{{ $plan['branch'] }}</code></pre>
            <div class="meta">
                <span class="badge">Commit</span>
            </div>
            <pre><code>{{ $plan['commit_message'] }}</code></pre>
            @if ($plan['related_workbench'] && $plan['related_workbench']['route_name'])
                <a class="button primary" href="{{ route($plan['related_workbench']['route_name']) }}">{{ $plan['related_workbench']['label'] }}</a>
            @endif
            <a class="button" href="{{ route('practice.technology-implementation-lab', [$plan['technology']] + request()->query()) }}">Open implementation lab</a>
            <a class="button primary" href="{{ route('practice.technology-portfolio-artifact', [$plan['technology']] + request()->query()) }}">Open portfolio artifact</a>
            <a class="button" href="{{ route('api.practice.technology-commit-plan', [$plan['technology']] + request()->query()) }}">Open commit API</a>
        </article>
    </section>

    <section class="section">
        <h2>Next Actions</h2>
        <div class="list">
            @foreach ($plan['next_actions'] as $action)
                <article class="item">
                    <h3>{{ $action['label'] }}</h3>
                    <p>{{ $action['purpose'] }}</p>
                    <a class="button primary" href="{{ $action['path'] }}">Open next step</a>
                    @if ($action['api_path'])
                        <a class="button" href="{{ $action['api_path'] }}">Open API</a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Changed Files</h2>
        <div class="list">
            @foreach ($plan['changed_files'] as $file)
                <article class="item">
                    <code>{{ $file }}</code>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification</h2>
        <div class="list">
            @foreach ($plan['verification'] as $command)
                <article class="item">
                    <pre><code>{{ $command }}</code></pre>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Evidence Checklist</h2>
        <div class="list">
            @foreach ($plan['evidence_checklist'] as $item)
                <article class="item">
                    <p>{{ $item }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Checklist</h2>
        <div class="list">
            @foreach ($plan['review_checklist'] as $item)
                <article class="item">
                    <p>{{ $item }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection

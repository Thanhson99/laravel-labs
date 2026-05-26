@extends('learning.layout', ['title' => 'Practice Evidence Reuse Plan Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Evidence reuse</span>
        <h1>Evidence reuse planning turns retrieval cards into portfolio and interview tasks.</h1>
        <p>
            This lab turns archive retrieval cards into reuse plans,
            portfolio tasks, interview tasks, review tasks, and quality checks.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.evidence-reuse-plan-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build reuse plans</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['reuse_summary']['plan_count'] }} plans</span>
                <span class="badge">{{ $lab['reuse_summary']['portfolio_plan_count'] }} portfolio</span>
                <span class="badge">{{ $lab['reuse_summary']['refresh_plan_count'] }} refresh</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.archive-retrieval-lab', request()->query()) }}">Open retrieval lab</a>
            <a class="button" href="{{ route('api.practice.evidence-reuse-plan-lab', request()->query()) }}">Open reuse API</a>
        </article>
    </section>

    <section class="section">
        <h2>Reuse Rules</h2>
        <div class="list">
            @foreach ($lab['reuse_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Reuse Plans</h2>
        <div class="list">
            @foreach ($lab['reuse_plans'] as $plan)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Plan {{ $plan['plan'] }}</span>
                        <span class="badge">{{ $plan['reuse_mode'] }}</span>
                    </div>
                    <h3>{{ $plan['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $plan['route_name'] }}</code></p>
                    <p><strong>Portfolio:</strong> {{ $plan['portfolio_task'] }}</p>
                    <p><strong>Interview:</strong> {{ $plan['interview_task'] }}</p>
                    <p><strong>Review:</strong> {{ $plan['review_task'] }}</p>
                    <h4>Quality Check</h4>
                    <ul>
                        @foreach ($plan['quality_check'] as $check)
                            <li>{{ $check }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Evidence Reuse Plan Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

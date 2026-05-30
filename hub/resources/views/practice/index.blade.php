@extends('learning.layout', ['title' => 'Practice Workspace'])

@section('content')
    <section class="hero">
        <span class="badge">Practice Workspace</span>
        <h1>This is the Laravel workspace for real coding practice.</h1>
        <p>
            Exercises here are defined in Laravel code, not rendered JSON content.
            Each exercise has a goal, tasks, target files, commands to run, and done criteria.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.index') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="controller, api, docker, test...">
        </label>
        <label>
            Track
            <select name="track">
                <option value="">All</option>
                @foreach ($tracks as $track)
                    <option value="{{ $track['slug'] }}" @selected($filters['track'] === $track['slug'])>{{ $track['name'] }}</option>
                @endforeach
            </select>
        </label>
        <button class="button primary" type="submit">Filter</button>
    </form>

    <section class="section">
        <article class="panel">
            <h2>Today Session</h2>
            <p class="muted">Build a small practice plan from native Laravel exercises, then run the listed checks.</p>
            <a class="button primary" href="{{ route('practice.sessions.today') }}">Open today session</a>
            <a class="button" href="{{ route('practice.syllabus') }}">Practice syllabus</a>
            <a class="button" href="{{ route('practice.mastery-path-lab') }}">Mastery path</a>
            <a class="button" href="{{ route('practice.rotation-lab') }}">Rotation lab</a>
            <a class="button" href="{{ route('practice.weekly-report-lab') }}">Weekly report</a>
            <a class="button" href="{{ route('practice.demo-script-lab') }}">Demo script</a>
            <a class="button" href="{{ route('practice.live-coding-lab') }}">Live coding</a>
            <a class="button" href="{{ route('practice.bug-fix-lab') }}">Bug fix</a>
            <a class="button" href="{{ route('practice.refactor-lab') }}">Refactor</a>
            <a class="button" href="{{ route('practice.release-readiness-lab') }}">Release readiness</a>
            <a class="button" href="{{ route('practice.interview-defense-lab') }}">Interview defense</a>
            <a class="button" href="{{ route('practice.knowledge-gap-lab') }}">Knowledge gap</a>
            <a class="button" href="{{ route('practice.spaced-repetition-lab') }}">Spaced repetition</a>
            <a class="button" href="{{ route('practice.mastery-evidence-lab') }}">Mastery evidence</a>
            <a class="button" href="{{ route('practice.competency-map-lab') }}">Competency map</a>
            <a class="button" href="{{ route('practice.next-challenge-lab') }}">Next challenge</a>
            <a class="button" href="{{ route('practice.challenge-execution-lab') }}">Challenge execution</a>
            <a class="button" href="{{ route('practice.challenge-evidence-review-lab') }}">Challenge evidence</a>
            <a class="button" href="{{ route('practice.challenge-promotion-lab') }}">Challenge promotion</a>
            <a class="button" href="{{ route('practice.next-session-handoff-lab') }}">Next handoff</a>
            <a class="button" href="{{ route('practice.session-replay-lab') }}">Session replay</a>
            <a class="button" href="{{ route('practice.session-debrief-lab') }}">Session debrief</a>
            <a class="button" href="{{ route('practice.session-archive-lab') }}">Session archive</a>
            <a class="button" href="{{ route('practice.archive-retrieval-lab') }}">Archive retrieval</a>
            <a class="button" href="{{ route('practice.evidence-reuse-plan-lab') }}">Evidence reuse</a>
            <a class="button" href="{{ route('practice.capstone-lab') }}">Capstone lab</a>
            <a class="button" href="{{ route('practice.mentor-feedback-lab') }}">Mentor feedback lab</a>
            <a class="button" href="{{ route('practice.checkpoint-exam-lab') }}">Checkpoint exam</a>
            <a class="button" href="{{ route('practice.queue') }}">Practice queue</a>
            <a class="button" href="{{ route('practice.sprint') }}">Practice sprint</a>
            <a class="button" href="{{ route('practice.tdd-lab') }}">TDD lab</a>
            <a class="button" href="{{ route('practice.review-lab') }}">Review lab</a>
            <a class="button" href="{{ route('practice.remediation-lab') }}">Remediation lab</a>
            <a class="button" href="{{ route('practice.pull-request-lab') }}">PR lab</a>
            <a class="button" href="{{ route('practice.assessment-lab') }}">Assessment lab</a>
            <a class="button" href="{{ route('practice.retrospective-lab') }}">Retrospective lab</a>
            <a class="button" href="{{ route('practice.portfolio-lab') }}">Portfolio lab</a>
            <a class="button primary" href="{{ route('practice.configuration-dashboard') }}">Configuration dashboard</a>
            <a class="button primary" href="{{ route('practice.configuration-risk-register') }}">Configuration risk register</a>
            <a class="button primary" href="{{ route('practice.configuration-remediation-plan') }}">Configuration remediation plan</a>
            <a class="button primary" href="{{ route('practice.configuration-pull-request-plan') }}">Configuration PR plan</a>
            <a class="button primary" href="{{ route('practice.configuration-assessment') }}">Configuration assessment</a>
            <a class="button primary" href="{{ route('practice.configuration-decision-record') }}">Configuration decision record</a>
            <a class="button primary" href="{{ route('practice.configuration-operations-runbook') }}">Configuration runbook</a>
            <a class="button primary" href="{{ route('practice.configuration-incident-drill') }}">Configuration incident drill</a>
            <a class="button primary" href="{{ route('practice.configuration-incident-postmortem') }}">Configuration postmortem</a>
            <a class="button primary" href="{{ route('practice.configuration-learning-pipeline') }}">Configuration pipeline</a>
            <a class="button primary" href="{{ route('practice.configuration-readiness') }}">Configuration readiness</a>
            <a class="button primary" href="{{ route('practice.configuration-test-plan') }}">Configuration test plan</a>
            <a class="button primary" href="{{ route('practice.configuration-change-checklist') }}">Configuration change checklist</a>
            <a class="button primary" href="{{ route('practice.configuration-deployment-plan') }}">Configuration deployment plan</a>
            <a class="button primary" href="{{ route('practice.configuration-release-evidence') }}">Configuration release evidence</a>
            <a class="button primary" href="{{ route('practice.configuration-interview-brief') }}">Configuration interview brief</a>
            <a class="button primary" href="{{ route('practice.configuration-mastery-checkpoint') }}">Configuration mastery checkpoint</a>
            <a class="button primary" href="{{ route('practice.configuration-spaced-review') }}">Configuration spaced review</a>
            <a class="button primary" href="{{ route('practice.configuration-evidence-archive') }}">Configuration evidence archive</a>
            <a class="button primary" href="{{ route('practice.configuration-archive-retrieval') }}">Configuration archive retrieval</a>
            <a class="button primary" href="{{ route('practice.configuration-evidence-reuse-plan') }}">Configuration evidence reuse</a>
            <a class="button primary" href="{{ route('practice.configuration-portfolio-brief') }}">Configuration portfolio brief</a>
            <a class="button primary" href="{{ route('practice.configuration-portfolio-review') }}">Configuration portfolio review</a>
            <a class="button primary" href="{{ route('practice.configuration-publication-checklist') }}">Configuration publication checklist</a>
            <a class="button primary" href="{{ route('practice.configuration-handoff-packet') }}">Configuration handoff packet</a>
            <a class="button primary" href="{{ route('practice.configuration-next-session-plan') }}">Configuration next session</a>
            <a class="button primary" href="{{ route('practice.configuration-session-debrief') }}">Configuration session debrief</a>
            <a class="button primary" href="{{ route('practice.configuration-session-archive') }}">Configuration session archive</a>
            <a class="button primary" href="{{ route('practice.configuration-archive-refresh-plan') }}">Configuration archive refresh</a>
            <a class="button primary" href="{{ route('practice.configuration-maintenance-roadmap') }}">Configuration maintenance roadmap</a>
            <a class="button" href="{{ route('practice.content-map') }}">Map content to practice</a>
            <a class="button" href="{{ route('practice.question-drills') }}">Question drill set</a>
            <a class="button" href="{{ route('practice.technology-board') }}">Technology board</a>
            <a class="button primary" href="{{ route('practice.technology-board', ['technology' => 'oauth-flow', 'language' => 'en', 'search' => 'PKCE', 'limit' => 5]) }}">PKCE board</a>
            <a class="button" href="{{ route('practice.technology-matrix') }}">Technology matrix</a>
            <a class="button primary" href="{{ route('practice.technology-taxonomy') }}">Technology taxonomy</a>
            <a class="button primary" href="{{ route('practice.technology-pipelines') }}">Technology pipelines</a>
            <a class="button primary" href="{{ route('practice.technology-quality-plan') }}">Technology quality plan</a>
            <a class="button primary" href="{{ route('practice.technology-code-examples') }}">Technology code examples</a>
            <a class="button primary" href="{{ route('practice.technology-learning-pipeline', 'api-validation') }}">Technology pipeline</a>
            <a class="button primary" href="{{ route('practice.technology-learning-pipeline', ['oauth-flow', 'language' => 'en', 'search' => 'PKCE', 'limit' => 3]) }}">PKCE pipeline</a>
            <a class="button primary" href="{{ route('practice.workbench.oauth-flow-plan') }}">PKCE workbench</a>
            <a class="button primary" href="{{ route('practice.technology-pipelines', ['technology' => 'php', 'language' => 'en', 'search' => 'stack memory', 'limit' => 3]) }}">Stack/Heap pipelines</a>
            <a class="button primary" href="{{ route('practice.technology-learning-pipeline', ['php', 'language' => 'en', 'search' => 'stack memory', 'limit' => 3]) }}">Stack/Heap pipeline</a>
            <a class="button primary" href="{{ route('practice.technology-implementation-lab', ['php', 'language' => 'en', 'search' => 'stack memory', 'limit' => 3]) }}">Stack/Heap lab</a>
            <a class="button primary" href="{{ route('practice.technology-interview-pack', ['php', 'language' => 'en', 'search' => 'stack memory', 'limit' => 3]) }}">Stack/Heap interview</a>
            <a class="button" href="{{ route('practice.source-packs.index') }}">Source packs</a>
        </article>
    </section>

    <section class="section">
        <h2>Technology Tracks</h2>
        <div class="grid">
            @foreach ($tracks as $track)
                <a class="panel" href="{{ route('practice.index', ['track' => $track['slug']]) }}">
                    <span class="badge">{{ $track['slug'] }}</span>
                    <h3>{{ $track['name'] }}</h3>
                    <p class="muted">{{ $track['summary'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Exercises</h2>
            <a class="button" href="{{ route('api.practice.index', request()->query()) }}">Open practice API</a>
        </div>
        <div class="list">
            @forelse ($exercises as $exercise)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $exercise['track'] }}</span>
                    </div>
                    <h3><a href="{{ route('practice.show', $exercise['slug']) }}">{{ $exercise['title'] }}</a></h3>
                    <p>{{ $exercise['objective'] }}</p>
                    <p class="muted">{{ $exercise['why'] }}</p>
                    <a class="button" href="{{ route('practice.show', $exercise['slug']) }}">Open exercise</a>
                </article>
            @empty
                <article class="item">
                    <h3>No exercises found</h3>
                    <p class="muted">Try another track or search keyword.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

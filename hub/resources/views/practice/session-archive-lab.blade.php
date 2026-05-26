@extends('learning.layout', ['title' => 'Practice Session Archive Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Session archive</span>
        <h1>Session archive turns debrief cards into reusable evidence records.</h1>
        <p>
            This lab turns debrief cards into archive entries,
            proof bundles, learning summaries, blocker status, retrieval tags, and next references.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.session-archive-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build archive entries</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['archive_summary']['entry_count'] }} entries</span>
                <span class="badge">{{ $lab['archive_summary']['archived_count'] }} archived</span>
                <span class="badge">{{ $lab['archive_summary']['retry_archive_count'] }} retries</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.session-debrief-lab', request()->query()) }}">Open debrief lab</a>
            <a class="button" href="{{ route('api.practice.session-archive-lab', request()->query()) }}">Open archive API</a>
        </article>
    </section>

    <section class="section">
        <h2>Archive Rules</h2>
        <div class="list">
            @foreach ($lab['archive_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Archive Entries</h2>
        <div class="list">
            @foreach ($lab['archive_entries'] as $entry)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Entry {{ $entry['entry'] }}</span>
                        <span class="badge">{{ $entry['archive_status'] }}</span>
                    </div>
                    <h3>{{ $entry['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $entry['route_name'] }}</code></p>
                    <p><strong>Command:</strong> <code>{{ $entry['command'] }}</code></p>

                    <h4>Proof Bundle</h4>
                    <ul>
                        @foreach ($entry['proof_bundle'] as $proof)
                            <li>{{ $proof }}</li>
                        @endforeach
                    </ul>

                    <h4>Learning Summary</h4>
                    <ol>
                        @foreach ($entry['learning_summary'] as $summary)
                            <li>{{ $summary }}</li>
                        @endforeach
                    </ol>

                    <h4>Retrieval Tags</h4>
                    <p>{{ implode(', ', $entry['retrieval_tags']) }}</p>
                    <p><strong>Blocker status:</strong> {{ $entry['blocker_status']['status'] }}</p>
                    <p><strong>Next reference:</strong> {{ $entry['next_reference'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Session Archive Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

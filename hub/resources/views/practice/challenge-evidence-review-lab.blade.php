@extends('learning.layout', ['title' => 'Practice Challenge Evidence Review Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Challenge evidence review</span>
        <h1>Challenge evidence review turns executed work into a results checklist.</h1>
        <p>
            This lab turns execution steps into evidence cards,
            review questions, pass signals, risk checks, and follow-up actions.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.challenge-evidence-review-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build review cards</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['source_execution_lab']['challenge_count'] }} challenges</span>
                <span class="badge">{{ $lab['review_summary']['review_card_count'] }} reviews</span>
                <span class="badge">{{ $lab['review_summary']['required_evidence_count'] }} evidence items</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.challenge-execution-lab', request()->query()) }}">Open execution lab</a>
            <a class="button" href="{{ route('api.practice.challenge-evidence-review-lab', request()->query()) }}">Open review API</a>
        </article>
    </section>

    <section class="section">
        <h2>Review Rules</h2>
        <div class="list">
            @foreach ($lab['review_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Evidence Review Cards</h2>
        <div class="list">
            @foreach ($lab['evidence_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $card['step'] }}</span>
                        <span class="badge">{{ $card['challenge_type'] }}</span>
                    </div>
                    <h3>{{ $card['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $card['route_name'] }}</code></p>
                    <p><strong>Command:</strong> <code>{{ $card['verification_command'] }}</code></p>

                    <h4>Required Evidence</h4>
                    <ul>
                        @foreach ($card['required_evidence'] as $evidence)
                            <li>{{ $evidence }}</li>
                        @endforeach
                    </ul>

                    <h4>Review Questions</h4>
                    <ol>
                        @foreach ($card['review_questions'] as $question)
                            <li>{{ $question }}</li>
                        @endforeach
                    </ol>

                    <h4>Pass Signals</h4>
                    <ul>
                        @foreach ($card['pass_signals'] as $signal)
                            <li>{{ $signal }}</li>
                        @endforeach
                    </ul>

                    <h4>Risk Checks</h4>
                    <ul>
                        @foreach ($card['risk_checks'] as $risk)
                            <li>{{ $risk }}</li>
                        @endforeach
                    </ul>

                    <p><strong>Follow-up:</strong> {{ $card['follow_up_action'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Challenge Evidence Review Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

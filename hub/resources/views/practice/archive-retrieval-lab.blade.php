@extends('learning.layout', ['title' => 'Practice Archive Retrieval Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Archive retrieval</span>
        <h1>Archive retrieval turns saved session evidence into searchable reuse cards.</h1>
        <p>
            This lab turns archive entries into retrieval cards,
            search keys, retrieval prompts, reuse targets, proof quotes, and refresh checks.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.archive-retrieval-lab') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="api, auth, docker...">
        </label>
        <label>
            Days
            <input type="number" name="days" min="1" max="14" value="{{ $filters['days'] }}">
        </label>
        <button class="button primary" type="submit">Build retrieval cards</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">{{ $lab['retrieval_summary']['card_count'] }} cards</span>
                <span class="badge">{{ $lab['retrieval_summary']['portfolio_ready_count'] }} portfolio ready</span>
                <span class="badge">{{ $lab['retrieval_summary']['refresh_required_count'] }} refresh</span>
            </div>
            <h2>{{ $lab['title'] }}</h2>
            <a class="button" href="{{ route('practice.session-archive-lab', request()->query()) }}">Open archive lab</a>
            <a class="button" href="{{ route('api.practice.archive-retrieval-lab', request()->query()) }}">Open retrieval API</a>
        </article>
    </section>

    <section class="section">
        <h2>Retrieval Rules</h2>
        <div class="list">
            @foreach ($lab['retrieval_rules'] as $rule)
                <article class="item">
                    <p>{{ $rule }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Retrieval Cards</h2>
        <div class="list">
            @foreach ($lab['retrieval_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Card {{ $card['card'] }}</span>
                        <span class="badge">{{ $card['retrieval_mode'] }}</span>
                    </div>
                    <h3>{{ $card['technology_segment'] }}</h3>
                    <p><strong>Route:</strong> <code>{{ $card['route_name'] }}</code></p>
                    <p><strong>Prompt:</strong> {{ $card['retrieval_prompt'] }}</p>

                    <h4>Search Keys</h4>
                    <p>{{ implode(', ', $card['search_keys']) }}</p>

                    <h4>Reuse Targets</h4>
                    <ul>
                        @foreach ($card['reuse_targets'] as $target)
                            <li>{{ $target }}</li>
                        @endforeach
                    </ul>

                    <h4>Proof To Quote</h4>
                    <ul>
                        @foreach ($card['proof_to_quote'] as $proof)
                            <li>{{ $proof }}</li>
                        @endforeach
                    </ul>

                    <p><strong>Refresh:</strong> {{ $card['refresh_check']['required_before_reuse'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <article class="panel">
            <h2>Archive Retrieval Progress Payload</h2>
            <pre><code>{{ json_encode($lab['progress_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </article>
    </section>
@endsection

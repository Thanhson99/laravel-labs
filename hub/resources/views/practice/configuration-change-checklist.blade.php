@extends('learning.layout', ['title' => 'Configuration Change Checklist'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration review</span>
        <h1>{{ $checklist['title'] }}</h1>
        <p>{{ $checklist['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>Quality status: {{ $checklist['quality_gate']['status'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-change-checklist') }}">Open checklist API</a>
            <a class="button primary" href="{{ route('practice.configuration-deployment-plan') }}">Open deployment plan</a>
            <a class="button" href="{{ route('practice.configuration-test-plan') }}">Open test plan</a>
        </div>

        <div class="list">
            @foreach ($checklist['change_cards'] as $card)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $card['area'] }}</span>
                        <span class="muted">{{ $card['file'] }}</span>
                    </div>
                    <h3>{{ $card['area'] }}</h3>
                    <p>{{ $card['impact'] }}</p>

                    <h4>Watch values</h4>
                    <ul>
                        @foreach ($card['watch_values'] as $value)
                            <li><code>{{ $value }}</code></li>
                        @endforeach
                    </ul>

                    <h4>Before change</h4>
                    <ul>
                        @foreach ($card['before_change'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ul>

                    <h4>After change</h4>
                    <ul>
                        @foreach ($card['after_change'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ul>

                    <p class="muted">{{ $card['rollback'] }}</p>

                    @if (! empty($card['test_group']))
                        <h4>Linked test group</h4>
                        <p class="muted">{{ $card['test_group']['name'] }}</p>
                        <ul>
                            @foreach ($card['test_group']['assertions'] as $assertion)
                                <li><code>{{ $assertion }}</code></li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Questions</h2>
        <div class="list">
            @foreach ($checklist['review_questions'] as $question)
                <article class="item">
                    <p>{{ $question }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($checklist['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

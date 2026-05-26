@extends('learning.layout', ['title' => 'Configuration Risk Register'])

@section('content')
    <section class="hero">
        <span class="badge">Configuration risk</span>
        <h1>{{ $riskRegister['title'] }}</h1>
        <p>{{ $riskRegister['summary'] }}</p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>{{ $riskRegister['risk_count'] }} risks, highest severity {{ $riskRegister['highest_severity'] }}</h2>
            <a class="button" href="{{ route('api.practice.configuration-risk-register') }}">Open risk API</a>
            <a class="button primary" href="{{ route('practice.configuration-remediation-plan') }}">Open remediation plan</a>
            <a class="button" href="{{ route('practice.configuration-dashboard') }}">Open dashboard</a>
        </div>

        <div class="list">
            @foreach ($riskRegister['risks'] as $risk)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $risk['severity'] }}</span>
                        <span class="muted">{{ $risk['area'] }}</span>
                    </div>
                    <h3>{{ $risk['key'] }}</h3>
                    <p>{{ $risk['signal'] }}</p>
                    <p>{{ $risk['mitigation'] }}</p>
                    <a class="button" href="{{ $risk['owner_route'] }}">Open owner stage</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Review Cadence</h2>
        <div class="panel">
            <ul>
                @foreach ($riskRegister['review_cadence'] as $cadence)
                    <li>{{ $cadence }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="section">
        <h2>Verification Commands</h2>
        <div class="panel">
            <ul>
                @foreach ($riskRegister['commands'] as $command)
                    <li><code>{{ $command }}</code></li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection

@extends('learning.layout', ['title' => 'Technology Interview Pack'])

@section('content')
    <section class="hero">
        <span class="badge">{{ $pack['technology'] }}</span>
        <h1>{{ $pack['title'] }}</h1>
        <p>
            This pack turns a portfolio artifact into interview questions, answer outlines,
            concrete evidence, and a short practice script.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('practice.technology-interview-pack', $pack['technology']) }}">
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
        <button class="button primary" type="submit">Build interview pack</button>
    </form>

    <section class="section">
        <article class="item">
            <div class="meta">
                <span class="badge">Artifact</span>
            </div>
            <h2>{{ $pack['artifact']['portfolio']['headline'] }}</h2>
            <a class="button" href="{{ route('practice.technology-portfolio-artifact', [$pack['technology']] + request()->query()) }}">Open portfolio artifact</a>
            <a class="button primary" href="{{ route('practice.technology-skill-assessment', [$pack['technology']] + request()->query()) }}">Open skill assessment</a>
            <a class="button" href="{{ route('api.practice.technology-interview-pack', [$pack['technology']] + request()->query()) }}">Open interview API</a>
        </article>
    </section>

    <section class="section">
        <h2>Questions</h2>
        <div class="list">
            @foreach ($pack['questions'] as $question)
                <article class="item">
                    <h3>{{ $question['question'] }}</h3>
                    <ul>
                        @foreach ($question['answer_outline'] as $point)
                            <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Evidence To Cite</h2>
        <div class="list">
            @foreach ($pack['evidence_to_cite'] as $evidence)
                <article class="item">
                    <p>{{ $evidence }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Practice Script</h2>
        <div class="list">
            @foreach ($pack['practice_script'] as $line)
                <article class="item">
                    <p>{{ $line }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection

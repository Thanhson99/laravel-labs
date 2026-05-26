@extends('learning.layout', ['title' => 'Study Plan'])

@section('content')
    <section class="hero">
        <span class="badge">Study plan</span>
        <h1>Study plans generated from the integrated source content.</h1>
        <p>
            Each plan groups the richest sources and topics, estimates study time,
            and defines outcomes so every module has a clear target.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('learning.study-plan') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="auth, queue, OOP, security...">
        </label>
        <label>
            Language
            <select name="language">
                @foreach ($languages as $language)
                    <option value="{{ $language }}" @selected($filters['language'] === $language)>{{ strtoupper($language) }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Family
            <select name="family">
                <option value="">All</option>
                @foreach ($families as $family)
                    <option value="{{ $family }}" @selected($filters['family'] === $family)>{{ $family }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Modules
            <input type="number" name="limit" min="1" max="24" value="{{ $filters['limit'] ?? 8 }}">
        </label>
        <button class="button primary" type="submit">Generate</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $studyPlan['meta']['count'] }} modules, about {{ $studyPlan['meta']['estimated_minutes'] }} minutes</h2>
            <a class="button" href="{{ route('api.learning.study-plan', request()->query()) }}">Open study plan API</a>
        </div>

        <div class="list">
            @forelse ($studyPlan['modules'] as $module)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Step {{ $module['step'] }}</span>
                        <span class="badge">{{ $module['family'] }}</span>
                        @if ($module['language'])
                            <span class="badge">{{ $module['language'] }}</span>
                        @endif
                        <span class="muted">{{ $module['item_count'] }} records</span>
                        <span class="muted">{{ $module['estimated_minutes'] }} minutes</span>
                    </div>
                    <h3>{{ $module['title'] }}</h3>
                    <p class="muted">{{ $module['source_path'] }}</p>
                    @if ($module['outcomes'])
                        <ul>
                            @foreach ($module['outcomes'] as $outcome)
                                <li>{{ $outcome }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="nav">
                        <a class="button" href="{{ route('learning.sources.show', $module['source_key']) }}">Open source</a>
                        <a class="button" href="{{ route('learning.quiz', ['family' => $module['family'], 'language' => $module['language'], 'search' => $module['topic'], 'limit' => 5]) }}">Practice this topic</a>
                    </div>
                </article>
            @empty
                <article class="item">
                    <h3>No study modules found</h3>
                    <p class="muted">Try fewer filters or a broader keyword.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

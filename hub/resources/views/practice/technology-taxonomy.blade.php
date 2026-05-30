@extends('learning.layout', ['title' => 'Technology Taxonomy'])

@section('content')
    <section class="hero">
        <span class="badge">Technology taxonomy</span>
        <h1>Supported technology keys for content-backed practice.</h1>
        <p>
            Use this catalog to see which technology keys exist, which exercise they map to,
            which runnable workbench they open, and which starter files a drill will suggest.
        </p>
    </section>

    <section class="section">
        <div class="topbar">
            <h2>
                @if ($taxonomy['meta']['filtered'])
                    {{ $taxonomy['meta']['technology_count'] }} of {{ $taxonomy['meta']['total_technology_count'] }} technologies
                @else
                    {{ $taxonomy['meta']['technology_count'] }} technologies
                @endif
                , {{ $taxonomy['meta']['workbench_count'] }} workbenches
            </h2>
            <span class="muted">{{ $taxonomy['meta']['alias_count'] }} searchable aliases</span>
            <a class="button" href="{{ route('api.practice.technology-taxonomy', array_filter(['search' => $filters['search'] ?? null, 'strength' => $filters['strength'] ?? null])) }}">Open taxonomy API</a>
            <a class="button" href="{{ route('practice.technology-matrix') }}">Open matrix</a>
            <a class="button primary" href="{{ route('practice.technology-pipelines') }}">Open pipelines</a>
        </div>

        <form class="panel" method="GET" action="{{ route('practice.technology-taxonomy') }}">
            <label>
                Search technology, alias, or concept
                <input
                    name="search"
                    type="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Try ppo, agi, jwt, kubernetes, lsm"
                >
            </label>
            <label style="margin-top: 12px;">
                Match strength
                <select name="strength">
                    <option value="">any strength</option>
                    @foreach (['strong', 'direct', 'partial'] as $strength)
                        <option value="{{ $strength }}" @selected(($filters['strength'] ?? null) === $strength)>
                            {{ $strength }} ({{ $taxonomy['meta']['strength_counts'][$strength] }})
                        </option>
                    @endforeach
                </select>
            </label>
            <button class="button primary" type="submit" style="margin-top: 14px;">Search taxonomy</button>
            @if (! empty($filters['search']) || ! empty($filters['strength']))
                <a class="button" href="{{ route('practice.technology-taxonomy') }}" style="margin-top: 14px;">Clear search</a>
            @endif
            <div class="meta" style="margin-top: 14px;">
                @foreach ($taxonomy['meta']['suggested_searches'] as $suggestedSearch)
                    <a class="button" href="{{ route('practice.technology-taxonomy', ['search' => $suggestedSearch]) }}">{{ $suggestedSearch }}</a>
                @endforeach
            </div>
        </form>

        @if (! $taxonomy['meta']['has_results'] && $taxonomy['meta']['empty_state'])
            <article class="panel">
                <h2>{{ $taxonomy['meta']['empty_state']['title'] }}</h2>
                <p>{{ $taxonomy['meta']['empty_state']['body'] }}</p>
                <div class="meta">
                    @foreach ($taxonomy['meta']['empty_state']['suggestions'] as $suggestedSearch)
                        <a class="button" href="{{ route('practice.technology-taxonomy', ['search' => $suggestedSearch]) }}">{{ $suggestedSearch }}</a>
                    @endforeach
                </div>
            </article>
        @endif

        <div class="list">
            @foreach ($taxonomy['items'] as $item)
                <article class="item">
                    <div class="meta">
                        <span class="badge">{{ $item['technology'] }}</span>
                        <span class="muted">{{ $item['starter_count'] }} starter files</span>
                    </div>
                    <h3>{{ $item['practice']['title'] }}</h3>
                    <p>{{ $item['learning_goal'] }}</p>
                    @if (! empty($item['matched_by']))
                        <p class="muted">
                            Matched by:
                            {{ collect($item['matched_by'])->map(fn (array $match): string => $match['field'].': '.$match['value'])->implode(', ') }}
                            | score: {{ $item['match_score'] }}
                            | strength: {{ $item['match_strength'] }}
                        </p>
                    @endif
                    <p class="muted">Aliases: {{ implode(', ', array_slice($item['aliases'], 0, 8)) }}</p>
                    <p class="muted">Concepts: {{ implode(', ', array_slice($item['concepts'], 0, 6)) }}</p>
                    <p class="muted">{{ implode(', ', array_slice($item['starter_files'], 0, 4)) }}</p>
                    @if ($item['related_workbench'] && $item['related_workbench']['route_name'])
                        <a class="button primary" href="{{ route($item['related_workbench']['route_name']) }}">{{ $item['related_workbench']['label'] }}</a>
                    @endif
                    <a class="button" href="{{ $item['links']['board'] }}">Open board</a>
                    <a class="button" href="{{ $item['links']['pipeline'] }}">Open pipeline</a>
                    <a class="button" href="{{ $item['links']['code_examples'] }}">Open code examples</a>
                </article>
            @endforeach
        </div>
    </section>
@endsection

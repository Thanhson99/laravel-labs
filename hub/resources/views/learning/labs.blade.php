@extends('learning.layout', ['title' => 'Practice Labs'])

@section('content')
    <section class="hero">
        <span class="badge">Hands-on labs</span>
        <h1>Learn each topic by implementing it in Laravel.</h1>
        <p>
            Each lab turns source material into concrete implementation work: objectives, tasks,
            files to create or edit, commands to run, and done criteria for self-checking.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('learning.labs') }}">
        <label>
            Search
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="auth, queue, Docker, API...">
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
            Track
            <select name="track">
                <option value="">All</option>
                @foreach ($tracks as $track)
                    <option value="{{ $track }}" @selected($filters['track'] === $track)>{{ $track }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Labs
            <input type="number" name="limit" min="1" max="20" value="{{ $filters['limit'] ?? 8 }}">
        </label>
        <button class="button primary" type="submit">Generate</button>
    </form>

    <section class="section">
        <div class="topbar">
            <h2>{{ $labs['meta']['count'] }} labs from {{ $labs['meta']['available_records'] }} records</h2>
            <a class="button" href="{{ route('api.learning.labs', request()->query()) }}">Open labs API</a>
        </div>

        <div class="list">
            @forelse ($labs['labs'] as $lab)
                <article class="item">
                    <div class="meta">
                        <span class="badge">Lab {{ $lab['number'] }}</span>
                        <span class="badge">{{ $lab['track'] }}</span>
                        <span class="badge">{{ $lab['source']['family'] }}</span>
                        @if ($lab['source']['language'])
                            <span class="badge">{{ $lab['source']['language'] }}</span>
                        @endif
                        <span class="muted">{{ $lab['record_count'] }} records</span>
                    </div>
                    <h3>{{ $lab['title'] }}</h3>
                    <p>{{ $lab['goal'] }}</p>
                    <p class="muted">{{ $lab['source']['path'] }}</p>

                    <h3>Tasks</h3>
                    <ul>
                        @foreach ($lab['tasks'] as $task)
                            <li>{{ $task }}</li>
                        @endforeach
                    </ul>

                    <h3>Files</h3>
                    <ul>
                        @foreach ($lab['files'] as $file)
                            <li><code>{{ str_replace('{topic}', $lab['source']['topic'], $file) }}</code></li>
                        @endforeach
                    </ul>

                    <h3>Commands</h3>
                    <pre><code>@foreach ($lab['commands'] as $command){{ str_replace('{topic}', $lab['source']['topic'], $command) }}
@endforeach</code></pre>

                    <h3>Done When</h3>
                    <ul>
                        @foreach ($lab['done'] as $done)
                            <li>{{ $done }}</li>
                        @endforeach
                    </ul>

                    <div class="nav">
                        <a class="button" href="{{ route('learning.sources.show', $lab['source']['key']) }}">Open source</a>
                        <a class="button" href="{{ route('learning.quiz', ['family' => $lab['source']['family'], 'language' => $lab['source']['language'], 'search' => $lab['source']['topic'], 'limit' => 5]) }}">Practice questions</a>
                    </div>
                </article>
            @empty
                <article class="item">
                    <h3>No labs found</h3>
                    <p class="muted">Try fewer filters or another technology track.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection

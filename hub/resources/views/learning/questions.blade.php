@extends('learning.layout', ['title' => 'Question Bank'])

@section('content')
    <section class="hero">
        <span class="badge">Question bank</span>
        <h1>Question bank integrated from the source JSON files.</h1>
        <p>
            Filter by language, content group, or search text. All records come from the repository source
            files under <strong>data/**</strong>, so the Laravel app stays connected to the learning portal.
        </p>
    </section>

    <form class="filters" method="GET" action="{{ route('learning.questions') }}">
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
        <button class="button primary" type="submit">Filter</button>
    </form>

    <section class="list">
        @forelse ($questions as $question)
            <article class="item">
                <div class="meta">
                    <span class="badge">{{ $question['family'] }}</span>
                    @if ($question['language'])
                        <span class="badge">{{ $question['language'] }}</span>
                    @endif
                    <span class="badge">{{ $question['type'] }}</span>
                    <a class="muted" href="{{ route('learning.sources.show', $question['source_key']) }}">{{ $question['source_path'] }}</a>
                </div>
                <h3>{{ $question['title'] }}</h3>
                @if ($question['group'])
                    <p class="muted">{{ $question['group'] }}</p>
                @endif
                @if ($question['body'])
                    <p>{{ $question['body'] }}</p>
                @endif
                @if ($question['answer'])
                    <p><strong>Answer:</strong> {{ $question['answer'] }}</p>
                @endif
                @if ($question['bullets'])
                    <ul>
                        @foreach ($question['bullets'] as $bullet)
                            <li>{{ $bullet }}</li>
                        @endforeach
                    </ul>
                @endif
                @if ($question['tip'])
                    <p><strong>Tip:</strong> {{ $question['tip'] }}</p>
                @endif
                @if ($question['note'])
                    <p><strong>Note:</strong> {{ $question['note'] }}</p>
                @endif
                @if ($question['code'])
                    <pre><code>{{ $question['code'] }}</code></pre>
                @endif
            </article>
        @empty
            <article class="item">
                <h3>No matching questions</h3>
                <p class="muted">Try removing one filter or searching another keyword.</p>
            </article>
        @endforelse
    </section>
@endsection

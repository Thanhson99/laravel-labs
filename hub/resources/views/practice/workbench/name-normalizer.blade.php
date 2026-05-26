@extends('learning.layout', ['title' => 'Name Normalizer Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>PHP CLI idea, Laravel UI/API execution.</h1>
        <p>
            This exercise turns PHP foundation practice into runnable Laravel code.
            Edit `App\Practice\Php\NameNormalizer`, reload the page, and run the test to verify the result.
        </p>
    </section>

    <section class="section">
        <form method="GET" action="{{ route('practice.workbench.name-normalizer') }}">
            <label>
                Raw names, one per line
                <textarea name="names" rows="8" style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px; font:inherit;">{{ $rawInput }}</textarea>
            </label>
            <button class="button primary" type="submit" style="margin-top: 12px;">Normalize</button>
        </form>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Normalized Output</h2>
                @if ($normalizedNames)
                    <ul>
                        @foreach ($normalizedNames as $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="muted">No valid names yet.</p>
                @endif
            </article>
            <article class="panel">
                <h2>Files To Edit</h2>
                <ul>
                    <li><code>app/Practice/Php/NameNormalizer.php</code></li>
                    <li><code>tests/Unit/Practice/NameNormalizerTest.php</code></li>
                    <li><code>tests/Feature/PracticeWorkbenchTest.php</code></li>
                </ul>
            </article>
            <article class="panel">
                <h2>Commands</h2>
                <pre><code>php artisan test --filter NameNormalizer
php artisan test --filter PracticeWorkbench
vendor\bin\pint --test</code></pre>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>API Practice</h2>
        <pre><code>POST /api/practice/name-normalizer
{
  "names": ["  jane   doe", "", "LARAVEL labs"]
}</code></pre>
    </section>
@endsection

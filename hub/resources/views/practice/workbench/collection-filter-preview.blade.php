@extends('learning.layout', ['title' => 'Collection Filter Preview Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Practice list-page filtering before touching the database.</h1>
        <p>
            This workbench uses a Laravel Collection to model search, status filtering, pagination,
            and response metadata. Read it before moving the same pattern into an Eloquent query.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Filter Input</h2>
                <form id="collectionFilterForm">
                    <label>
                        Search
                        <input name="search" value="api" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Status
                        <select name="status">
                            <option value="all">All</option>
                            <option value="open" selected>Open</option>
                            <option value="in-review">In review</option>
                            <option value="done">Done</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Page
                        <input name="page" type="number" min="1" max="50" value="1">
                    </label>

                    <label style="margin-top: 12px;">
                        Per page
                        <input name="per_page" type="number" min="1" max="10" value="3">
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Preview filtered list</button>
                </form>
            </article>

            <article class="panel">
                <h2>Filtered Response</h2>
                <p class="muted" id="collectionFilterStatus">Submit filters to preview the response.</p>
                <pre class="raw-json"><code id="collectionFilterOutput">POST /api/practice/collection-filter-preview</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PreviewCollectionFilterRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/CollectionFilterPreviewController.php</code></li>
                    <li><code>app/Services/Practice/CollectionFilterPreviewService.php</code></li>
                    <li><code>tests/Feature/CollectionFilterPreviewWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Search</span>
                </div>
                <p>Search for <code>docker</code>, <code>security</code>, or <code>http</code> and inspect which fields are included in the search haystack.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Pagination</span>
                </div>
                <p>Set <code>per_page</code> to <code>1</code> and compare <code>total</code>, <code>page</code>, and <code>last_page</code>.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Eloquent next</span>
                </div>
                <p>After understanding the service, replace the Collection pipeline with an Eloquent query using <code>when()</code> and <code>paginate()</code>.</p>
            </article>
        </div>
    </section>

    <script>
        const collectionFilterForm = document.querySelector('#collectionFilterForm');
        const collectionFilterStatus = document.querySelector('#collectionFilterStatus');
        const collectionFilterOutput = document.querySelector('#collectionFilterOutput');

        collectionFilterForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(collectionFilterForm);
            const payload = {
                search: String(formData.get('search') || ''),
                status: String(formData.get('status') || 'all'),
                page: Number(formData.get('page') || 1),
                per_page: Number(formData.get('per_page') || 3),
            };

            collectionFilterStatus.textContent = 'Running POST /api/practice/collection-filter-preview...';
            collectionFilterOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.collection-filter-preview.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                collectionFilterStatus.textContent = `HTTP ${response.status}`;
                collectionFilterOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                collectionFilterStatus.textContent = 'Request failed';
                collectionFilterOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection

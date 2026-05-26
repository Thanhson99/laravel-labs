@extends('learning.layout', ['title' => 'File Storage Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan upload validation, disk, path, and cleanup.</h1>
        <p>
            This workbench turns Laravel file storage into a readable plan before you accept real uploads.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Storage Input</h2>
                <form id="fileStoragePlanForm">
                    <label>
                        Purpose
                        <input name="purpose" value="Profile avatar" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        Disk
                        <select name="disk">
                            <option value="local">local</option>
                            <option value="public" selected>public</option>
                            <option value="s3">s3</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Visibility
                        <select name="visibility">
                            <option value="private">private</option>
                            <option value="public" selected>public</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Max MB
                        <input name="max_mb" type="number" min="1" max="100" value="5">
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan storage</button>
                </form>
            </article>

            <article class="panel">
                <h2>Storage Plan</h2>
                <p class="muted" id="fileStoragePlanStatus">Submit input to build the storage plan.</p>
                <pre class="raw-json"><code id="fileStoragePlanOutput">POST /api/practice/file-storage-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanFileStorageRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/FileStoragePlanController.php</code></li>
                    <li><code>app/Services/Practice/FileStoragePlanService.php</code></li>
                    <li><code>config/filesystems.php</code></li>
                    <li><code>tests/Feature/FileStoragePlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const fileStoragePlanForm = document.querySelector('#fileStoragePlanForm');
        const fileStoragePlanStatus = document.querySelector('#fileStoragePlanStatus');
        const fileStoragePlanOutput = document.querySelector('#fileStoragePlanOutput');

        fileStoragePlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(fileStoragePlanForm);
            const payload = {
                purpose: String(formData.get('purpose') || ''),
                disk: String(formData.get('disk') || 'public'),
                visibility: String(formData.get('visibility') || 'public'),
                max_mb: Number(formData.get('max_mb') || 1),
            };

            fileStoragePlanStatus.textContent = 'Running POST /api/practice/file-storage-plan...';
            fileStoragePlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.file-storage-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                fileStoragePlanStatus.textContent = `HTTP ${response.status}`;
                fileStoragePlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                fileStoragePlanStatus.textContent = 'Request failed';
                fileStoragePlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection

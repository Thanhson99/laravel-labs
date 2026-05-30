@extends('learning.layout', ['title' => 'JavaScript Arrow This Lab'])

@section('content')
    <section class="hero">
        <div>
            <span class="badge">Frontend Interview Workbench</span>
            <h1>JavaScript Arrow This Lab</h1>
            <p>Compare arrow-function lexical <code>this</code> with normal function call-site <code>this</code>, object-method traps, and callback use cases.</p>
        </div>
    </section>

    <section class="section">
        <div class="grid">
            <form id="arrowThisForm" class="panel">
                <h2>Analyze snippet</h2>
                <label for="arrowThisSnippet">JavaScript snippet</label>
                <textarea id="arrowThisSnippet" name="snippet" rows="14">const user = {
  name: 'Son',
  normal() {
    return this.name;
  },
  arrow: () => this.name,
};

console.log(user.normal());
console.log(user.arrow());
user.arrow.call({ name: 'An' });</textarea>

                <label for="arrowThisScenario">Scenario</label>
                <select id="arrowThisScenario" name="scenario">
                    <option value="mixed">Mixed</option>
                    <option value="object-method">Object method</option>
                    <option value="callback">Callback</option>
                    <option value="class-method">Class method</option>
                </select>

                <label for="arrowThisLevel">Interview level</label>
                <select id="arrowThisLevel" name="interview_level">
                    <option value="junior">Junior</option>
                    <option value="middle">Middle</option>
                    <option value="senior" selected>Senior</option>
                </select>

                <button type="submit">Analyze arrow this</button>
                <p id="arrowThisStatus" class="muted">POST /api/practice/javascript-arrow-this-lab</p>
            </form>

            <div class="panel">
                <h2>Analysis Result</h2>
                <pre class="raw-json"><code id="arrowThisOutput">Run the lab to inspect lexical this, call-site this, binding traps, and interview notes.</code></pre>
            </div>
        </div>

        <div class="grid">
            <article class="panel">
                <h2>What to Look For</h2>
                <ul>
                    <li>Arrow functions do not create their own <code>this</code>.</li>
                    <li>Normal functions can receive <code>this</code> from the call site.</li>
                    <li><code>obj.arrow()</code> does not make <code>this</code> equal to <code>obj</code>.</li>
                    <li><code>call</code>, <code>apply</code>, and <code>bind</code> cannot rebind arrow-function <code>this</code>.</li>
                </ul>
            </article>

            <article class="panel">
                <h2>Verification</h2>
                <ul>
                    <li><code>php artisan route:list --path=javascript-arrow-this-lab</code></li>
                    <li><code>php artisan test --filter JavascriptArrowThisLabWorkbenchTest</code></li>
                    <li><code>vendor\bin\pint --test</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const arrowThisForm = document.getElementById('arrowThisForm');
        const arrowThisOutput = document.getElementById('arrowThisOutput');
        const arrowThisStatus = document.getElementById('arrowThisStatus');

        function renderArrowThisJson(value) {
            return JSON.stringify(value, null, 2)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;');
        }

        arrowThisForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            arrowThisStatus.textContent = 'Running POST /api/practice/javascript-arrow-this-lab...';

            const payload = {
                snippet: document.getElementById('arrowThisSnippet').value,
                scenario: document.getElementById('arrowThisScenario').value,
                interview_level: document.getElementById('arrowThisLevel').value,
            };

            const response = await fetch('{{ route('api.practice.javascript-arrow-this-lab.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            arrowThisOutput.innerHTML = renderArrowThisJson(data);
            arrowThisStatus.textContent = response.ok ? 'Analysis ready.' : 'Validation failed.';
        });
    </script>
@endsection

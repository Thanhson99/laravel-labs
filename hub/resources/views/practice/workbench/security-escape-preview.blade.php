@extends('learning.layout', ['title' => 'Security Escape Preview Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Practice Blade escaping with unsafe-looking content.</h1>
        <p>
            This workbench calls a real API that validates user input, escapes it, detects risky patterns,
            and explains which Blade output rule to use.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Unsafe Input</h2>
                <form id="escapePreviewForm">
                    <label>
                        Title
                        <input name="title" value="Profile bio preview" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Body
                        <textarea name="body" rows="8" style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px; font:inherit;">Hello <script>alert('xss')</script> Laravel</textarea>
                    </label>

                    <label style="margin-top: 12px;">
                        Rendering context
                        <select name="rendering_context" style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px; font:inherit;">
                            <option value="html_text">HTML text</option>
                            <option value="html_attribute">HTML attribute</option>
                            <option value="javascript_data">JavaScript data</option>
                            <option value="url">URL href/src</option>
                            <option value="rich_text">Rich text</option>
                        </select>
                    </label>

                    <div style="margin-top: 12px;">
                        <p class="muted" style="margin-bottom: 8px;">Payload presets</p>
                        <div class="actions">
                            <button class="button" type="button" data-preset="script">Script tag</button>
                            <button class="button" type="button" data-preset="event">Inline event</button>
                            <button class="button" type="button" data-preset="url">JavaScript URL</button>
                            <button class="button" type="button" data-preset="dom">DOM sink</button>
                        </div>
                    </div>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Preview escaped output</button>
                </form>
            </article>

            <article class="panel">
                <h2>Escaped Preview</h2>
                <p class="muted" id="escapePreviewStatus">Submit the form to inspect escaped output.</p>
                <div class="list" id="escapePreviewSummary" style="margin-top: 14px;">
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Context rule</span>
                        </div>
                        <p id="escapePreviewContextRule">Run the preview to see the context-specific rendering rule.</p>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Risk findings</span>
                        </div>
                        <p class="muted" id="escapePreviewDecision">Release decision appears after running the preview.</p>
                        <p class="muted" id="escapePreviewSeverity">Severity summary appears after running the preview.</p>
                        <ul id="escapePreviewFindings">
                            <li>No findings yet.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Payload tests</span>
                        </div>
                        <ul id="escapePreviewPayloadTests">
                            <li>Submit the form to generate context-specific test payloads.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Generated test plan</span>
                        </div>
                        <ul id="escapePreviewTestPlan">
                            <li>Run the preview to generate feature-test scenarios.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Secure snippets</span>
                        </div>
                        <ul id="escapePreviewSecureSnippets">
                            <li>Run the preview to generate context-specific safe rendering snippets.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Review checklist</span>
                        </div>
                        <ul id="escapePreviewReviewChecklist">
                            <li>Run the preview to generate file-level review checks.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Remediation steps</span>
                        </div>
                        <ul id="escapePreviewRemediationSteps">
                            <li>Run the preview to generate prioritized fix steps.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">XSS variants</span>
                        </div>
                        <ul id="escapePreviewVariantReview">
                            <li>Run the preview to compare reflected, stored, and DOM XSS review prompts.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Interview outline</span>
                        </div>
                        <ul id="escapePreviewInterviewOutline">
                            <li>Run the preview to generate a concise interview answer outline.</li>
                        </ul>
                    </article>
                </div>
                <pre class="raw-json"><code id="escapePreviewOutput">POST /api/practice/security-escape-preview</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PreviewEscapedContentRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/SecurityEscapePreviewController.php</code></li>
                    <li><code>app/Services/Practice/SecurityEscapePreviewService.php</code></li>
                    <li><code>resources/views/practice/workbench/security-escape-preview.blade.php</code></li>
                    <li><code>tests/Feature/SecurityEscapePreviewWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Script tag</span>
                </div>
                <p>Submit content with <code>&lt;script&gt;</code> and confirm the API reports a <code>script-tag</code> risk flag.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Inline event</span>
                </div>
                <p>Try an image tag with <code>onerror=</code> and read how the service detects inline event handlers.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Blade rule</span>
                </div>
                <p>Read the view and compare escaped <code>@{{ $value }}</code> output with unsafe raw HTML output.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Context</span>
                </div>
                <p>Switch the rendering context and compare why HTML text, attributes, JavaScript data, URLs, and rich text need different checks.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">CSP</span>
                </div>
                <p>Use the CSP note as defense-in-depth only; the main fix must still be escaped output or sanitized trusted HTML.</p>
            </article>
        </div>
    </section>

    <script>
        const escapePreviewForm = document.querySelector('#escapePreviewForm');
        const escapePreviewStatus = document.querySelector('#escapePreviewStatus');
        const escapePreviewOutput = document.querySelector('#escapePreviewOutput');
        const escapePreviewContextRule = document.querySelector('#escapePreviewContextRule');
        const escapePreviewDecision = document.querySelector('#escapePreviewDecision');
        const escapePreviewSeverity = document.querySelector('#escapePreviewSeverity');
        const escapePreviewFindings = document.querySelector('#escapePreviewFindings');
        const escapePreviewPayloadTests = document.querySelector('#escapePreviewPayloadTests');
        const escapePreviewTestPlan = document.querySelector('#escapePreviewTestPlan');
        const escapePreviewSecureSnippets = document.querySelector('#escapePreviewSecureSnippets');
        const escapePreviewReviewChecklist = document.querySelector('#escapePreviewReviewChecklist');
        const escapePreviewRemediationSteps = document.querySelector('#escapePreviewRemediationSteps');
        const escapePreviewVariantReview = document.querySelector('#escapePreviewVariantReview');
        const escapePreviewInterviewOutline = document.querySelector('#escapePreviewInterviewOutline');
        const escapePreviewPresets = {
            script: {
                title: 'Stored profile payload',
                body: "Hello <script>alert('xss')</script> Laravel",
                rendering_context: 'html_text',
            },
            event: {
                title: 'Image event payload',
                body: '<img src=x onerror=alert(1)>',
                rendering_context: 'rich_text',
            },
            url: {
                title: 'Unsafe link payload',
                body: 'javascript:alert(1)',
                rendering_context: 'url',
            },
            dom: {
                title: 'DOM sink payload',
                body: 'document.querySelector("#app").innerHTML = userInput',
                rendering_context: 'javascript_data',
            },
        };

        const replaceList = (target, items, formatter) => {
            target.replaceChildren();

            if (!Array.isArray(items) || items.length === 0) {
                const empty = document.createElement('li');
                empty.textContent = 'No items returned.';
                target.append(empty);

                return;
            }

            items.forEach((item) => {
                const row = document.createElement('li');
                row.textContent = formatter(item);
                target.append(row);
            });
        };

        const renderPreviewSummary = (body) => {
            const data = body && body.data ? body.data : {};

            escapePreviewContextRule.textContent = data.context_rule || 'No context rule returned.';
            const decision = data.release_decision || {};
            escapePreviewDecision.textContent = `Decision ${decision.status || 'unknown'}: ${decision.reason || 'No release decision returned.'}`;
            const severity = data.severity_summary || {};
            escapePreviewSeverity.textContent = `High ${severity.high || 0} / Medium ${severity.medium || 0} / Low ${severity.low || 0}`;
            replaceList(
                escapePreviewFindings,
                data.risk_findings || [],
                (finding) => `${finding.flag || 'unknown'} (${finding.severity || 'n/a'}): ${finding.fix || finding.evidence || 'Review output context.'}`
            );
            replaceList(
                escapePreviewPayloadTests,
                data.payload_tests || [],
                (payload) => payload
            );
            replaceList(
                escapePreviewTestPlan,
                data.test_plan || [],
                (item) => `${item.name || 'xss_test'}: ${item.purpose || 'Prove safe rendering.'}`
            );
            replaceList(
                escapePreviewSecureSnippets,
                data.secure_code_snippets || [],
                (item) => `${item.label || 'Snippet'}: ${item.code || ''} - ${item.note || 'Review context.'}`
            );
            replaceList(
                escapePreviewReviewChecklist,
                data.review_checklist || [],
                (item) => `${item.status || 'review'}: ${item.label || 'Review output path.'} (${item.file_hint || 'No file hint.'})`
            );
            replaceList(
                escapePreviewRemediationSteps,
                data.remediation_steps || [],
                (item) => `P${item.priority || '?'} ${item.owner || 'owner'}: ${item.action || 'Fix rendering path.'}`
            );
            replaceList(
                escapePreviewVariantReview,
                data.variant_review || [],
                (item) => `${item.variant || 'xss'}: ${item.review_prompt || 'Review browser execution path.'}`
            );
            replaceList(
                escapePreviewInterviewOutline,
                data.interview_answer_outline || [],
                (item) => item
            );
        };

        document.querySelectorAll('[data-preset]').forEach((button) => {
            button.addEventListener('click', () => {
                const preset = escapePreviewPresets[button.dataset.preset];

                if (!preset) {
                    return;
                }

                escapePreviewForm.elements.title.value = preset.title;
                escapePreviewForm.elements.body.value = preset.body;
                escapePreviewForm.elements.rendering_context.value = preset.rendering_context;
                escapePreviewStatus.textContent = `Loaded preset: ${button.textContent}`;
            });
        });

        escapePreviewForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = Object.fromEntries(new FormData(escapePreviewForm).entries());

            escapePreviewStatus.textContent = 'Running POST /api/practice/security-escape-preview...';
            escapePreviewOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.security-escape-preview.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                escapePreviewStatus.textContent = `HTTP ${response.status}`;
                escapePreviewOutput.textContent = JSON.stringify(body, null, 2);
                renderPreviewSummary(body);
            } catch (error) {
                escapePreviewStatus.textContent = 'Request failed';
                escapePreviewOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection

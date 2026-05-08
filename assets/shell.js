const SHELL_SITE_ROOT = document.body.dataset.siteRoot || ".";

const PAGE_SHELLS = {
  landing: "landing-shell",
  roadmap: "detail-shell",
  practice: "detail-shell",
  glossary: "detail-shell",
  laravel: "topic-shell",
  interview: "topic-shell",
  "vibe-coding": "topic-shell",
  php: "php-overview-shell"
};

function resolveShellName(pageKey) {
  if (PAGE_SHELLS[pageKey]) {
    return PAGE_SHELLS[pageKey];
  }

  if (
    pageKey.startsWith("laravel-")
    || pageKey.startsWith("interview-")
    || pageKey.startsWith("vibe-")
  ) {
    return "topic-shell";
  }

  if (pageKey.startsWith("php-")) {
    return "php-level-shell";
  }

  return "detail-shell";
}

async function loadPageShell() {
  const pageKey = document.body.dataset.page || "landing";
  const shellName = resolveShellName(pageKey);
  const response = await fetch(`${SHELL_SITE_ROOT}/partials/${shellName}.html`);
  if (!response.ok) {
    throw new Error(`Failed to load shell "${shellName}"`);
  }

  const markup = await response.text();
  let host = document.getElementById("appShell");
  if (!host) {
    host = document.createElement("div");
    host.id = "appShell";
    document.body.prepend(host);
  }
  host.innerHTML = markup;
}

window.__shellReady = loadPageShell().catch((error) => {
  console.error(error);
  let host = document.getElementById("appShell");
  if (!host) {
    host = document.createElement("div");
    host.id = "appShell";
    document.body.prepend(host);
  }
  host.innerHTML = `
    <main class="detail-main">
      <section class="panel detail-section">
        <h1>Page shell failed to load</h1>
        <p class="section-copy">Check the local server path for the shared HTML partials.</p>
      </section>
    </main>
  `;
});

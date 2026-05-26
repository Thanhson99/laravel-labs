const STORAGE_KEY = "laravel-labs-language";
const THEME_KEY = "laravel-labs-theme";
const PHP_LAST_LEVEL_KEY = "laravel-labs-php-last-level";
const LARAVEL_LAST_TOPIC_KEY = "laravel-labs-laravel-last-topic";
const INTERVIEW_LAST_TOPIC_KEY = "laravel-labs-interview-last-topic";
const VIBE_CODING_LAST_TOPIC_KEY = "laravel-labs-vibe-coding-last-topic";
const ROADMAP_PROGRESS_KEY = "laravel-labs-roadmap-progress";
const ROADMAP_COLLAPSE_KEY = "laravel-labs-roadmap-collapsed";
const CONTENT_PROGRESS_KEY = "laravel-labs-content-progress";
const SEARCH_INDEX_CACHE = new Map();
const SITE_ROOT = document.body.dataset.siteRoot || ".";
const HUB_BASE_URL = normalizeBaseUrl(
  document.body.dataset.hubBaseUrl ||
  window.LARAVEL_LABS_HUB_BASE_URL ||
  ""
);
const DEFAULT_LANGUAGE = "en";
const SUPPORTED_LANGUAGES = ["en", "vi"];
let currentLanguage = "en";
let activePhpScrollAnimation = 0;
const PHP_LEVEL_UI = {
  en: {
    collapseCode: "Collapse code",
    expandCode: "Expand code",
    copyCode: "Copy",
    copiedCode: "✓ Copied"
  },
  vi: {
    collapseCode: "Thu gọn mã",
    expandCode: "Mở mã",
    copyCode: "Sao chép",
    copiedCode: "✓ Đã sao chép"
  }
};
const PHP_KEYWORD_UI = {
  en: {
    title: "Keyword Jump List",
    subtitle: "Use a few quick keywords or search for a topic such as `database`, `file`, `loop`, or `session`.",
    hint: "Need a shortcut later? Scroll to the end of this PHP page for the keyword search and jump list.",
    searchLabel: "Search PHP keywords",
    searchPlaceholder: "Type a keyword like database, file, foreach, csv...",
    empty: "No matching keyword yet.",
    countSuffix: "keywords",
    quickTitle: "Quick topics",
    showAll: "Show all keywords",
    hideAll: "Hide full list"
  },
  vi: {
    title: "Danh Sách Từ Khóa",
    subtitle: "Dùng vài từ khóa nhanh hoặc search theo các chủ đề như `database`, `file`, `loop`, `session`.",
    hint: "Nếu muốn tra nhanh theo chủ đề, hãy kéo xuống cuối trang PHP. Ở đó có ô search và danh sách nhảy nhanh.",
    searchLabel: "Tìm từ khóa PHP",
    searchPlaceholder: "Nhập từ khóa như database, file, foreach, csv...",
    empty: "Chưa có từ khóa phù hợp.",
    countSuffix: "từ khóa",
    quickTitle: "Chủ đề nhanh",
    showAll: "Xem toàn bộ từ khóa",
    hideAll: "Ẩn danh sách đầy đủ"
  }
};
const PHP_LEVELS = [
  { key: "starter", path: `${SITE_ROOT}/data/php/starter` },
  { key: "intermediate", path: `${SITE_ROOT}/data/php/intermediate` },
  { key: "advanced", path: `${SITE_ROOT}/data/php/advanced` }
];
const LEGACY_PAGE_KEY_MAP = {
  videos: "vibe-coding",
  "video-foundations": "vibe-prompting",
  "video-laravel-builds": "vibe-ai-crud",
  "video-debug-refactor": "vibe-ai-review",
  "video-devops-runtime": "vibe-ai-runtime"
};
const PAGE_PATHS = {
  landing: `${SITE_ROOT}/index.html`,
  roadmap: `${SITE_ROOT}/sites/roadmap/index.html`,
  glossary: `${SITE_ROOT}/sites/glossary/index.html`,
  practice: `${SITE_ROOT}/sites/practice/index.html`,
  php: `${SITE_ROOT}/sites/php/index.html`,
  "php-starter": `${SITE_ROOT}/sites/php/starter.html`,
  "php-intermediate": `${SITE_ROOT}/sites/php/intermediate.html`,
  "php-advanced": `${SITE_ROOT}/sites/php/advanced.html`,
  laravel: `${SITE_ROOT}/sites/laravel/index.html`,
  "laravel-overview": `${SITE_ROOT}/sites/laravel/overview.html`,
  "laravel-structure": `${SITE_ROOT}/sites/laravel/structure.html`,
  "laravel-composer": `${SITE_ROOT}/sites/laravel/composer.html`,
  "laravel-php-commands": `${SITE_ROOT}/sites/laravel/php-commands.html`,
  "laravel-frontend": `${SITE_ROOT}/sites/laravel/frontend.html`,
  "laravel-blade-ui": `${SITE_ROOT}/sites/laravel/blade-ui.html`,
  "laravel-data": `${SITE_ROOT}/sites/laravel/data.html`,
  "laravel-request-flow": `${SITE_ROOT}/sites/laravel/request-flow.html`,
  "laravel-container-architecture": `${SITE_ROOT}/sites/laravel/container-architecture.html`,
  "laravel-auth-security": `${SITE_ROOT}/sites/laravel/auth-security.html`,
  "laravel-api-integration": `${SITE_ROOT}/sites/laravel/api-integration.html`,
  "laravel-files-media": `${SITE_ROOT}/sites/laravel/files-media.html`,
  "laravel-async": `${SITE_ROOT}/sites/laravel/async.html`,
  "laravel-performance-search": `${SITE_ROOT}/sites/laravel/performance-search.html`,
  "laravel-realtime": `${SITE_ROOT}/sites/laravel/realtime.html`,
  "laravel-quality": `${SITE_ROOT}/sites/laravel/quality.html`,
  "laravel-devops": `${SITE_ROOT}/sites/laravel/devops.html`,
  "laravel-maintenance-upgrade": `${SITE_ROOT}/sites/laravel/maintenance-upgrade.html`,
  "laravel-repo-map": `${SITE_ROOT}/sites/laravel/repo-map.html`,
  interview: `${SITE_ROOT}/sites/interview/index.html`,
  "interview-fresher": `${SITE_ROOT}/sites/interview/fresher.html`,
  "interview-junior": `${SITE_ROOT}/sites/interview/junior.html`,
  "interview-intermediate": `${SITE_ROOT}/sites/interview/intermediate.html`,
  "interview-senior": `${SITE_ROOT}/sites/interview/senior.html`,
  "interview-master": `${SITE_ROOT}/sites/interview/master.html`,
  "interview-devops": `${SITE_ROOT}/sites/interview/devops.html`,
  "vibe-coding": `${SITE_ROOT}/sites/vibe-coding/index.html`,
  vibeCoding: `${SITE_ROOT}/sites/vibe-coding/index.html`,
  "vibe-prompting": `${SITE_ROOT}/sites/vibe-coding/prompting.html`,
  "vibe-ai-crud": `${SITE_ROOT}/sites/vibe-coding/ai-crud.html`,
  "vibe-ai-review": `${SITE_ROOT}/sites/vibe-coding/ai-review.html`,
  "vibe-ai-runtime": `${SITE_ROOT}/sites/vibe-coding/ai-runtime.html`
};
const PHP_LEVEL_PAGE_MAP = {
  php: PAGE_PATHS.php,
  starter: PAGE_PATHS["php-starter"],
  intermediate: PAGE_PATHS["php-intermediate"],
  advanced: PAGE_PATHS["php-advanced"]
};

function normalizeBaseUrl(value) {
  return String(value || "").replace(/\/+$/, "");
}

function resolveConfiguredHref(value) {
  return String(value || "#").replaceAll("{{HUB_BASE_URL}}", HUB_BASE_URL);
}

const PHP_HEADER_MENU = {
  en: {
    overview: { label: "Overview", desc: "Roadmap, study flow, and where to continue next." },
    starter: { label: "Level 1", desc: "What PHP is, syntax, files, data, and first backend habits." },
    intermediate: { label: "Level 2", desc: "Functions, loops, input, services, logging, and real code structure." },
    advanced: { label: "Level 3", desc: "Architecture, N+1, transactions, consistency, and production thinking." }
  },
  vi: {
    overview: { label: "Tổng quan", desc: "Lộ trình học, cách đi từng chặng và học tiếp từ đâu." },
    starter: { label: "Cấp 1", desc: "PHP là gì, cú pháp, file, dữ liệu và thói quen backend đầu tiên." },
    intermediate: { label: "Cấp 2", desc: "Hàm, vòng lặp, input, service, logging và cấu trúc code thực tế." },
    advanced: { label: "Cấp 3", desc: "Kiến trúc, N+1, transaction, consistency và tư duy production." }
  }
};
function isLaravelPage(pageKey) {
  return pageKey === "laravel" || pageKey.startsWith("laravel-");
}

function isInterviewPage(pageKey) {
  return pageKey === "interview" || pageKey.startsWith("interview-");
}

function isVideoPage(pageKey) {
  return pageKey === "vibe-coding"
    || pageKey === "videos"
    || pageKey.startsWith("vibe-")
    || pageKey.startsWith("video-");
}

function canonicalPageKey(pageKey) {
  return LEGACY_PAGE_KEY_MAP[pageKey] || pageKey;
}

function getLaravelTopicKey(pageKey) {
  return pageKey === "laravel" ? "" : pageKey.replace(/^laravel-/, "");
}

function getLaravelTopics(page) {
  return (page.menu || []).map((item, index) => ({
    ...item,
    index: index + 1,
    pageKey: item.anchor,
    topicKey: item.anchor.replace(/^laravel-/, ""),
    href: item.anchor === "laravel-overview"
      ? getPageHref("laravel")
      : getPageHref(item.anchor)
  }));
}

function getLaravelHubTopics(page) {
  return getLaravelTopics(page)
    .filter((item) => item.anchor !== "laravel-overview")
    .map((item, index) => ({
      ...item,
      index: index + 1
    }));
}

function getInterviewTopicKey(pageKey) {
  return pageKey === "interview" ? "" : pageKey.replace(/^interview-/, "");
}

function getInterviewTopics(page) {
  return (page.menu || []).map((item, index) => ({
    ...item,
    index: index + 1,
    pageKey: item.anchor,
    topicKey: item.anchor.replace(/^interview-/, ""),
    href: getPageHref(item.anchor)
  }));
}

function getVideoTopicKey(pageKey) {
  if (pageKey === "vibe-coding" || pageKey === "videos") {
    return "";
  }

  return pageKey
    .replace(/^vibe-/, "")
    .replace(/^video-/, "");
}

function getVideoTopics(page) {
  return (page.menu || []).map((item, index) => ({
    ...item,
    index: index + 1,
    pageKey: item.anchor,
    topicKey: item.anchor
      .replace(/^vibe-/, "")
      .replace(/^video-/, ""),
    href: getPageHref(item.anchor)
  }));
}

async function loadInterviewSection(language, pageKey) {
  const topicKey = getInterviewTopicKey(pageKey);
  if (!topicKey) {
    throw new Error(`Missing Interview topic key for "${pageKey}"`);
  }
  return loadJson(`${SITE_ROOT}/data/interview/${topicKey}.${language}.json`);
}

async function loadVideoSection(language, pageKey) {
  const topicKey = getVideoTopicKey(pageKey);
  if (!topicKey) {
    throw new Error(`Missing Video topic key for "${pageKey}"`);
  }
  return loadJson(`${SITE_ROOT}/data/vibe-coding/${topicKey}.${language}.json`);
}

function getContentPath(language) {
  return `${SITE_ROOT}/data/site-content.${language}.json`;
}

async function loadContent(language) {
  const response = await fetch(getContentPath(language));
  if (!response.ok) {
    throw new Error(`Failed to load site data for ${language}: ${response.status}`);
  }

  return response.json();
}

async function loadJson(path) {
  const response = await fetch(path);
  if (!response.ok) {
    throw new Error(`Failed to load ${path}: ${response.status}`);
  }

  return response.json();
}

async function loadPhpLevels(language) {
  return Promise.all(
    PHP_LEVELS.map(async (level) => ({
      key: level.key,
      ...(await loadJson(`${level.path}.${language}.json`))
    }))
  );
}

async function loadPhpLevel(language, levelKey) {
  const level = PHP_LEVELS.find((item) => item.key === levelKey);
  if (!level) {
    throw new Error(`Missing PHP level config for "${levelKey}"`);
  }

  return {
    key: level.key,
    ...(await loadJson(`${level.path}.${language}.json`))
  };
}

async function loadLaravelSections(page, language) {
  const sectionFiles = Array.isArray(page.sectionFiles) ? page.sectionFiles : [];
  if (!sectionFiles.length) {
    return page.sections || [];
  }

  return Promise.all(
    sectionFiles.map((key) => loadJson(`${SITE_ROOT}/data/laravel/${key}.${language}.json`))
  );
}

async function loadLaravelSection(language, pageKey) {
  const topicKey = getLaravelTopicKey(pageKey);
  if (!topicKey) {
    throw new Error(`Missing Laravel topic key for "${pageKey}"`);
  }
  return loadJson(`${SITE_ROOT}/data/laravel/${topicKey}.${language}.json`);
}

function getLanguage() {
  const saved = localStorage.getItem(STORAGE_KEY);
  return SUPPORTED_LANGUAGES.includes(saved) ? saved : DEFAULT_LANGUAGE;
}

function getTheme() {
  const saved = localStorage.getItem(THEME_KEY);
  return saved === "dark" ? "dark" : "light";
}

function readStoredJson(key) {
  try {
    return JSON.parse(localStorage.getItem(key) || "{}");
  } catch (error) {
    return {};
  }
}

function getContentProgressState() {
  return readStoredJson(CONTENT_PROGRESS_KEY);
}

function saveContentProgressState(state) {
  localStorage.setItem(CONTENT_PROGRESS_KEY, JSON.stringify(state));
}

function isContentItemDone(progressId) {
  return Boolean(getContentProgressState()[progressId]);
}

function toggleContentItemDone(progressId) {
  const state = getContentProgressState();
  if (state[progressId]) {
    delete state[progressId];
  } else {
    state[progressId] = true;
  }
  saveContentProgressState(state);
}

function clearPageProgress(pageKey) {
  const state = getContentProgressState();
  Object.keys(state).forEach((progressId) => {
    if (progressId.startsWith(`${pageKey}::`)) {
      delete state[progressId];
    }
  });
  saveContentProgressState(state);
}

function applyTheme(theme) {
  document.body.dataset.theme = theme;
}

function text(value, language) {
  if (typeof value === "string") {
    return value;
  }

  return value?.[language] ?? "";
}

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

function formatRichText(value) {
  return escapeHtml(value).replace(/`([^`]+)`/g, '<span class="inline-keyword">$1</span>');
}

function getPageHref(pageKey) {
  const key = canonicalPageKey(pageKey);
  return PAGE_PATHS[key] || `${SITE_ROOT}/sites/${key}.html`;
}

function getProgressId(pageKey, sectionKey, itemIndex, title) {
  return `${pageKey}::${sectionKey}::${itemIndex}::${slugify(title || `item-${itemIndex + 1}`)}`;
}

function getPageTitleForBreadcrumb(pageKey) {
  if (pageKey === "landing") {
    return document.getElementById("landingTitle")?.textContent?.trim() || "";
  }

  if (pageKey.startsWith("php-")) {
    return document.querySelector(".level-shell h2")?.textContent?.trim()
      || document.getElementById("pageTitle")?.textContent?.trim()
      || "";
  }

  return document.getElementById("pageTitle")?.textContent?.trim() || "";
}

function getBreadcrumbEntries(data, language, pageKey) {
  if (pageKey === "landing") {
    return [];
  }

  const entries = [
    {
      label: text(data.common.navigation.home, language),
      href: `${SITE_ROOT}/index.html`
    }
  ];

  if (pageKey === "roadmap") {
    entries.push({
      label: text(data.common.navigation.roadmap, language),
      href: getPageHref("roadmap")
    });
    return entries;
  }

  if (pageKey === "glossary") {
    entries.push({
      label: text(data.common.navigation.glossary, language),
      href: getPageHref("glossary")
    });
    return entries;
  }

  if (pageKey === "practice") {
    entries.push({
      label: text(data.common.navigation.practice, language),
      href: getPageHref("practice")
    });
    return entries;
  }

  if (pageKey === "php") {
    entries.push({
      label: text(data.common.navigation.php, language),
      href: getPageHref("php")
    });
    return entries;
  }

  if (pageKey.startsWith("php-")) {
    entries.push({
      label: text(data.common.navigation.php, language),
      href: getPageHref("php")
    });
    entries.push({
      label: getPageTitleForBreadcrumb(pageKey),
      href: getPageHref(pageKey)
    });
    return entries;
  }

  if (pageKey === "laravel") {
    entries.push({
      label: text(data.common.navigation.laravel, language),
      href: getPageHref("laravel")
    });
    return entries;
  }

  if (pageKey.startsWith("laravel-")) {
    entries.push({
      label: text(data.common.navigation.laravel, language),
      href: getPageHref("laravel")
    });
    entries.push({
      label: getPageTitleForBreadcrumb(pageKey),
      href: getPageHref(pageKey)
    });
    return entries;
  }

  if (pageKey === "interview") {
    entries.push({
      label: text(data.common.navigation.interview, language),
      href: getPageHref("interview")
    });
    return entries;
  }

  if (pageKey.startsWith("interview-")) {
    entries.push({
      label: text(data.common.navigation.interview, language),
      href: getPageHref("interview")
    });
    entries.push({
      label: getPageTitleForBreadcrumb(pageKey),
      href: getPageHref(pageKey)
    });
    return entries;
  }

  if (pageKey === "vibe-coding" || pageKey === "videos") {
    entries.push({
      label: text(data.common.navigation.vibeCoding, language),
      href: getPageHref("vibe-coding")
    });
    return entries;
  }

  if (pageKey.startsWith("vibe-") || pageKey.startsWith("video-")) {
    entries.push({
      label: text(data.common.navigation.vibeCoding, language),
      href: getPageHref("vibe-coding")
    });
    entries.push({
      label: getPageTitleForBreadcrumb(pageKey),
      href: getPageHref(pageKey)
    });
    return entries;
  }

  entries.push({
    label: getPageTitleForBreadcrumb(pageKey),
    href: getPageHref(pageKey)
  });

  return entries;
}

function renderBreadcrumbTrail(data, language, pageKey) {
  const heroContent = document.querySelector(".hero-content");
  if (!heroContent) {
    return;
  }

  const existing = heroContent.querySelector(".hero-breadcrumbs");
  if (existing) {
    existing.remove();
  }

  const entries = getBreadcrumbEntries(data, language, pageKey);
  if (!entries.length) {
    return;
  }

  const nav = document.createElement("nav");
  nav.className = "hero-breadcrumbs";
  nav.setAttribute("aria-label", language === "vi" ? "Điều hướng vị trí" : "Breadcrumb");
  nav.innerHTML = `
    <ol>
      ${entries
        .map((entry, index) => {
          const isLast = index === entries.length - 1;
          return `
            <li>
              ${isLast
                ? `<span aria-current="page">${escapeHtml(entry.label)}</span>`
                : `<a href="${escapeHtml(resolveConfiguredHref(entry.href))}">${escapeHtml(entry.label)}</a>`}
            </li>
          `;
        })
        .join("")}
    </ol>
  `;

  const eyebrow = heroContent.querySelector(".eyebrow");
  if (eyebrow) {
    heroContent.insertBefore(nav, eyebrow);
    return;
  }

  heroContent.prepend(nav);
}

function easeOutCubic(progress) {
  return 1 - (1 - progress) ** 3;
}

function animateWindowScroll(targetTop) {
  if (activePhpScrollAnimation) {
    window.cancelAnimationFrame(activePhpScrollAnimation);
    activePhpScrollAnimation = 0;
  }

  const initialTop = window.scrollY;
  const initialDistance = targetTop - initialTop;

  if (Math.abs(initialDistance) < 2) {
    window.scrollTo(0, targetTop);
    return Promise.resolve();
  }

  let startTop = initialTop;
  let distance = initialDistance;

  if (Math.abs(initialDistance) > 1400) {
    const landingOffset = Math.min(920, Math.max(520, Math.abs(initialDistance) * 0.18));
    const preJumpTop = targetTop - Math.sign(initialDistance) * landingOffset;
    const clampedPreJumpTop = Math.max(0, preJumpTop);
    window.scrollTo(0, clampedPreJumpTop);
    startTop = clampedPreJumpTop;
    distance = targetTop - startTop;
  }

  const duration = Math.min(820, Math.max(320, Math.abs(distance) * 0.5));

  return new Promise((resolve) => {
    const startTime = performance.now();

    const step = (now) => {
      const elapsed = now - startTime;
      const progress = Math.min(1, elapsed / duration);
      const eased = easeOutCubic(progress);

      window.scrollTo(0, startTop + distance * eased);

      if (progress < 1) {
        activePhpScrollAnimation = window.requestAnimationFrame(step);
        return;
      }

      activePhpScrollAnimation = 0;
      window.scrollTo(0, targetTop);
      resolve();
    };

    activePhpScrollAnimation = window.requestAnimationFrame(step);
  });
}

function getPhpJumpTargets(target) {
  const visibleContainer = target.closest(".php-example-block, .php-question-block, .php-note-block, .php-phase");

  return {
    scrollTarget: visibleContainer || target,
    exactTarget: target,
    visibleContainer
  };
}

function slugify(value) {
  return String(value ?? "")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

function extractInlineKeywords(value) {
  return [...String(value ?? "").matchAll(/`([^`]+)`/g)].map((match) => match[1].trim()).filter(Boolean);
}

function collectPhpKeywords(levels) {
  const groupedEntries = new Map();

  const addKeyword = (keyword, targetId, groupKey, groupLabel, weight = 0) => {
    const cleanKeyword = String(keyword ?? "").trim();
    if (!cleanKeyword || !targetId || !groupKey) {
      return;
    }

    if (!groupedEntries.has(groupKey)) {
      groupedEntries.set(groupKey, {
        key: groupKey,
        label: groupLabel,
        items: new Map()
      });
    }

    const group = groupedEntries.get(groupKey);
    const key = cleanKeyword.toLowerCase();
    const current = group.items.get(key);
    if (!current || weight > current.weight) {
      group.items.set(key, { keyword: cleanKeyword, targetId, weight });
    }
  };

  const addTextKeywords = (textValue, targetId, groupKey, groupLabel, weight = 0) => {
    extractInlineKeywords(textValue).forEach((keyword) => addKeyword(keyword, targetId, groupKey, groupLabel, weight));
  };

  levels.forEach((level) => {
    const levelId = level.anchor;
    const groupKey = level.anchor;
    const groupLabel = `${level.badge} · ${level.title}`;
    addKeyword(level.title, levelId, groupKey, groupLabel, 1);
    addTextKeywords(level.summary, levelId, groupKey, groupLabel, 1);
    (level.highlights || []).forEach((item) => addTextKeywords(item, levelId, groupKey, groupLabel, 1));
    (level.questions || []).forEach((item, index) => {
      const questionId = `${levelId}-question-${index + 1}`;
      addKeyword(item.tag, questionId, groupKey, groupLabel, 2);
      addTextKeywords(item.question, questionId, groupKey, groupLabel, 2);
      addTextKeywords(item.answer, questionId, groupKey, groupLabel, 2);
    });

    (level.modules || []).forEach((module, index) => {
      const moduleId = `${levelId}-module-${index + 1}`;
      addKeyword(module.title, moduleId, groupKey, groupLabel, 2);
      addTextKeywords(module.description, moduleId, groupKey, groupLabel, 2);
      (module.bullets || []).forEach((bullet) => addTextKeywords(bullet, moduleId, groupKey, groupLabel, 2));
    });

    (level.phases || []).forEach((phase, phaseIndex) => {
      const phaseId = `${levelId}-phase-${phaseIndex + 1}`;
      addKeyword(phase.title, phaseId, groupKey, groupLabel, 3);
      addTextKeywords(phase.intro, phaseId, groupKey, groupLabel, 3);

      (phase.topics || []).forEach((topic, topicIndex) => {
        const topicId = `${phaseId}-topic-${topicIndex + 1}`;
        addKeyword(topic.term, topicId, groupKey, groupLabel, 4);
        addTextKeywords(topic.body, topicId, groupKey, groupLabel, 4);
        addTextKeywords(topic.note, topicId, groupKey, groupLabel, 4);
      });

      (phase.examples || []).forEach((example, exampleIndex) => {
        const exampleId = `${phaseId}-example-${exampleIndex + 1}`;
        addKeyword(example.title, exampleId, groupKey, groupLabel, 2);
        addTextKeywords(example.description, exampleId, groupKey, groupLabel, 2);
      });
    });

    (level.examples || []).forEach((example, index) => {
      const exampleId = `${levelId}-example-${index + 1}`;
      addKeyword(example.title, exampleId, groupKey, groupLabel, 2);
      addTextKeywords(example.description, exampleId, groupKey, groupLabel, 2);
    });
  });

  return [...groupedEntries.values()].map((group) => ({
    ...group,
    items: [...group.items.values()].sort((left, right) => left.keyword.localeCompare(right.keyword))
  }));
}

function renderPhpKeywordDirectory(levels, language) {
  const ui = PHP_KEYWORD_UI[language];
  const keywords = collectPhpKeywords(levels);
  const hint = document.getElementById("phpKeywordHint");
  const title = document.getElementById("phpKeywordTitle");
  const subtitle = document.getElementById("phpKeywordSubtitle");
  const searchLabel = document.getElementById("phpKeywordSearchLabel");
  const search = document.getElementById("phpKeywordSearch");
  const quick = document.getElementById("phpKeywordQuick");
  const toggle = document.getElementById("phpKeywordToggle");
  const results = document.getElementById("phpKeywordResults");

  if (!hint || !title || !subtitle || !searchLabel || !search || !quick || !toggle || !results) {
    return;
  }

  hint.innerHTML = formatRichText(ui.hint);
  title.innerHTML = formatRichText(ui.title);
  subtitle.innerHTML = formatRichText(ui.subtitle);
  searchLabel.textContent = ui.searchLabel;
  search.placeholder = ui.searchPlaceholder;

  const quickItems = keywords
    .flatMap((group) => group.items.map((item) => ({ ...item, groupLabel: group.label })))
    .sort((left, right) => right.weight - left.weight || left.keyword.localeCompare(right.keyword))
    .slice(0, 10);

  quick.innerHTML = `
    <div class="php-keyword-quick-head">${ui.quickTitle}</div>
    <div class="php-keyword-chip-list">
      ${quickItems
        .map(
          (entry) => `
            <a class="php-keyword-chip php-keyword-chip-quick" href="#${entry.targetId}" data-target-id="${entry.targetId}">
              ${escapeHtml(entry.keyword)}
            </a>
          `
        )
        .join("")}
    </div>
  `;

  const renderResults = (query = "") => {
    const normalizedQuery = query.trim().toLowerCase();
    const groups = keywords
      .map((group) => ({
        ...group,
        items: group.items.filter((entry) => entry.keyword.toLowerCase().includes(normalizedQuery))
      }))
      .filter((group) => group.items.length);

    if (!groups.length) {
      results.innerHTML = `<p class="php-keyword-empty">${ui.empty}</p>`;
      return;
    }

    results.innerHTML = groups
      .map(
        (group) => `
          <section class="php-keyword-group">
            <div class="php-keyword-group-head">
              <h3>${escapeHtml(group.label)}</h3>
              <span>${group.items.length} ${ui.countSuffix}</span>
            </div>
            <div class="php-keyword-chip-list">
              ${group.items
                .map(
                  (entry) => `
                    <a class="php-keyword-chip" href="#${entry.targetId}" data-target-id="${entry.targetId}">
                      ${escapeHtml(entry.keyword)}
                    </a>
                  `
                )
                .join("")}
            </div>
          </section>
        `
      )
      .join("");
  };

  let expanded = false;
  const syncToggleState = () => {
    const searching = search.value.trim().length > 0;
    const shouldShow = expanded || searching;
    results.toggleAttribute("hidden", !shouldShow);
    toggle.textContent = shouldShow ? ui.hideAll : ui.showAll;
    toggle.setAttribute("aria-expanded", String(shouldShow));
  };

  search.value = "";
  search.oninput = () => {
    renderResults(search.value);
    syncToggleState();
  };
  toggle.onclick = () => {
    expanded = !expanded;
    renderResults(search.value);
    syncToggleState();
  };
  renderResults();
  syncToggleState();
}

function bindPhpKeywordJumps() {
  document.querySelectorAll(".php-keyword-chip").forEach((chip) => {
    chip.onclick = async (event) => {
      event.preventDefault();
      const targetId = chip.dataset.targetId;
      const target = targetId ? document.getElementById(targetId) : null;
      if (!target) {
        return;
      }

      const jumpTargets = getPhpJumpTargets(target);
      const headerOffset = 104;
      const targetTop = Math.max(0, jumpTargets.scrollTarget.getBoundingClientRect().top + window.scrollY - headerOffset);

      await animateWindowScroll(targetTop);

      const highlightTargets = [jumpTargets.exactTarget, jumpTargets.visibleContainer].filter(Boolean);
      highlightTargets.forEach((item) => {
        item.classList.remove("php-jump-target");
        item.classList.remove("php-jump-target-parent");
        item.classList.remove("php-jump-target-exact");
      });
      window.setTimeout(() => {
        if (jumpTargets.visibleContainer) {
          jumpTargets.visibleContainer.classList.add("php-jump-target", "php-jump-target-parent");
        }
        jumpTargets.exactTarget.classList.add("php-jump-target", "php-jump-target-exact");
        window.setTimeout(() => {
          highlightTargets.forEach((item) => {
            item.classList.remove("php-jump-target");
            item.classList.remove("php-jump-target-parent");
            item.classList.remove("php-jump-target-exact");
          });
        }, 2800);
      }, 120);
    };
  });
}

function createPhpLevelSwitcher(pageKey, page, overviewPage, levels, language) {
  const currentValue = pageKey === "php" ? "php" : pageKey.replace("php-", "");
  const options = [
    {
      value: "php",
      href: PHP_LEVEL_PAGE_MAP.php,
      label: text(overviewPage.title, language)
    },
    ...levels.map((level) => ({
      value: level.key,
      href: PHP_LEVEL_PAGE_MAP[level.key],
      label: level.title
    }))
  ];

  return `
    <label class="php-level-switcher-label">
      <span>${language === "vi" ? "Chọn level PHP" : "Choose PHP level"}</span>
      <select class="php-level-switcher-select" id="phpLevelSelect">
        ${options
          .map(
            (option) => `
              <option value="${option.value}" data-href="${option.href}"${option.value === currentValue ? " selected" : ""}>
                ${escapeHtml(option.label)}
              </option>
            `
          )
          .join("")}
      </select>
    </label>
  `;
}

function bindPhpLevelSwitcher() {
  const select = document.getElementById("phpLevelSelect");
  if (!select) {
    return;
  }

  select.onchange = () => {
    const option = select.options[select.selectedIndex];
    const href = option?.dataset.href;
    if (href) {
      window.location.href = href;
    }
  };
}

function getRoadmapProgress() {
  try {
    const saved = JSON.parse(localStorage.getItem(ROADMAP_PROGRESS_KEY) || "[]");
    return Array.isArray(saved) ? new Set(saved) : new Set();
  } catch {
    return new Set();
  }
}

function saveRoadmapProgress(progress) {
  localStorage.setItem(ROADMAP_PROGRESS_KEY, JSON.stringify([...progress]));
}

function getRoadmapCollapsed() {
  try {
    const saved = JSON.parse(localStorage.getItem(ROADMAP_COLLAPSE_KEY) || "[]");
    return Array.isArray(saved) ? new Set(saved) : new Set();
  } catch {
    return new Set();
  }
}

function saveRoadmapCollapsed(collapsed) {
  localStorage.setItem(ROADMAP_COLLAPSE_KEY, JSON.stringify([...collapsed]));
}

function normalizeRoadmapBranchKey(value) {
  return String(value || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .trim();
}

function getRoadmapBranchIcon(branch) {
  const icon = (paths) => `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
      ${paths}
    </svg>
  `;
  const source = `${branch.badge || ""} ${branch.title || ""}`;
  const key = normalizeRoadmapBranchKey(source);
  if (key.includes("frontend")) {
    return icon('<path d="M4 6h16v12H4z" /><path d="M9 10l-2 2 2 2" /><path d="M15 10l2 2-2 2" />');
  }
  if (key.includes("git")) {
    return icon('<circle cx="6" cy="12" r="2" /><circle cx="12" cy="6" r="2" /><circle cx="18" cy="18" r="2" /><path d="M8 11l2-3" /><path d="M13.5 7.5l3 8" />');
  }
  if (key.includes("linux")) {
    return icon('<path d="M8 7c0-2 1.4-3 4-3s4 1 4 3v4.5c0 1.6-.7 2.8-2 3.7l1 3.8H9l1-3.8c-1.3-.9-2-2.1-2-3.7z" /><circle cx="10" cy="9.5" r="0.9" fill="currentColor" stroke="none" /><circle cx="14" cy="9.5" r="0.9" fill="currentColor" stroke="none" /><path d="M10.5 12.6c.6.5 1 .7 1.5.7s.9-.2 1.5-.7" />');
  }
  if (key.includes("http")) {
    return icon('<path d="M4 12a8 8 0 1 0 16 0a8 8 0 1 0-16 0" /><path d="M4.5 9h15" /><path d="M4.5 15h15" /><path d="M12 4c2 2.2 3 4.8 3 8s-1 5.8-3 8c-2-2.2-3-4.8-3-8s1-5.8 3-8z" />');
  }
  if (key.includes("database")) {
    return icon('<ellipse cx="12" cy="6.5" rx="6.5" ry="2.5" /><path d="M5.5 6.5v7c0 1.4 2.9 2.5 6.5 2.5s6.5-1.1 6.5-2.5v-7" /><path d="M5.5 10c0 1.4 2.9 2.5 6.5 2.5s6.5-1.1 6.5-2.5" />');
  }
  if (key.includes("php")) {
    return icon('<path d="M7 6h5.5a3 3 0 1 1 0 6H9v6H7z" /><path d="M15 9h2.5a2.5 2.5 0 1 1 0 5H15z" />');
  }
  if (key.includes("oop")) {
    return icon('<rect x="4.5" y="5" width="6" height="6" rx="1.5" /><rect x="13.5" y="5" width="6" height="6" rx="1.5" /><rect x="9" y="13" width="6" height="6" rx="1.5" /><path d="M10.5 8h3" /><path d="M12 11v2" /><path d="M15 8h1.5" />');
  }
  if (key.includes("composer")) {
    return icon('<path d="M8 5h8l2 3v9a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8z" /><path d="M8 5v4h10" /><path d="M10 12h4" /><path d="M10 15h4" />');
  }
  if (key.includes("thiet ke code") || key.includes("design pattern") || key.includes("solid") || key.includes("dependency injection")) {
    return icon('<path d="M8 7h8" /><path d="M8 12h8" /><path d="M8 17h8" /><circle cx="6" cy="7" r="1.5" /><circle cx="18" cy="12" r="1.5" /><circle cx="6" cy="17" r="1.5" />');
  }
  if (key.includes("framework")) {
    return icon('<path d="M4 7l8-3 8 3-8 3z" /><path d="M4 12l8 3 8-3" /><path d="M4 17l8 3 8-3" />');
  }
  if (key.includes("laravel")) {
    return icon('<path d="M6 7.5l6-3.2 6 3.2v7L12 18l-6-3.3z" /><path d="M12 4.3v6.5" /><path d="M6 7.5l6 3.3 6-3.3" />');
  }
  if (key.includes("api") || key.includes("oauth")) {
    return icon('<path d="M8 8l-3 3 3 3" /><path d="M16 8l3 3-3 3" /><path d="M13 5l-2 14" />');
  }
  if (key.includes("chat luong") || key.includes("testing") || key.includes("security") || key.includes("performance")) {
    return icon('<path d="M12 4l6 2.8v4.3c0 4.1-2.4 6.9-6 8.9c-3.6-2-6-4.8-6-8.9V6.8z" /><path d="M9.3 11.9l1.8 1.8 3.8-4.3" />');
  }
  if (key.includes("devops")) {
    return icon('<path d="M7 10V7.5A2.5 2.5 0 0 1 9.5 5h5A2.5 2.5 0 0 1 17 7.5V10" /><rect x="4" y="10" width="16" height="9" rx="2" /><path d="M10 14h4" />');
  }
  if (key.includes("debug")) {
    return icon('<circle cx="11" cy="11" r="5" /><path d="M15 15l4 4" /><path d="M11 8v3l2 2" />');
  }
  if (key.includes("workflow") || key.includes("docs")) {
    return icon('<path d="M8 4.5h6l3 3V19H8a2 2 0 0 1-2-2V6.5a2 2 0 0 1 2-2z" /><path d="M14 4.5v3h3" /><path d="M9.5 11h5" /><path d="M9.5 14h5" />');
  }

  const fallback = String(branch.badge || branch.title || "").trim();
  const words = fallback.split(/\s+/).filter(Boolean);
  const label = words.length >= 2
    ? `${words[0][0] || ""}${words[1][0] || ""}`.toUpperCase()
    : fallback.slice(0, 2).toUpperCase();
  return `<span>${escapeHtml(label)}</span>`;
}

function bindRoadmapFilters(language) {
  const search = document.getElementById("roadmapFilterSearch");
  const clear = document.getElementById("roadmapFilterClear");
  const summary = document.getElementById("roadmapFilterSummary");
  const progressValue = document.getElementById("roadmapProgressValue");
  const progressFill = document.getElementById("roadmapProgressFill");
  const toneButtons = document.querySelectorAll(".roadmap-filter-tone");
  const overviewLinks = document.querySelectorAll(".roadmap-overview-link");
  const nodes = document.querySelectorAll(".roadmap-tree-node");
  const toggleButtons = document.querySelectorAll(".roadmap-progress-toggle");
  const collapseButtons = document.querySelectorAll(".roadmap-collapse-toggle");
  const studyModeToggle = document.getElementById("roadmapStudyMode");

  if (!search || !clear || !summary || !nodes.length) {
    return;
  }

  const labels = {
    en: {
      all: "Showing all branches",
      filtered: (count, total) => `Showing ${count} of ${total} branches`,
      done: "Marked done",
      undo: "Mark as learning",
      collapse: "Collapse branch",
      expand: "Expand branch",
      studyOff: "Study mode",
      studyOn: "Study mode: unfinished"
    },
    vi: {
      all: "Đang hiển thị toàn bộ nhánh",
      filtered: (count, total) => `Đang hiển thị ${count}/${total} nhánh`,
      done: "Đã học xong",
      undo: "Đánh dấu đang học",
      collapse: "Thu gọn nhánh",
      expand: "Mở nhánh",
      studyOff: "Chế độ học",
      studyOn: "Chế độ học: chưa xong"
    }
  }[language];

  let activeTone = "all";
  const progress = getRoadmapProgress();
  const collapsed = getRoadmapCollapsed();
  let studyMode = false;

  const syncProgressUi = () => {
    let completed = 0;
    nodes.forEach((node) => {
      const nodeId = node.id;
      const isDone = progress.has(nodeId);
      node.classList.toggle("done", isDone);
      if (isDone) {
        completed += 1;
      }
      const button = node.querySelector(".roadmap-progress-toggle");
      if (button) {
        button.classList.toggle("done", isDone);
        button.textContent = isDone ? labels.done : labels.undo;
        button.setAttribute("aria-pressed", String(isDone));
      }
    });

    overviewLinks.forEach((link) => {
      const targetId = link.getAttribute("href")?.replace("#", "");
      const isDone = targetId ? progress.has(targetId) : false;
      link.classList.toggle("done", isDone);
    });

    const ratio = nodes.length ? Math.round((completed / nodes.length) * 100) : 0;
    if (progressValue) {
      progressValue.textContent = `${completed}/${nodes.length} · ${ratio}%`;
    }
    if (progressFill) {
      progressFill.style.width = `${ratio}%`;
    }
  };

  const syncCollapseUi = () => {
    nodes.forEach((node) => {
      const nodeId = node.id;
      const isCollapsed = collapsed.has(nodeId);
      node.classList.toggle("collapsed", isCollapsed);
      const button = node.querySelector(".roadmap-collapse-toggle");
      if (button) {
        button.classList.toggle("collapsed", isCollapsed);
        button.textContent = isCollapsed ? labels.expand : labels.collapse;
        button.setAttribute("aria-pressed", String(isCollapsed));
      }
    });
  };

  const syncStudyModeUi = () => {
    if (!studyModeToggle) {
      return;
    }
    studyModeToggle.classList.toggle("active", studyMode);
    studyModeToggle.setAttribute("aria-pressed", String(studyMode));
    studyModeToggle.textContent = studyMode ? labels.studyOn : labels.studyOff;
  };

  const applyFilter = () => {
    const query = search.value.trim().toLowerCase();
    let visibleCount = 0;

    nodes.forEach((node) => {
      const tone = [...node.classList].find((value) => value.startsWith("tone-"))?.replace("tone-", "")
        || node.querySelector(".roadmap-tree-dot")?.className.match(/tone-([a-z]+)/)?.[1]
        || "grow";
      const matchesTone = activeTone === "all" || tone === activeTone;
      const matchesQuery = !query || node.textContent.toLowerCase().includes(query);
      const matchesStudyMode = !studyMode || !progress.has(node.id);
      const visible = matchesTone && matchesQuery && matchesStudyMode;
      node.toggleAttribute("hidden", !visible);
      if (visible) {
        visibleCount += 1;
      }
    });

    overviewLinks.forEach((link) => {
      const targetId = link.getAttribute("href")?.replace("#", "");
      const target = targetId ? document.getElementById(targetId) : null;
      link.toggleAttribute("hidden", !target || target.hasAttribute("hidden"));
    });

    summary.textContent = visibleCount === nodes.length && activeTone === "all" && !query
      ? labels.all
      : labels.filtered(visibleCount, nodes.length);
  };

  search.oninput = applyFilter;
  clear.onclick = () => {
    search.value = "";
    activeTone = "all";
    studyMode = false;
    toneButtons.forEach((button) => {
      button.classList.toggle("active", button.dataset.tone === "all");
      button.setAttribute("aria-pressed", String(button.dataset.tone === "all"));
    });
    if (studyModeToggle) {
      syncStudyModeUi();
    }
    applyFilter();
  };

  toneButtons.forEach((button) => {
    button.onclick = () => {
      activeTone = button.dataset.tone || "all";
      toneButtons.forEach((item) => {
        const active = item === button;
        item.classList.toggle("active", active);
        item.setAttribute("aria-pressed", String(active));
      });
      applyFilter();
    };
  });

  if (studyModeToggle) {
    studyModeToggle.onclick = () => {
      studyMode = !studyMode;
      syncStudyModeUi();
      applyFilter();
    };
  }

  toggleButtons.forEach((button) => {
    button.onclick = () => {
      const node = button.closest(".roadmap-tree-node");
      const nodeId = node?.id;
      if (!nodeId) {
        return;
      }

      if (progress.has(nodeId)) {
        progress.delete(nodeId);
      } else {
        progress.add(nodeId);
      }

      saveRoadmapProgress(progress);
      syncProgressUi();
      applyFilter();
    };
  });

  collapseButtons.forEach((button) => {
    button.onclick = () => {
      const node = button.closest(".roadmap-tree-node");
      const nodeId = node?.id;
      if (!nodeId) {
        return;
      }

      if (collapsed.has(nodeId)) {
        collapsed.delete(nodeId);
      } else {
        collapsed.add(nodeId);
      }

      saveRoadmapCollapsed(collapsed);
      syncCollapseUi();
    };
  });

  syncProgressUi();
  syncCollapseUi();
  syncStudyModeUi();
  applyFilter();
}

function bindHeaderDropdowns() {
  const dropdowns = document.querySelectorAll(".site-nav-dropdown");
  if (!dropdowns.length) {
    return;
  }

  const closeAll = () => {
    dropdowns.forEach((dropdown) => {
      dropdown.classList.remove("open", "open-locked");
      if (dropdown._closeTimer) {
        window.clearTimeout(dropdown._closeTimer);
        dropdown._closeTimer = 0;
      }
    });
    document.querySelectorAll(".site-nav-trigger").forEach((trigger) => {
      trigger.setAttribute("aria-expanded", "false");
    });
  };

  dropdowns.forEach((dropdown) => {
    const trigger = dropdown.querySelector(".site-nav-trigger");
    if (!trigger) {
      return;
    }

    const openDropdown = (locked = false) => {
      dropdowns.forEach((item) => {
        if (item === dropdown) {
          return;
        }
        item.classList.remove("open", "open-locked");
        if (item._closeTimer) {
          window.clearTimeout(item._closeTimer);
          item._closeTimer = 0;
        }
        const itemTrigger = item.querySelector(".site-nav-trigger");
        if (itemTrigger) {
          itemTrigger.setAttribute("aria-expanded", "false");
        }
      });
      if (dropdown._closeTimer) {
        window.clearTimeout(dropdown._closeTimer);
        dropdown._closeTimer = 0;
      }
      dropdown.classList.add("open");
      dropdown.classList.toggle("open-locked", locked);
      trigger.setAttribute("aria-expanded", "true");
    };

    const scheduleClose = () => {
      if (dropdown.classList.contains("open-locked")) {
        return;
      }
      if (dropdown._closeTimer) {
        window.clearTimeout(dropdown._closeTimer);
      }
      dropdown._closeTimer = window.setTimeout(() => {
        dropdown.classList.remove("open");
        trigger.setAttribute("aria-expanded", "false");
      }, 160);
    };

    trigger.onclick = (event) => {
      event.preventDefault();
      event.stopPropagation();
      const isLocked = dropdown.classList.contains("open-locked");
      closeAll();
      if (!isLocked) {
        openDropdown(true);
      }
    };

    dropdown.addEventListener("mouseenter", () => openDropdown(false));
    dropdown.addEventListener("mouseleave", scheduleClose);
    dropdown.addEventListener("focusin", () => openDropdown(false));
  });

  document.addEventListener("click", (event) => {
    if (!event.target.closest(".site-nav-dropdown")) {
      closeAll();
    }
  });
}

function setActiveLanguage(language) {
  document.querySelectorAll(".lang-btn").forEach((button) => {
    button.classList.toggle("active", button.dataset.lang === language);
    button.setAttribute("aria-pressed", String(button.dataset.lang === language));
  });
}

function createHeaderNav(data, language, pageKey) {
  const phpMenu = PHP_HEADER_MENU[language];
  const laravelPage = data.pages?.laravel || {};
  const laravelMenu = getLaravelTopics(laravelPage);
  const interviewPage = data.pages?.interview || {};
  const interviewMenu = getInterviewTopics(interviewPage);
  const vibeCodingPage = data.pages?.vibeCoding || {};
  const videoMenu = getVideoTopics(vibeCodingPage);
  const navigation = [
    { key: "landing", label: text(data.common.navigation.home, language), href: getPageHref("landing"), type: "link" },
    { key: "roadmap", label: text(data.common.navigation.roadmap, language), href: getPageHref("roadmap"), type: "link" },
    { key: "glossary", label: text(data.common.navigation.glossary, language), href: getPageHref("glossary"), type: "link" },
    {
      key: "php",
      label: text(data.common.navigation.php, language),
      href: getPageHref("php"),
      type: "dropdown",
      active: pageKey === "php" || pageKey.startsWith("php-"),
      items: [
        { label: phpMenu.overview.label, desc: phpMenu.overview.desc, href: PHP_LEVEL_PAGE_MAP.php, active: pageKey === "php" },
        { label: phpMenu.starter.label, desc: phpMenu.starter.desc, href: PHP_LEVEL_PAGE_MAP.starter, active: pageKey === "php-starter" },
        { label: phpMenu.intermediate.label, desc: phpMenu.intermediate.desc, href: PHP_LEVEL_PAGE_MAP.intermediate, active: pageKey === "php-intermediate" },
        { label: phpMenu.advanced.label, desc: phpMenu.advanced.desc, href: PHP_LEVEL_PAGE_MAP.advanced, active: pageKey === "php-advanced" }
      ]
    },
    laravelMenu.length
      ? {
          key: "laravel",
          label: text(data.common.navigation.laravel, language),
          href: getPageHref("laravel"),
          type: "dropdown",
          active: isLaravelPage(pageKey),
          items: laravelMenu.map((item) => ({
            label: text(item.label, language),
            desc: text(item.desc, language),
            href: item.href,
            active: item.anchor === "laravel-overview" ? pageKey === "laravel" : pageKey === item.pageKey
          }))
        }
      : { key: "laravel", label: text(data.common.navigation.laravel, language), href: getPageHref("laravel"), type: "link" },
    {
      key: "vibe-coding",
      label: text(data.common.navigation.vibeCoding, language),
      href: getPageHref("vibe-coding"),
      type: "dropdown",
      active: isVideoPage(pageKey),
      items: [
        {
          label: language === "vi" ? "Tổng quan" : "Overview",
          desc: language === "vi"
            ? "Hub tổng quan của Vibe Coding và 4 nhánh học cùng AI."
            : "Overview hub for Vibe Coding and the four AI-assisted branches.",
          href: getPageHref("vibe-coding"),
          active: pageKey === "vibe-coding" || pageKey === "videos"
        },
        ...videoMenu.map((item) => ({
          label: text(item.label, language),
          desc: text(item.desc, language),
          href: item.href,
          active: pageKey === item.pageKey
        }))
      ]
    },
    {
      key: "interview",
      label: text(data.common.navigation.interview, language),
      href: getPageHref("interview"),
      type: "dropdown",
      active: isInterviewPage(pageKey),
      items: [
        {
          label: language === "vi" ? "Tổng quan" : "Overview",
          desc: language === "vi"
            ? "Hub tổng quan của khu phỏng vấn và các level đang có."
            : "Overview hub for the interview area and its current levels.",
          href: getPageHref("interview"),
          active: pageKey === "interview"
        },
        ...interviewMenu.map((item) => ({
          label: text(item.label, language),
          desc: text(item.desc, language),
          href: item.href,
          active: pageKey === item.pageKey
        }))
      ]
    }
  ];
  const theme = getTheme();

  return `
    <div class="site-header-shell">
      <div class="site-header-brand">
        <a class="brand-link" href="${SITE_ROOT}/index.html">Laravel Labs</a>
      </div>
      <nav class="site-nav" aria-label="Primary navigation">
        ${navigation
          .map(
            (item) => {
              if (item.type === "dropdown") {
                return `
                  <div class="site-nav-dropdown site-nav-dropdown--${item.key}${item.active ? " active" : ""}">
                    <button
                      type="button"
                      class="site-nav-trigger${item.active ? " active" : ""}"
                      aria-expanded="false"
                      aria-haspopup="true"
                    >
                      ${item.label}
                    </button>
                    <div class="site-nav-submenu" aria-label="${item.label}">
                      ${item.items
                        .map(
                          (subItem) => `
                            <a class="site-nav-submenu-link${subItem.active ? " active" : ""}" href="${subItem.href}">
                              <span class="site-nav-submenu-copy">
                                <strong>${subItem.label}</strong>
                                <span>${subItem.desc}</span>
                              </span>
                            </a>
                          `
                        )
                        .join("")}
                    </div>
                  </div>
                `;
              }

              return `
                <a class="site-nav-link${item.key === pageKey ? " active" : ""}" href="${item.href}">
                  ${item.label}
                </a>
              `;
            }
          )
          .join("")}
      </nav>
      <div class="site-header-actions">
        <button type="button" class="header-search-toggle" id="globalSearchToggle">
          ${language === "vi" ? "Tìm" : "Search"}
        </button>
        <div class="header-lang-switch lang-switch" aria-label="Language switcher">
          <button type="button" class="lang-btn${language === "en" ? " active" : ""}" data-lang="en" aria-pressed="${language === "en"}">EN</button>
          <button type="button" class="lang-btn${language === "vi" ? " active" : ""}" data-lang="vi" aria-pressed="${language === "vi"}">VI</button>
        </div>
        <button
          type="button"
          class="theme-toggle${theme === "dark" ? " dark" : ""}"
          id="themeToggle"
          aria-label="Toggle color theme"
          aria-pressed="${theme === "dark"}"
        >
          <span class="theme-toggle-label theme-toggle-label-light">${text(data.common.theme.light, language)}</span>
          <span class="theme-toggle-switch" aria-hidden="true">
            <span class="theme-toggle-switch-track"></span>
            <span class="theme-toggle-thumb"></span>
          </span>
          <span class="theme-toggle-label theme-toggle-label-dark">${text(data.common.theme.dark, language)}</span>
        </button>
      </div>
    </div>
  `;
}

function renderGlobalHeader(data, language, pageKey) {
  let host = document.getElementById("siteHeader");
  if (!host) {
    host = document.createElement("header");
    host.id = "siteHeader";
    host.className = "site-header";
    document.body.prepend(host);
  }

  host.innerHTML = createHeaderNav(data, language, pageKey);
}

function renderPageUtilityBar(language) {
  const pageKey = document.body.dataset.page || "";
  const main = document.querySelector(".detail-main");
  if (!main || pageKey === "landing" || pageKey === "roadmap") {
    const existing = document.getElementById("pageUtilityBar");
    if (existing) {
      existing.remove();
    }
    return;
  }

  let host = document.getElementById("pageUtilityBar");
  if (!host) {
    host = document.createElement("section");
    host.id = "pageUtilityBar";
    host.className = "page-utility-bar";
    main.prepend(host);
  }

  const labels = language === "vi"
    ? {
        title: "Tiến độ trang này",
        subtitle: "Đánh dấu xong từng mục để nhớ mình đã đi tới đâu.",
        reset: "Xóa đánh dấu trang"
      }
    : {
        title: "Progress on this page",
        subtitle: "Mark items as done so you can resume without losing context.",
        reset: "Reset page progress"
      };

  host.innerHTML = `
    <div class="page-utility-copy">
      <strong>${labels.title}</strong>
      <p>${labels.subtitle}</p>
    </div>
    <div class="page-utility-progress">
      <span class="page-utility-progress-value" id="pageUtilityProgressValue">0 / 0</span>
      <div class="page-utility-progress-track">
        <span class="page-utility-progress-fill" id="pageUtilityProgressFill"></span>
      </div>
    </div>
    <button type="button" class="page-utility-reset" id="pageUtilityReset">${labels.reset}</button>
  `;

  const reset = document.getElementById("pageUtilityReset");
  if (reset) {
    reset.onclick = () => {
      clearPageProgress(pageKey);
      syncContentProgressUi(language);
    };
  }
}

function syncContentProgressUi(language) {
  const labels = language === "vi"
    ? {
        markDone: "Đánh dấu xong",
        markUndo: "Bỏ đánh dấu"
      }
    : {
        markDone: "Mark done",
        markUndo: "Undo"
      };
  const toggles = [...document.querySelectorAll("[data-progress-id]")];
  const uniqueIds = [...new Set(toggles.map((toggle) => toggle.dataset.progressId).filter(Boolean))];
  const doneCount = uniqueIds.filter((progressId) => isContentItemDone(progressId)).length;
  const totalCount = uniqueIds.length;
  const progressValue = document.getElementById("pageUtilityProgressValue");
  const progressFill = document.getElementById("pageUtilityProgressFill");
  const utilityBar = document.getElementById("pageUtilityBar");

  if (utilityBar) {
    utilityBar.hidden = totalCount === 0;
  }
  if (progressValue) {
    progressValue.textContent = `${doneCount} / ${totalCount}`;
  }
  if (progressFill) {
    progressFill.style.width = `${totalCount ? Math.round((doneCount / totalCount) * 100) : 0}%`;
  }

  toggles.forEach((toggle) => {
    const progressId = toggle.dataset.progressId;
    const done = isContentItemDone(progressId);
    toggle.classList.toggle("done", done);
    toggle.setAttribute("aria-pressed", String(done));
    toggle.textContent = done ? labels.markUndo : labels.markDone;
    const item = toggle.closest("[data-progress-item]");
    if (item) {
      item.classList.toggle("done", done);
    }
  });

  document.querySelectorAll("[data-section-progress]").forEach((pill) => {
    const sectionKey = pill.dataset.sectionProgress;
    const section = document.querySelector(`.detail-section[data-progress-scope="${sectionKey}"]`);
    if (!section) {
      return;
    }
    const sectionIds = [...new Set(
      [...section.querySelectorAll("[data-progress-id]")]
        .map((toggle) => toggle.dataset.progressId)
        .filter(Boolean)
    )];
    const sectionDone = sectionIds.filter((progressId) => isContentItemDone(progressId)).length;
    const strong = pill.querySelector("strong");
    if (strong) {
      strong.textContent = `${sectionDone} / ${sectionIds.length}`;
    }
  });
}

function bindContentProgress(language) {
  renderPageUtilityBar(language);

  document.querySelectorAll("[data-progress-id]").forEach((toggle) => {
    toggle.onclick = () => {
      const progressId = toggle.dataset.progressId;
      if (!progressId) {
        return;
      }
      toggleContentItemDone(progressId);
      syncContentProgressUi(language);
    };
  });

  document.querySelectorAll(".bullet-code-copy").forEach((button) => {
    button.onclick = async () => {
      const shell = button.closest(".bullet-code-shell, .php-code-shell");
      const code = shell?.querySelector("code");
      const copyLabel = button.dataset.copyLabel || "Copy";
      const copiedLabel = button.dataset.copiedLabel || "Copied";
      if (!code) {
        return;
      }

      button.disabled = true;
      button.classList.remove("is-copied");

      try {
        await copyTextToClipboard(code.textContent || "");
        button.textContent = copiedLabel;
        button.classList.add("is-copied");
        if (button._copyTimer) {
          window.clearTimeout(button._copyTimer);
        }
        button._copyTimer = window.setTimeout(() => {
          button.textContent = copyLabel;
          button.classList.remove("is-copied");
          button.disabled = false;
        }, 1600);
        return;
      } catch (error) {
        button.textContent = copyLabel;
      }

      button.disabled = false;
    };
  });

  syncContentProgressUi(language);
}

async function copyTextToClipboard(value) {
  if (navigator.clipboard?.writeText) {
    await navigator.clipboard.writeText(value);
    return;
  }

  const textarea = document.createElement("textarea");
  textarea.value = value;
  textarea.setAttribute("readonly", "");
  textarea.style.position = "fixed";
  textarea.style.opacity = "0";
  textarea.style.pointerEvents = "none";
  document.body.appendChild(textarea);
  textarea.focus();
  textarea.select();

  const succeeded = document.execCommand("copy");
  textarea.remove();

  if (!succeeded) {
    throw new Error("Copy command failed");
  }
}

function collectSearchText(value, language, collector = []) {
  if (!value) {
    return collector;
  }

  if (typeof value === "string") {
    collector.push(value);
    return collector;
  }

  if (Array.isArray(value)) {
    value.forEach((entry) => collectSearchText(entry, language, collector));
    return collector;
  }

  if (typeof value === "object") {
    if (typeof value.vi === "string" || typeof value.en === "string") {
      collector.push(text(value, language));
      return collector;
    }

    Object.entries(value).forEach(([key, entry]) => {
      if (key === "href" || key === "anchor" || key.startsWith("__")) {
        return;
      }
      collectSearchText(entry, language, collector);
    });
  }

  return collector;
}

function createSearchDoc({ title, group, href, content }) {
  return {
    title: String(title || "").trim(),
    group: String(group || "").trim(),
    href,
    content: String(content || "").replace(/\s+/g, " ").trim()
  };
}

function createPageSearchDocs(pageKey, page, language, groupLabel) {
  const href = getPageHref(pageKey);
  const docs = [
    createSearchDoc({
      title: text(page.title, language),
      group: groupLabel,
      href,
      content: collectSearchText(page, language).join(" ")
    })
  ];

  (page.menu || []).forEach((item) => {
    const targetHref = item.anchor
      ? getPageHref(item.anchor)
      : href;
    docs.push(
      createSearchDoc({
        title: text(item.label, language),
        group: groupLabel,
        href: targetHref,
        content: collectSearchText(item, language).join(" ")
      })
    );
  });

  (page.sections || []).forEach((section, sectionIndex) => {
    const sectionHeading = text(section.heading, language);
    docs.push(
      createSearchDoc({
        title: sectionHeading,
        group: groupLabel,
        href,
        content: collectSearchText(section, language).join(" ")
      })
    );
    (section.items || []).forEach((item, itemIndex) => {
      docs.push(
        createSearchDoc({
          title: text(item.title || item.label, language) || `${sectionHeading} ${itemIndex + 1}`,
          group: sectionHeading || groupLabel,
          href,
          content: collectSearchText(item, language).join(" ")
        })
      );
    });
  });

  return docs.filter((doc) => doc.title || doc.content);
}

function createTopicSearchDocs(topic, language, href, groupLabel) {
  const title = text(topic.heading || topic.title, language);
  const docs = [
    createSearchDoc({
      title,
      group: groupLabel,
      href,
      content: collectSearchText(topic, language).join(" ")
    })
  ];

  (topic.items || []).forEach((item, itemIndex) => {
    docs.push(
      createSearchDoc({
        title: text(item.title || item.label, language) || `${title} ${itemIndex + 1}`,
        group: title || groupLabel,
        href,
        content: collectSearchText(item, language).join(" ")
      })
    );
  });

  return docs.filter((doc) => doc.title || doc.content);
}

async function buildSearchIndex(data, language) {
  if (SEARCH_INDEX_CACHE.has(language)) {
    return SEARCH_INDEX_CACHE.get(language);
  }

  const docs = [];
  Object.entries(data.pages || {}).forEach(([pageKey, page]) => {
    docs.push(...createPageSearchDocs(pageKey, page, language, text(page.title, language)));
  });

  const laravelTopics = await Promise.all(
    getLaravelTopics(data.pages.laravel || {})
      .filter((topic) => topic.anchor !== "laravel-overview")
      .map(async (topic) => ({
        href: topic.href,
        group: `${text(data.pages.laravel.title, language)} · ${text(topic.label, language)}`,
        payload: await loadJson(`${SITE_ROOT}/data/laravel/${topic.topicKey}.${language}.json`)
      }))
  );
  laravelTopics.forEach((topic) => {
    docs.push(...createTopicSearchDocs(topic.payload, language, topic.href, topic.group));
  });

  const interviewTopics = await Promise.all(
    getInterviewTopics(data.pages.interview || {}).map(async (topic) => ({
      href: topic.href,
      group: `${text(data.pages.interview.title, language)} · ${text(topic.label, language)}`,
      payload: await loadJson(`${SITE_ROOT}/data/interview/${topic.topicKey}.${language}.json`)
    }))
  );
  interviewTopics.forEach((topic) => {
    docs.push(...createTopicSearchDocs(topic.payload, language, topic.href, topic.group));
  });

  const videoTopics = await Promise.all(
    getVideoTopics(data.pages.vibeCoding || {}).map(async (topic) => ({
      href: topic.href,
      group: `${text((data.pages.vibeCoding || {}).title, language)} · ${text(topic.label, language)}`,
      payload: await loadJson(`${SITE_ROOT}/data/vibe-coding/${topic.topicKey}.${language}.json`)
    }))
  );
  videoTopics.forEach((topic) => {
    docs.push(...createTopicSearchDocs(topic.payload, language, topic.href, topic.group));
  });

  const phpLevels = await loadPhpLevels(language);
  phpLevels.forEach((level) => {
    docs.push(
      createSearchDoc({
        title: level.title,
        group: `${text(data.pages.php.title, language)} · ${level.badge}`,
        href: PHP_LEVEL_PAGE_MAP[level.key],
        content: collectSearchText(level, language).join(" ")
      })
    );
  });

  const uniqueDocs = docs.filter((doc) => doc.title || doc.content);
  SEARCH_INDEX_CACHE.set(language, uniqueDocs);
  return uniqueDocs;
}

function scoreSearchDoc(doc, query) {
  const loweredQuery = query.toLowerCase();
  const title = doc.title.toLowerCase();
  const group = doc.group.toLowerCase();
  const content = doc.content.toLowerCase();
  let score = 0;

  if (title.startsWith(loweredQuery)) {
    score += 120;
  } else if (title.includes(loweredQuery)) {
    score += 80;
  }

  if (group.includes(loweredQuery)) {
    score += 40;
  }

  if (content.includes(loweredQuery)) {
    score += 20;
    const contentHits = content.split(loweredQuery).length - 1;
    score += Math.min(12, contentHits * 2);
  }

  return score;
}

function createSearchSnippet(doc, query) {
  const loweredQuery = query.toLowerCase();
  const content = doc.content || doc.title;
  const normalized = content.toLowerCase();
  const index = normalized.indexOf(loweredQuery);
  const start = index > 36 ? index - 36 : 0;
  const end = Math.min(content.length, start + 180);
  const snippet = content.slice(start, end).trim();
  return formatRichText(index > 0 ? `...${snippet}` : snippet);
}

function ensureGlobalSearchHost(language) {
  const labels = language === "vi"
    ? {
        title: "Tìm toàn cục",
        subtitle: "Tìm qua Laravel, PHP, Interview, Vibe Coding, glossary và practice.",
        placeholder: "Nhập từ khóa như queue, policy, RESTful API, prompt, nginx...",
        empty: "Chưa có kết quả phù hợp.",
        loading: "Đang lập chỉ mục nội dung...",
        close: "Đóng"
      }
    : {
        title: "Global search",
        subtitle: "Search across Laravel, PHP, Interview, Vibe Coding, glossary, and practice.",
        placeholder: "Type a keyword like queue, policy, RESTful API, prompt, nginx...",
        empty: "No matching result yet.",
        loading: "Building the content index...",
        close: "Close"
      };

  let host = document.getElementById("globalSearchHost");
  if (!host) {
    host = document.createElement("div");
    host.id = "globalSearchHost";
    host.className = "global-search";
    document.body.appendChild(host);
  }

  host.classList.remove("open");
  document.body.classList.remove("search-open");

  host.innerHTML = `
    <div class="global-search-backdrop" data-search-close></div>
    <div class="global-search-panel" role="dialog" aria-modal="true" aria-labelledby="globalSearchTitle">
      <div class="global-search-head">
        <div>
          <strong id="globalSearchTitle">${labels.title}</strong>
          <p>${labels.subtitle}</p>
        </div>
        <button type="button" class="global-search-close" data-search-close>${labels.close}</button>
      </div>
      <label class="global-search-field">
        <input id="globalSearchInput" type="search" placeholder="${labels.placeholder}" autocomplete="off" />
      </label>
      <div class="global-search-results" id="globalSearchResults">
        <p class="global-search-status">${labels.loading}</p>
      </div>
    </div>
  `;

  return { host, labels };
}

function bindGlobalSearch(data, language) {
  const toggle = document.getElementById("globalSearchToggle");
  if (!toggle) {
    return;
  }

  const { host, labels } = ensureGlobalSearchHost(language);
  const results = host.querySelector("#globalSearchResults");
  const input = host.querySelector("#globalSearchInput");
  const closeSearch = () => {
    host.classList.remove("open");
    document.body.classList.remove("search-open");
  };
  const openSearch = async () => {
    host.classList.add("open");
    document.body.classList.add("search-open");
    if (input) {
      input.value = "";
      input.focus();
    }
    results.innerHTML = `<p class="global-search-status">${labels.loading}</p>`;
    const docs = await buildSearchIndex(data, language);
    const renderQuery = (query = "") => {
      const normalizedQuery = query.trim().toLowerCase();
      if (!normalizedQuery) {
        results.innerHTML = docs
          .slice(0, 10)
          .map(
            (doc) => `
              <a class="global-search-result" href="${doc.href}">
                <span class="global-search-group">${escapeHtml(doc.group)}</span>
                <strong>${escapeHtml(doc.title)}</strong>
                <span>${formatRichText(doc.content.slice(0, 160))}</span>
              </a>
            `
          )
          .join("");
        return;
      }

      const ranked = docs
        .map((doc) => ({ doc, score: scoreSearchDoc(doc, normalizedQuery) }))
        .filter((entry) => entry.score > 0)
        .sort((left, right) => right.score - left.score)
        .slice(0, 24);

      if (!ranked.length) {
        results.innerHTML = `<p class="global-search-status">${labels.empty}</p>`;
        return;
      }

      results.innerHTML = ranked
        .map(
          ({ doc }) => `
            <a class="global-search-result" href="${doc.href}">
              <span class="global-search-group">${escapeHtml(doc.group)}</span>
              <strong>${escapeHtml(doc.title)}</strong>
              <span>${createSearchSnippet(doc, normalizedQuery)}</span>
            </a>
          `
        )
        .join("");
    };

    renderQuery();
    if (input) {
      input.oninput = () => renderQuery(input.value);
    }
  };

  toggle.onclick = () => {
    openSearch().catch((error) => {
      results.innerHTML = `<p class="global-search-status">${escapeHtml(error.message || "Search failed.")}</p>`;
    });
  };

  host.querySelectorAll("[data-search-close]").forEach((button) => {
    button.onclick = closeSearch;
  });

  host.onkeydown = (event) => {
    if (event.key === "Escape") {
      closeSearch();
    }
  };
}

function renderReadingDock(language) {
  if (!document.body.dataset.page.startsWith("php")) {
    const existing = document.getElementById("readingDock");
    if (existing) {
      existing.remove();
    }
    return;
  }

  const labels = {
    en: {
      progress: "Reading progress",
      top: "Back to top"
    },
    vi: {
      progress: "Tiến độ đọc",
      top: "Lên đầu trang"
    }
  }[language];

  let dock = document.getElementById("readingDock");
  if (!dock) {
    dock = document.createElement("div");
    dock.id = "readingDock";
    dock.className = "reading-dock";
    document.body.appendChild(dock);
  }

  dock.innerHTML = `
    <div class="reading-dock-progress" aria-label="${labels.progress}">
      <span class="reading-dock-label">${labels.progress}</span>
      <span class="reading-dock-value" id="readingDockValue">0%</span>
      <div class="reading-dock-track">
        <span class="reading-dock-fill" id="readingDockFill"></span>
      </div>
    </div>
    <button type="button" class="reading-dock-top" id="readingDockTop">${labels.top}</button>
  `;

  const topButton = document.getElementById("readingDockTop");
  if (topButton) {
    topButton.onclick = () => animateWindowScroll(0);
  }

  const updateReadingDock = () => {
    const doc = document.documentElement;
    const maxScroll = Math.max(1, doc.scrollHeight - window.innerHeight);
    const ratio = Math.min(1, Math.max(0, window.scrollY / maxScroll));
    const percent = Math.round(ratio * 100);
    const fill = document.getElementById("readingDockFill");
    const value = document.getElementById("readingDockValue");
    if (fill) {
      fill.style.width = `${percent}%`;
    }
    if (value) {
      value.textContent = `${percent}%`;
    }
    dock.classList.toggle("visible", window.scrollY > 220);
  };

  window.removeEventListener("scroll", window._readingDockHandler || (() => {}));
  window._readingDockHandler = updateReadingDock;
  window.addEventListener("scroll", window._readingDockHandler, { passive: true });
  updateReadingDock();
}

function bindThemeToggle(data, language, pageKey, render) {
  const toggle = document.getElementById("themeToggle");
  if (!toggle) {
    return;
  }

  toggle.addEventListener("click", async () => {
    const nextTheme = getTheme() === "dark" ? "light" : "dark";
    localStorage.setItem(THEME_KEY, nextTheme);
    applyTheme(nextTheme);
    await render(language);
  });
}

function createCard(item, language) {
  const href = resolveConfiguredHref(item.href);

  return `
    <article class="service-card landing-card">
      <h3>${text(item.title, language)}</h3>
      <p>${text(item.summary, language)}</p>
      <p class="landing-card-action">
        <a class="btn" href="${escapeHtml(href)}">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 24" aria-hidden="true" focusable="false">
            <path d="m18 0 8 12 10-8-4 20H4L0 4l10 8 8-12z"></path>
          </svg>
          <span>${text(item.button, language)}</span>
        </a>
      </p>
    </article>
  `;
}

function createRepoCard(item, language) {
  const nextLabel = item.nextLabel ? text(item.nextLabel, language) : "";
  const next = item.next ? text(item.next, language) : "";
  const openLabel = item.openLabel ? text(item.openLabel, language) : "";
  const open = item.open ? text(item.open, language) : "";

  return `
    <article class="repo-card">
      <h3>${item.name}</h3>
      <p>${text(item.summary, language)}</p>
      <p><strong>${text(item.focusLabel, language)}</strong> ${text(item.focus, language)}</p>
      ${nextLabel && next ? `<p><strong>${nextLabel}</strong> ${next}</p>` : ""}
      ${openLabel && open ? `<p><strong>${openLabel}</strong> ${open}</p>` : ""}
    </article>
  `;
}

function createBulletItem(item, language, index = 0, questionNumbered = false, questionStyle = "") {
  const title = text(item.title, language);
  const body = text(item.body, language);
  const pageKey = document.body.dataset.page || "site";
  const sectionKey = item.__sectionKey || "section";
  const progressTitleKey = text(item.title, "en") || text(item.title, "vi") || title;
  const progressId = getProgressId(pageKey, sectionKey, item.__globalIndex ?? index, progressTitleKey);
  const done = isContentItemDone(progressId);
  const questionPrefix = questionNumbered
    ? `<span class="bullet-question-number">${language === "vi" ? "Câu" : "Question"} ${String(index + 1).padStart(2, "0")}.</span>`
    : "";
  const labels = language === "vi"
    ? {
        question: "Câu hỏi",
        answer: "Trả lời",
        keyPoints: "Ý chính",
        tip: "Mẹo",
        note: "Lưu ý",
        markDone: "Đánh dấu xong",
        markUndo: "Bỏ đánh dấu",
        copy: "Sao chép",
        copied: "✓ Đã sao chép"
      }
    : {
        question: "Question",
        answer: "Answer",
        keyPoints: "Key points",
        tip: "Tip",
        note: "Note",
        markDone: "Mark done",
        markUndo: "Undo",
        copy: "Copy",
        copied: "✓ Copied"
      };
  const bullets = Array.isArray(item.bullets) && item.bullets.length
    ? `
      <p class="bullet-meta-label">${labels.keyPoints}</p>
      <ul class="bullet-sublist">
        ${item.bullets.map((entry) => `<li>${formatRichText(text(entry, language))}</li>`).join("")}
      </ul>
    `
    : "";
  const code = item.code
    ? `
      <div class="bullet-code-shell">
        <button
          type="button"
          class="bullet-code-copy"
          data-copy-label="${labels.copy}"
          data-copied-label="${labels.copied}"
        >${labels.copy}</button>
        <pre><code>${escapeHtml(text(item.code, language))}</code></pre>
      </div>
    `
    : "";
  const tip = item.tip ? `<p class="bullet-tip"><span class="bullet-meta-label">${labels.tip}</span>${formatRichText(text(item.tip, language))}</p>` : "";
  const note = item.note ? `<p class="bullet-note"><span class="bullet-meta-label">${labels.note}</span>${formatRichText(text(item.note, language))}</p>` : "";
  const links = Array.isArray(item.links) && item.links.length
    ? `
      <div class="bullet-links">
        ${item.links
          .map((entry) => {
            const href = resolveConfiguredHref(entry.href);
            const label = text(entry.label, language);
            const desc = entry.desc ? `<span>${formatRichText(text(entry.desc, language))}</span>` : "";
            return `
              <a class="bullet-link-chip" href="${escapeHtml(href)}">
                <strong>${label}</strong>
                ${desc}
              </a>
            `;
          })
          .join("")}
      </div>
    `
    : "";

  return `
    <li
      class="${questionStyle === "interview" ? "bullet-qa-item" : ""}${done ? " done" : ""}"
      data-progress-item="${progressId}"
    >
      <div class="bullet-item-toolbar">
        <button
          type="button"
          class="content-progress-toggle${done ? " done" : ""}"
          data-progress-id="${progressId}"
          aria-pressed="${done}"
        >
          ${done ? labels.markUndo : labels.markDone}
        </button>
      </div>
      ${title ? `<strong>${questionPrefix}${questionStyle === "interview" ? `<span class="bullet-meta-label bullet-inline-label">${labels.question}</span>` : ""}${title}</strong>` : ""}
      ${body ? `<span class="bullet-body">${questionStyle === "interview" ? `<span class="bullet-meta-label">${labels.answer}</span>` : ""}${formatRichText(body)}</span>` : ""}
      ${bullets}
      ${code}
      ${tip}
      ${note}
      ${links}
    </li>
  `;
}

function createSectionBreakHeader(breakConfig, language) {
  const title = text(breakConfig.title, language);
  const description = breakConfig.description ? text(breakConfig.description, language) : "";
  const tone = breakConfig.tone ? ` tone-${escapeHtml(breakConfig.tone)}` : "";
  const chipLabel = escapeHtml(breakConfig.label || "");

  return `
    <div class="bullet-part-header${tone}">
      <span class="bullet-part-chip">${chipLabel}</span>
      <div class="bullet-part-copy">
        <h3>${title}</h3>
        ${description ? `<p>${formatRichText(description)}</p>` : ""}
      </div>
    </div>
  `;
}

function toRomanNumeral(value) {
  const numerals = [
    ["M", 1000],
    ["CM", 900],
    ["D", 500],
    ["CD", 400],
    ["C", 100],
    ["XC", 90],
    ["L", 50],
    ["XL", 40],
    ["X", 10],
    ["IX", 9],
    ["V", 5],
    ["IV", 4],
    ["I", 1]
  ];

  let remaining = Math.max(1, Number(value) || 1);
  let output = "";

  numerals.forEach(([symbol, amount]) => {
    while (remaining >= amount) {
      output += symbol;
      remaining -= amount;
    }
  });

  return output;
}

function createGroupedBulletList(section, language) {
  const items = Array.isArray(section.items) ? section.items : [];
  const questionNumbered = Boolean(section.questionNumbered);
  const questionStyle = section.questionStyle || "";
  const sectionKey = section.anchor || slugify(text(section.heading, "en") || text(section.heading, "vi") || text(section.heading, language) || "section");
  const breaks = Array.isArray(section.breaks)
    ? [...section.breaks]
        .filter((entry) => Number.isInteger(entry.start) && entry.start >= 1 && entry.start <= items.length)
        .sort((left, right) => left.start - right.start)
    : [];

  if (!breaks.length) {
    return `<${questionNumbered ? "ol" : "ul"} class="bullet-list${questionStyle === "interview" ? " interview-qa-list" : ""}">${items.map((item, index) => createBulletItem({ ...item, __sectionKey: sectionKey }, language, index, questionNumbered, questionStyle)).join("")}</${questionNumbered ? "ol" : "ul"}>`;
  }

  const normalizedBreaks = breaks[0]?.start === 1
    ? breaks
    : [{ start: 1, title: { vi: "Phần 1", en: "Part 1" }, tone: "amber" }, ...breaks];

  const groups = normalizedBreaks.map((entry, index) => {
    const startIndex = entry.start - 1;
    const endIndex = (normalizedBreaks[index + 1]?.start || (items.length + 1)) - 1;
    return {
      ...entry,
      label: toRomanNumeral(index + 1),
      items: items.slice(startIndex, endIndex).map((item, itemIndex) => ({
        ...item,
        __sectionKey: sectionKey,
        __globalIndex: startIndex + itemIndex
      }))
    };
  });

  return `
    <div class="bullet-partitions">
      ${groups
        .map(
          (group) => `
            <section class="bullet-partition">
              ${createSectionBreakHeader(group, language)}
              <${questionNumbered ? "ol" : "ul"} class="bullet-list${questionStyle === "interview" ? " interview-qa-list" : ""}">
                ${group.items.map((item) => createBulletItem(item, language, item.__globalIndex ?? 0, questionNumbered, questionStyle)).join("")}
              </${questionNumbered ? "ol" : "ul"}>
            </section>
          `
        )
        .join("")}
    </div>
  `;
}

function createContentCard(item, language) {
  return `
    <article class="content-card">
      <h3>${text(item.title, language)}</h3>
      <p>${text(item.body, language)}</p>
    </article>
  `;
}

function createLinkCard(item, language) {
  const href = resolveConfiguredHref(item.href);
  const label = text(item.label, language);
  const description = text(item.description, language);
  return `
    <article class="link-card">
      <h3>${label}</h3>
      <p>${description}</p>
      <p class="card-action"><a class="text-link" href="${escapeHtml(href)}" target="_blank" rel="noreferrer">${text(item.action, language)}</a></p>
    </article>
  `;
}

function createWorkbenchCard(item, language) {
  const href = resolveConfiguredHref(item.href);
  const files = Array.isArray(item.files) ? item.files : [];
  const commands = Array.isArray(item.commands) ? item.commands : [];
  const concepts = Array.isArray(item.concepts) ? item.concepts : [];

  return `
    <article class="content-card workbench-card">
      <div class="meta">
        <span class="badge">${text(item.track, language)}</span>
      </div>
      <h3>${text(item.title, language)}</h3>
      <p>${formatRichText(text(item.body, language))}</p>
      ${concepts.length ? `
        <p class="bullet-meta-label">${language === "vi" ? "Khái niệm" : "Concepts"}</p>
        <ul class="bullet-sublist">
          ${concepts.map((concept) => `<li>${formatRichText(text(concept, language))}</li>`).join("")}
        </ul>
      ` : ""}
      ${files.length ? `
        <p class="bullet-meta-label">${language === "vi" ? "Đọc code ở" : "Read code in"}</p>
        <ul class="bullet-sublist">
          ${files.map((file) => `<li><code>${escapeHtml(file)}</code></li>`).join("")}
        </ul>
      ` : ""}
      ${commands.length ? `
        <div class="bullet-code-shell">
          <pre><code>${escapeHtml(commands.join("\n"))}</code></pre>
        </div>
      ` : ""}
      <p class="card-action">
        <a class="text-link" href="${escapeHtml(href)}" target="_blank" rel="noreferrer">${text(item.action, language)}</a>
      </p>
    </article>
  `;
}

function createMindmapSection(section, language) {
  const createReferences = (references = []) => {
    if (!Array.isArray(references) || !references.length) {
      return "";
    }

    return `
      <div class="roadmap-reference-list">
        ${references
          .map(
            (reference) => `
              <a class="roadmap-reference-link" href="${escapeHtml(resolveConfiguredHref(reference.href))}" target="_blank" rel="noreferrer">
                ${text(reference.label, language)}
              </a>
            `
          )
          .join("")}
      </div>
    `;
  };

  const legend = Array.isArray(section.legend) && section.legend.length
    ? `
      <div class="roadmap-legend">
        ${section.legend
          .map(
            (item) => `
              <div class="roadmap-legend-item tone-${escapeHtml(item.tone || "grow")}">
                <span class="roadmap-legend-dot" aria-hidden="true"></span>
                <span>${text(item.label, language)}</span>
              </div>
            `
          )
          .join("")}
      </div>
    `
    : "";

  const branches = Array.isArray(section.branches) ? section.branches : [];
  const overview = branches.length
    ? `
      <div class="roadmap-overview">
        <div class="roadmap-overview-head">
          <h3>${language === "vi" ? "Đi nhanh tới đúng nhánh" : "Jump to the right branch"}</h3>
          <p>${language === "vi" ? "Chọn đúng nhánh để xem đúng vị trí trong cây thay vì phải kéo toàn bộ trang." : "Use these anchors to jump to the exact branch instead of scanning the whole tree."}</p>
        </div>
        <div class="roadmap-overview-grid">
          ${branches
            .map((branch) => {
              const branchId = `roadmap-${slugify(branch.step || branch.title)}`;
              return `
                <a class="roadmap-overview-link tone-${escapeHtml(branch.tone || "grow")}" href="#${branchId}">
                  <span class="roadmap-overview-step">${escapeHtml(branch.step || "")}</span>
                  <span class="roadmap-overview-copy">
                    <strong>${text(branch.title, language)}</strong>
                    <span>${text(branch.badge, language)}</span>
                  </span>
                </a>
              `;
            })
            .join("")}
        </div>
      </div>
    `
    : "";
  const filters = branches.length
    ? `
      <div class="roadmap-filter-bar">
        <label class="roadmap-filter-search">
          <span class="sr-only">${language === "vi" ? "Tìm nhánh trong cây trí tuệ" : "Search branches in the learning tree"}</span>
          <input
            type="search"
            id="roadmapFilterSearch"
            placeholder="${language === "vi" ? "Tìm nhánh, chủ đề, ví dụ: API, Docker, session..." : "Search branches or topics, for example API, Docker, session..."}"
          />
        </label>
        <div class="roadmap-filter-tones" aria-label="${language === "vi" ? "Lọc theo mức độ ưu tiên" : "Filter by priority"}">
          <button type="button" class="roadmap-filter-tone active" data-tone="all" aria-pressed="true">${language === "vi" ? "Tất cả" : "All"}</button>
          <button type="button" class="roadmap-filter-tone tone-core" data-tone="core" aria-pressed="false">${language === "vi" ? "Học trước" : "Learn first"}</button>
          <button type="button" class="roadmap-filter-tone tone-grow" data-tone="grow" aria-pressed="false">${language === "vi" ? "Học tiếp" : "Grow next"}</button>
          <button type="button" class="roadmap-filter-tone tone-flex" data-tone="flex" aria-pressed="false">${language === "vi" ? "Khi cần" : "When needed"}</button>
        </div>
        <div class="roadmap-filter-actions">
          <div class="roadmap-progress">
            <div class="roadmap-progress-head">
              <span>${language === "vi" ? "Tiến độ cây trí tuệ" : "Learning tree progress"}</span>
              <strong id="roadmapProgressValue">0/${branches.length} · 0%</strong>
            </div>
            <div class="roadmap-progress-track">
              <span class="roadmap-progress-fill" id="roadmapProgressFill"></span>
            </div>
          </div>
          <p class="roadmap-filter-summary" id="roadmapFilterSummary"></p>
          <div class="roadmap-filter-action-row">
            <button type="button" class="roadmap-filter-tone roadmap-study-toggle" id="roadmapStudyMode" aria-pressed="false">
              ${language === "vi" ? "Chế độ học" : "Study mode"}
            </button>
            <button type="button" class="roadmap-filter-clear" id="roadmapFilterClear">${language === "vi" ? "Xóa lọc" : "Clear filters"}</button>
          </div>
        </div>
      </div>
    `
    : "";
  const cta = section.cta
    ? `
      <article class="roadmap-cta">
        <div class="roadmap-cta-copy">
          <p class="roadmap-cta-eyebrow">${text(section.cta.eyebrow, language)}</p>
          <h3>${text(section.cta.title, language)}</h3>
          <p>${text(section.cta.body, language)}</p>
        </div>
        <a class="btn" href="${escapeHtml(resolveConfiguredHref(section.cta.href))}">${text(section.cta.action, language)}</a>
      </article>
    `
    : "";

  return `
    <section class="panel detail-section roadmap-tree-panel">
      <div class="section-heading">
        <h2>${text(section.heading, language)}</h2>
        <p>${text(section.intro, language)}</p>
      </div>
      ${legend}
      ${overview}
      ${filters}
      <div class="roadmap-tree">
        <article class="roadmap-tree-root">
          <p class="roadmap-tree-root-eyebrow">${text(section.root.eyebrow, language)}</p>
          <h3>${text(section.root.title, language)}</h3>
          <p>${text(section.root.summary, language)}</p>
        </article>
        <ol class="roadmap-tree-track">
          ${branches
            .map(
              (branch, index) => {
                const branchId = `roadmap-${slugify(branch.step || branch.title)}`;
                return `
                <li class="roadmap-tree-node side-${escapeHtml(branch.side || (index % 2 === 0 ? "left" : "right"))}" id="${branchId}">
                  <div class="roadmap-tree-spine" aria-hidden="true">
                    <span class="roadmap-tree-dot tone-${escapeHtml(branch.tone || "grow")}"></span>
                  </div>
                  <article class="roadmap-node-card tone-${escapeHtml(branch.tone || "grow")}">
                    <div class="roadmap-node-head">
                      <div class="roadmap-node-heading">
                        <span class="roadmap-node-icon" aria-hidden="true">${getRoadmapBranchIcon(branch)}</span>
                        <span class="roadmap-node-step">${escapeHtml(branch.step || "")}</span>
                        <span class="roadmap-node-badge">${text(branch.badge, language)}</span>
                      </div>
                      <button type="button" class="roadmap-collapse-toggle" aria-pressed="false">
                        ${language === "vi" ? "Thu gọn nhánh" : "Collapse branch"}
                      </button>
                    </div>
                    <div class="roadmap-node-tools">
                      <button type="button" class="roadmap-progress-toggle" aria-pressed="false">
                        ${language === "vi" ? "Đánh dấu đang học" : "Mark as learning"}
                      </button>
                    </div>
                    <div class="roadmap-node-body">
                      <h3>${text(branch.title, language)}</h3>
                      <p>${text(branch.summary, language)}</p>
                      <div class="roadmap-chip-list">
                        ${(branch.topics || [])
                          .map((topic) => `<span class="roadmap-chip">${text(topic, language)}</span>`)
                          .join("")}
                      </div>
                      ${Array.isArray(branch.subbranches) && branch.subbranches.length
                        ? `
                          <div class="roadmap-subbranch-list">
                            ${branch.subbranches
                              .map(
                                (subbranch) => `
                                  <section class="roadmap-subbranch">
                                    <h4>${text(subbranch.title, language)}</h4>
                                    <ul>
                                      ${(subbranch.items || [])
                                        .map((item) => `<li>${text(item, language)}</li>`)
                                        .join("")}
                                    </ul>
                                  </section>
                                `
                              )
                              .join("")}
                          </div>
                        `
                        : ""}
                      ${branch.references?.length
                        ? `
                          <div class="roadmap-reference-shell">
                            <div class="roadmap-reference-head">
                              <span>${language === "vi" ? "Nguồn nên đọc" : "Worthwhile references"}</span>
                            </div>
                            ${createReferences(branch.references)}
                          </div>
                        `
                        : ""}
                      ${branch.note ? `<p class="roadmap-node-note">${text(branch.note, language)}</p>` : ""}
                    </div>
                  </article>
                </li>
              `;
              }
            )
            .join("")}
        </ol>
        ${cta}
      </div>
    </section>
  `;
}

function createLevelNavCard(level, href, active = false) {
  return `
    <li class="php-roadmap-item">
      <a class="php-roadmap-link${active ? " active" : ""}" href="${href}">
        <span class="level-tag">${level.badge}</span>
        <span class="php-roadmap-copy">
          <strong class="php-roadmap-title">${level.title}</strong>
          <span>${level.summary}</span>
        </span>
      </a>
    </li>
  `;
}

function createLaravelTopicNavCard(topic, language, active = false) {
  const badge = String(topic.index).padStart(2, "0");
  return `
    <li class="php-roadmap-item">
      <a class="php-roadmap-link${active ? " active" : ""}" href="${topic.href}">
        <span class="level-tag">${badge}</span>
        <span class="php-roadmap-copy">
          <strong class="php-roadmap-title">${text(topic.label, language)}</strong>
          <span>${text(topic.desc, language)}</span>
        </span>
      </a>
    </li>
  `;
}

function createLaravelTopicSwitcher(topics, currentPageKey, language) {
  const currentIndex = topics.findIndex((topic) => topic.pageKey === currentPageKey);
  const currentTopic = currentIndex >= 0 ? topics[currentIndex] : topics[0];
  const prevTopic = currentIndex > 0 ? topics[currentIndex - 1] : null;
  const nextTopic = currentIndex >= 0 && currentIndex < topics.length - 1 ? topics[currentIndex + 1] : null;
  const labels = {
    en: {
      title: "Jump to another Laravel topic",
      prev: "Previous",
      next: "Next",
      select: "Choose a Laravel topic"
    },
    vi: {
      title: "Chuyển nhanh sang topic Laravel khác",
      prev: "Mục trước",
      next: "Mục sau",
      select: "Chọn một topic Laravel"
    }
  }[language];

  return `
    <div class="laravel-topic-switcher">
      <div class="laravel-topic-switcher-head">
        <div class="laravel-topic-switcher-copy">
          <span class="level-tag">${String(currentTopic?.index || 1).padStart(2, "0")}</span>
          <div>
            <strong>${currentTopic ? text(currentTopic.label, language) : ""}</strong>
            <span>${currentTopic ? text(currentTopic.desc, language) : ""}</span>
          </div>
        </div>
        <label class="php-level-switcher-label laravel-topic-switcher-label">
          <span>${labels.select}</span>
          <select class="php-level-switcher-select" id="laravelTopicSelect">
            ${topics
              .map(
                (topic) => `
                  <option value="${topic.pageKey}" data-href="${topic.href}"${topic.pageKey === currentPageKey ? " selected" : ""}>
                    ${String(topic.index).padStart(2, "0")} · ${escapeHtml(text(topic.label, language))}
                  </option>
                `
              )
              .join("")}
          </select>
        </label>
      </div>
      <div class="laravel-topic-switcher-actions">
        ${prevTopic ? `<a class="php-resume-link laravel-topic-switcher-link" href="${prevTopic.href}">${labels.prev}</a>` : `<span class="laravel-topic-switcher-link disabled">${labels.prev}</span>`}
        ${nextTopic ? `<a class="php-resume-link laravel-topic-switcher-link" href="${nextTopic.href}">${labels.next}</a>` : `<span class="laravel-topic-switcher-link disabled">${labels.next}</span>`}
      </div>
    </div>
  `;
}

function saveLastPhpLevel(pageKey) {
  if (!pageKey.startsWith("php-")) {
    return;
  }

  localStorage.setItem(PHP_LAST_LEVEL_KEY, pageKey);
}

function getLastPhpLevel() {
  const saved = localStorage.getItem(PHP_LAST_LEVEL_KEY);
  return ["php-starter", "php-intermediate", "php-advanced"].includes(saved) ? saved : "";
}

function createPhpResumePrompt(language, levels) {
  const lastLevelKey = getLastPhpLevel();
  if (!lastLevelKey) {
    return "";
  }

  const levelKey = lastLevelKey.replace("php-", "");
  const level = levels.find((item) => item.key === levelKey);
  if (!level) {
    return "";
  }

  const copy = {
    en: {
      eyebrow: "Resume from where you stopped",
      title: "Continue your latest PHP stage",
      action: "Open this stage"
    },
    vi: {
      eyebrow: "Học tiếp đúng chặng đang dừng",
      title: "Mở lại chặng PHP gần nhất",
      action: "Tiếp tục học"
    }
  }[language];

  return `
    <div class="php-resume-card">
      <div class="php-resume-copy">
        <p class="php-resume-eyebrow">${copy.eyebrow}</p>
        <p class="php-resume-title">${copy.title}: <strong>${level.title}</strong></p>
      </div>
      <a class="php-resume-link" href="${PHP_LEVEL_PAGE_MAP[level.key]}">${copy.action}</a>
    </div>
  `;
}

function saveLastLaravelTopic(pageKey) {
  if (!pageKey.startsWith("laravel-")) {
    return;
  }
  localStorage.setItem(LARAVEL_LAST_TOPIC_KEY, pageKey);
}

function getLastLaravelTopic() {
  const saved = localStorage.getItem(LARAVEL_LAST_TOPIC_KEY) || "";
  return saved.startsWith("laravel-") ? saved : "";
}

function createLaravelResumePrompt(language, topics) {
  const lastTopicPage = getLastLaravelTopic();
  if (!lastTopicPage) {
    return "";
  }

  const topic = topics.find((item) => item.pageKey === lastTopicPage);
  if (!topic) {
    return "";
  }

  const copy = {
    en: {
      eyebrow: "Resume from where you stopped",
      title: "Continue your latest Laravel topic",
      action: "Open this topic"
    },
    vi: {
      eyebrow: "Học tiếp đúng mục đang dừng",
      title: "Mở lại mục Laravel gần nhất",
      action: "Tiếp tục học"
    }
  }[language];

  return `
    <div class="php-resume-card">
      <div class="php-resume-copy">
        <p class="php-resume-eyebrow">${copy.eyebrow}</p>
        <p class="php-resume-title">${copy.title}: <strong>${text(topic.label, language)}</strong></p>
      </div>
      <a class="php-resume-link" href="${topic.href}">${copy.action}</a>
    </div>
  `;
}

function bindLaravelTopicSwitcher() {
  const select = document.getElementById("laravelTopicSelect");
  if (!select) {
    return;
  }

  select.onchange = () => {
    const option = select.options[select.selectedIndex];
    const href = option?.dataset.href;
    if (href) {
      window.location.href = href;
    }
  };
}

function createInterviewTopicNavCard(topic, language, active = false) {
  const badge = String(topic.index).padStart(2, "0");
  return `
    <li class="php-roadmap-item">
      <a class="php-roadmap-link${active ? " active" : ""}" href="${topic.href}">
        <span class="level-tag">${badge}</span>
        <span class="php-roadmap-copy">
          <strong class="php-roadmap-title">${text(topic.label, language)}</strong>
          <span>${text(topic.desc, language)}</span>
        </span>
      </a>
    </li>
  `;
}

function createInterviewTopicSwitcher(topics, currentPageKey, language) {
  const currentIndex = topics.findIndex((topic) => topic.pageKey === currentPageKey);
  const currentTopic = currentIndex >= 0 ? topics[currentIndex] : topics[0];
  const prevTopic = currentIndex > 0 ? topics[currentIndex - 1] : null;
  const nextTopic = currentIndex >= 0 && currentIndex < topics.length - 1 ? topics[currentIndex + 1] : null;
  const labels = {
    en: {
      prev: "Previous",
      next: "Next",
      select: "Choose an interview topic"
    },
    vi: {
      prev: "Mục trước",
      next: "Mục sau",
      select: "Chọn một topic phỏng vấn"
    }
  }[language];

  return `
    <div class="laravel-topic-switcher">
      <div class="laravel-topic-switcher-head">
        <div class="laravel-topic-switcher-copy">
          <span class="level-tag">${String(currentTopic?.index || 1).padStart(2, "0")}</span>
          <div>
            <strong>${currentTopic ? text(currentTopic.label, language) : ""}</strong>
            <span>${currentTopic ? text(currentTopic.desc, language) : ""}</span>
          </div>
        </div>
        <label class="php-level-switcher-label laravel-topic-switcher-label">
          <span>${labels.select}</span>
          <select class="php-level-switcher-select" id="interviewTopicSelect">
            ${topics
              .map(
                (topic) => `
                  <option value="${topic.pageKey}" data-href="${topic.href}"${topic.pageKey === currentPageKey ? " selected" : ""}>
                    ${String(topic.index).padStart(2, "0")} · ${escapeHtml(text(topic.label, language))}
                  </option>
                `
              )
              .join("")}
          </select>
        </label>
      </div>
      <div class="laravel-topic-switcher-actions">
        ${prevTopic ? `<a class="php-resume-link laravel-topic-switcher-link" href="${prevTopic.href}">${labels.prev}</a>` : `<span class="laravel-topic-switcher-link disabled">${labels.prev}</span>`}
        ${nextTopic ? `<a class="php-resume-link laravel-topic-switcher-link" href="${nextTopic.href}">${labels.next}</a>` : `<span class="laravel-topic-switcher-link disabled">${labels.next}</span>`}
      </div>
    </div>
  `;
}

function saveLastInterviewTopic(pageKey) {
  if (!pageKey.startsWith("interview-")) {
    return;
  }
  localStorage.setItem(INTERVIEW_LAST_TOPIC_KEY, pageKey);
}

function getLastInterviewTopic() {
  const saved = localStorage.getItem(INTERVIEW_LAST_TOPIC_KEY) || "";
  return saved.startsWith("interview-") ? saved : "";
}

function createInterviewResumePrompt(language, topics) {
  const lastTopicPage = getLastInterviewTopic();
  if (!lastTopicPage) {
    return "";
  }

  const topic = topics.find((item) => item.pageKey === lastTopicPage);
  if (!topic) {
    return "";
  }

  const copy = {
    en: {
      eyebrow: "Resume from where you stopped",
      title: "Continue your latest interview topic",
      action: "Open this topic"
    },
    vi: {
      eyebrow: "Học tiếp đúng mục đang dừng",
      title: "Mở lại mục phỏng vấn gần nhất",
      action: "Tiếp tục học"
    }
  }[language];

  return `
    <div class="php-resume-card">
      <div class="php-resume-copy">
        <p class="php-resume-eyebrow">${copy.eyebrow}</p>
        <p class="php-resume-title">${copy.title}: <strong>${text(topic.label, language)}</strong></p>
      </div>
      <a class="php-resume-link" href="${topic.href}">${copy.action}</a>
    </div>
  `;
}

function createVideoResumePrompt(language, topics) {
  const lastTopicPage = getLastVideoTopic();
  if (!lastTopicPage) {
    return "";
  }

  const topic = topics.find((item) => item.pageKey === lastTopicPage);
  if (!topic) {
    return "";
  }

  const copy = {
    en: {
      eyebrow: "Resume from where you stopped",
      title: "Continue your latest Vibe Coding topic",
      action: "Open this topic"
    },
    vi: {
      eyebrow: "Học tiếp đúng mục đang dừng",
      title: "Mở lại mục Vibe Coding gần nhất",
      action: "Tiếp tục học"
    }
  }[language];

  return `
    <div class="php-resume-card">
      <div class="php-resume-copy">
        <p class="php-resume-eyebrow">${copy.eyebrow}</p>
        <p class="php-resume-title">${copy.title}: <strong>${text(topic.label, language)}</strong></p>
      </div>
      <a class="php-resume-link" href="${topic.href}">${copy.action}</a>
    </div>
  `;
}

function bindInterviewTopicSwitcher() {
  const select = document.getElementById("interviewTopicSelect");
  if (!select) {
    return;
  }

  select.onchange = () => {
    const option = select.options[select.selectedIndex];
    const href = option?.dataset.href;
    if (href) {
      window.location.href = href;
    }
  };
}

function createVideoTopicNavCard(topic, language, active = false) {
  const badge = String(topic.index).padStart(2, "0");
  return `
    <li class="php-roadmap-item">
      <a class="php-roadmap-link${active ? " active" : ""}" href="${topic.href}">
        <span class="level-tag">${badge}</span>
        <span class="php-roadmap-copy">
          <strong class="php-roadmap-title">${text(topic.label, language)}</strong>
          <span>${text(topic.desc, language)}</span>
        </span>
      </a>
    </li>
  `;
}

function createVideoTopicSwitcher(topics, currentPageKey, language) {
  const currentIndex = topics.findIndex((topic) => topic.pageKey === currentPageKey);
  const currentTopic = currentIndex >= 0 ? topics[currentIndex] : topics[0];
  const prevTopic = currentIndex > 0 ? topics[currentIndex - 1] : null;
  const nextTopic = currentIndex >= 0 && currentIndex < topics.length - 1 ? topics[currentIndex + 1] : null;
  const labels = {
    en: {
      prev: "Previous",
      next: "Next",
      select: "Choose a Vibe Coding topic"
    },
    vi: {
      prev: "Mục trước",
      next: "Mục sau",
      select: "Chọn một topic Vibe Coding"
    }
  }[language];

  return `
    <div class="laravel-topic-switcher">
      <div class="laravel-topic-switcher-head">
        <div class="laravel-topic-switcher-copy">
          <span class="level-tag">${String(currentTopic?.index || 1).padStart(2, "0")}</span>
          <div>
            <strong>${currentTopic ? text(currentTopic.label, language) : ""}</strong>
            <span>${currentTopic ? text(currentTopic.desc, language) : ""}</span>
          </div>
        </div>
        <label class="php-level-switcher-label laravel-topic-switcher-label">
          <span>${labels.select}</span>
          <select class="php-level-switcher-select" id="videoTopicSelect">
            ${topics
              .map(
                (topic) => `
                  <option value="${topic.pageKey}" data-href="${topic.href}"${topic.pageKey === currentPageKey ? " selected" : ""}>
                    ${String(topic.index).padStart(2, "0")} · ${escapeHtml(text(topic.label, language))}
                  </option>
                `
              )
              .join("")}
          </select>
        </label>
      </div>
      <div class="laravel-topic-switcher-actions">
        ${prevTopic ? `<a class="php-resume-link laravel-topic-switcher-link" href="${prevTopic.href}">${labels.prev}</a>` : `<span class="laravel-topic-switcher-link disabled">${labels.prev}</span>`}
        ${nextTopic ? `<a class="php-resume-link laravel-topic-switcher-link" href="${nextTopic.href}">${labels.next}</a>` : `<span class="laravel-topic-switcher-link disabled">${labels.next}</span>`}
      </div>
    </div>
  `;
}

function saveLastVideoTopic(pageKey) {
  if (
    !pageKey
    || pageKey === "vibe-coding"
    || pageKey === "videos"
    || (!pageKey.startsWith("vibe-") && !pageKey.startsWith("video-"))
  ) {
    return;
  }
  localStorage.setItem(VIBE_CODING_LAST_TOPIC_KEY, pageKey);
}

function getLastVideoTopic() {
  const saved = localStorage.getItem(VIBE_CODING_LAST_TOPIC_KEY)
    || localStorage.getItem("laravel-labs-video-last-topic")
    || "";

  if (saved.startsWith("vibe-")) {
    return saved;
  }

  return {
    "video-foundations": "vibe-prompting",
    "video-laravel-builds": "vibe-ai-crud",
    "video-debug-refactor": "vibe-ai-review",
    "video-devops-runtime": "vibe-ai-runtime"
  }[saved] || "";
}

function bindVideoTopicSwitcher() {
  const select = document.getElementById("videoTopicSelect");
  if (!select) {
    return;
  }

  select.onchange = () => {
    const option = select.options[select.selectedIndex];
    const href = option?.dataset.href;
    if (href) {
      window.location.href = href;
    }
  };
}

function createPhpModuleCard(module) {
  const moduleId = module.id || "";
  const bullets = Array.isArray(module.bullets)
    ? `<ul class="php-note-points">${module.bullets.map((item) => `<li>${formatRichText(item)}</li>`).join("")}</ul>`
    : "";

  return `
    <article class="php-note-block"${moduleId ? ` id="${moduleId}"` : ""}>
      <h3>${module.title}</h3>
      <p class="php-note-copy">${formatRichText(module.description)}</p>
      ${bullets}
    </article>
  `;
}

function createSnippetCard(example, language) {
  const exampleId = example.id || "";
  const ui = PHP_LEVEL_UI[language];
  return `
    <article class="php-example-block"${exampleId ? ` id="${exampleId}"` : ""}>
      <div class="php-example-head">
        <h3>${example.title}</h3>
        <button type="button" class="php-code-toggle" data-expanded="true">
          ${ui.collapseCode}
        </button>
      </div>
      <p class="php-note-copy">${formatRichText(example.description)}</p>
      <div class="php-code-shell">
        <button
          type="button"
          class="bullet-code-copy"
          data-copy-label="${ui.copyCode}"
          data-copied-label="${ui.copiedCode}"
        >${ui.copyCode}</button>
        <pre><code>${escapeHtml(example.code)}</code></pre>
      </div>
    </article>
  `;
}

function createQuestionCard(item) {
  const questionId = item.id || "";
  return `
    <article class="php-question-block"${questionId ? ` id="${questionId}"` : ""}>
      <span class="level-tag">${item.tag}</span>
      <h3>${item.question}</h3>
      <p class="qa-answer">${formatRichText(item.answer)}</p>
    </article>
  `;
}

function createPhaseTopic(item) {
  const topicId = item.id || "";
  return `
    <li class="php-phase-topic"${topicId ? ` id="${topicId}"` : ""}>
      <strong>${item.term}</strong>
      <span>${formatRichText(item.body)}</span>
      ${item.note ? `<em class="php-note-inline">${formatRichText(item.note)}</em>` : ""}
    </li>
  `;
}

function createPhase(section, language) {
  const sectionId = section.id || "";
  const examples = Array.isArray(section.examples) && section.examples.length
    ? `
      <div class="php-examples-stack">
        ${section.examples.map((example) => createSnippetCard(example, language)).join("")}
      </div>
    `
    : "";

  return `
    <section class="php-phase"${sectionId ? ` id="${sectionId}"` : ""}>
      <div class="php-phase-head">
        <span class="level-tag">${section.badge}</span>
        <h3>${section.title}</h3>
      </div>
      <p class="php-note-copy">${formatRichText(section.intro)}</p>
      <ul class="php-phase-topics">
        ${section.topics.map((item) => createPhaseTopic(item)).join("")}
      </ul>
      ${examples}
    </section>
  `;
}

function withPhpIds(levels) {
  return levels.map((level) => {
    const levelId = level.anchor;
    return {
      ...level,
      modules: (level.modules || []).map((module, moduleIndex) => ({
        ...module,
        id: `${levelId}-module-${moduleIndex + 1}`
      })),
      examples: (level.examples || []).map((example, exampleIndex) => ({
        ...example,
        id: `${levelId}-example-${exampleIndex + 1}`
      })),
      questions: (level.questions || []).map((item, questionIndex) => ({
        ...item,
        id: `${levelId}-question-${questionIndex + 1}`
      })),
      phases: (level.phases || []).map((phase, phaseIndex) => {
        const phaseId = `${levelId}-phase-${phaseIndex + 1}`;
        return {
          ...phase,
          id: phaseId,
          topics: (phase.topics || []).map((topic, topicIndex) => ({
            ...topic,
            id: `${phaseId}-topic-${topicIndex + 1}`
          })),
          examples: (phase.examples || []).map((example, exampleIndex) => ({
            ...example,
            id: `${phaseId}-example-${exampleIndex + 1}`
          }))
        };
      })
    };
  });
}

function renderLanding(page, language) {
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("landingTitle").textContent = text(page.title, language);
  document.getElementById("landingSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("landingSectionTitle").textContent = text(page.sectionTitle, language);
  document.getElementById("landingGrid").innerHTML = page.cards.map((item) => createCard(item, language)).join("");
  document.getElementById("repoSectionTitle").textContent = text(page.repoSectionTitle, language);
  document.getElementById("repoSectionSubtitle").textContent = text(page.repoSectionSubtitle, language);
  document.getElementById("repoGrid").innerHTML = page.repoCards.map((item) => createRepoCard(item, language)).join("");
}

function renderSection(section, language) {
  const heading = text(section.heading, language);
  const intro = text(section.intro, language);
  const sectionId = section.anchor ? ` id="${escapeHtml(section.anchor)}"` : "";
  const sectionKey = section.anchor || slugify(heading || "section");

  if (section.type === "mindmap") {
    return createMindmapSection(section, language);
  }

  if (section.type === "list") {
    return `
      <section class="panel detail-section" data-progress-scope="${sectionKey}"${sectionId}>
        <div class="detail-section-head">
          <div class="detail-section-copy">
            <h2>${heading}</h2>
            <p class="section-copy">${intro}</p>
          </div>
          <div class="section-progress-pill" data-section-progress="${sectionKey}">
            <strong>0 / 0</strong>
          </div>
        </div>
        ${createGroupedBulletList(section, language)}
      </section>
    `;
  }

  if (section.type === "links") {
    return `
      <section class="panel detail-section"${sectionId}>
        <h2>${heading}</h2>
        <p class="section-copy">${intro}</p>
        <div class="cards content-grid">
          ${section.items.map((item) => createLinkCard(item, language)).join("")}
        </div>
      </section>
    `;
  }

  if (section.type === "workbenches") {
    return `
      <section class="panel detail-section"${sectionId}>
        <h2>${heading}</h2>
        <p class="section-copy">${intro}</p>
        <div class="cards content-grid">
          ${section.items.map((item) => createWorkbenchCard(item, language)).join("")}
        </div>
      </section>
    `;
  }

  return `
    <section class="panel detail-section"${sectionId}>
      <h2>${heading}</h2>
      <p class="section-copy">${intro}</p>
      <div class="cards content-grid">
        ${section.items.map((item) => createContentCard(item, language)).join("")}
      </div>
    </section>
  `;
}

function renderDetail(page, language, common) {
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = text(page.title, language);
  document.getElementById("pageSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("pageSections").innerHTML = page.sections.map((section) => renderSection(section, language)).join("");
  bindRoadmapFilters(language);
}

function renderLaravel(page, language, common, sections) {
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = text(page.title, language);
  document.getElementById("pageSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("pageSections").innerHTML = sections.map((section) => renderSection(section, language)).join("");
}

function renderLaravelOverview(page, language, common) {
  const topics = getLaravelHubTopics(page);
  const resumeHost = document.getElementById("laravelResumePrompt");
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = text(page.title, language);
  document.getElementById("pageSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("laravelJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("laravelJourneySubtitle").textContent = text(page.journeySubtitle, language);
  if (resumeHost) {
    resumeHost.innerHTML = createLaravelResumePrompt(language, topics);
  }
  document.getElementById("laravelTopicNav").innerHTML = `
    <ol class="php-roadmap">
      ${topics.map((topic) => createLaravelTopicNavCard(topic, language, false)).join("")}
    </ol>
  `;
  document.getElementById("pageSections").innerHTML = "";
}

function renderLaravelTopic(page, section, language, common, pageKey) {
  const topics = getLaravelHubTopics(page);
  const normalizedSection = { anchor: pageKey, ...section };
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = normalizedSection.heading;
  document.getElementById("pageSubtitle").textContent = normalizedSection.intro || text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("laravelJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("laravelJourneySubtitle").textContent = text(page.journeySubtitle, language);
  const resumeHost = document.getElementById("laravelResumePrompt");
  if (resumeHost) {
    resumeHost.innerHTML = "";
  }
  document.getElementById("laravelTopicNav").innerHTML = createLaravelTopicSwitcher(topics, pageKey, language);
  bindLaravelTopicSwitcher();
  document.getElementById("pageSections").innerHTML = renderSection(normalizedSection, language);
  saveLastLaravelTopic(pageKey);
}

function renderInterviewOverview(page, language, common) {
  const topics = getInterviewTopics(page);
  const resumeHost = document.getElementById("laravelResumePrompt");
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = text(page.title, language);
  document.getElementById("pageSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("laravelJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("laravelJourneySubtitle").textContent = text(page.journeySubtitle, language);
  if (resumeHost) {
    resumeHost.innerHTML = createInterviewResumePrompt(language, topics);
  }
  document.getElementById("laravelTopicNav").innerHTML = `
    <ol class="php-roadmap">
      ${topics.map((topic) => createInterviewTopicNavCard(topic, language, false)).join("")}
    </ol>
  `;
  document.getElementById("pageSections").innerHTML = page.sections.map((section) => renderSection(section, language)).join("");
}

function renderInterviewTopic(page, section, language, common, pageKey) {
  const topics = getInterviewTopics(page);
  const sectionKey = pageKey;
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = section.heading;
  document.getElementById("pageSubtitle").textContent = section.intro || text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("laravelJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("laravelJourneySubtitle").textContent = text(page.journeySubtitle, language);
  const resumeHost = document.getElementById("laravelResumePrompt");
  if (resumeHost) {
    resumeHost.innerHTML = "";
  }
  document.getElementById("laravelTopicNav").innerHTML = createInterviewTopicSwitcher(topics, pageKey, language);
  bindInterviewTopicSwitcher();
  const normalizedSection = {
    type: "list",
    anchor: pageKey,
    ...section
  };
  document.getElementById("pageSections").innerHTML = `
    <section class="panel detail-section interview-topic-section" data-progress-scope="${sectionKey}">
      <div class="detail-section-head">
        <div class="detail-section-copy">
          <h2>${section.heading}</h2>
          <p class="section-copy">${section.intro || text(page.subtitle, language)}</p>
        </div>
        <div class="section-progress-pill" data-section-progress="${sectionKey}">
          <strong>0 / 0</strong>
        </div>
      </div>
      ${createGroupedBulletList(normalizedSection, language)}
    </section>
  `;
  saveLastInterviewTopic(pageKey);
}

function renderVideoOverview(page, language, common) {
  const topics = getVideoTopics(page);
  const resumeHost = document.getElementById("laravelResumePrompt");
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = text(page.title, language);
  document.getElementById("pageSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("laravelJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("laravelJourneySubtitle").textContent = text(page.journeySubtitle, language);
  if (resumeHost) {
    resumeHost.innerHTML = createVideoResumePrompt(language, topics);
  }
  document.getElementById("laravelTopicNav").innerHTML = `
    <ol class="php-roadmap">
      ${topics.map((topic) => createVideoTopicNavCard(topic, language, false)).join("")}
    </ol>
  `;
  document.getElementById("pageSections").innerHTML = page.sections.map((section) => renderSection(section, language)).join("");
}

function renderVideoTopic(page, section, language, common, pageKey) {
  const topics = getVideoTopics(page);
  const normalizedSection = { anchor: pageKey, ...section };
  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = normalizedSection.heading;
  document.getElementById("pageSubtitle").textContent = normalizedSection.intro || text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("laravelJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("laravelJourneySubtitle").textContent = text(page.journeySubtitle, language);
  const resumeHost = document.getElementById("laravelResumePrompt");
  if (resumeHost) {
    resumeHost.innerHTML = "";
  }
  document.getElementById("laravelTopicNav").innerHTML = createVideoTopicSwitcher(topics, pageKey, language);
  bindVideoTopicSwitcher();
  document.getElementById("pageSections").innerHTML = renderSection(normalizedSection, language);
  saveLastVideoTopic(pageKey);
}

function renderPhpLevel(level, language) {
  const hasPhases = Array.isArray(level.phases) && level.phases.length;
  const highlights = Array.isArray(level.highlights)
    ? `<ul class="bullet-list">${level.highlights.map((item) => `<li>${item}</li>`).join("")}</ul>`
    : "";

  const phases = hasPhases
    ? `
      <div class="php-phase-stack">
        ${level.phases.map((section) => createPhase(section, language)).join("")}
      </div>
    `
    : "";

  const modules = !hasPhases && Array.isArray(level.modules)
    ? `
      <div class="php-notes-stack">
        ${level.modules.map((module) => createPhpModuleCard(module)).join("")}
      </div>
    `
    : "";

  const examples = !hasPhases && Array.isArray(level.examples) && level.examples.length
    ? `
      <div class="detail-section" id="${level.anchor}-examples">
        <h3>${level.examplesTitle}</h3>
        <div class="php-examples-stack">
          ${level.examples.map((example) => createSnippetCard(example, language)).join("")}
        </div>
      </div>
    `
    : "";

  const questions = Array.isArray(level.questions) && level.questions.length
    ? `
      <div class="detail-section" id="${level.anchor}-questions">
        <h3>${level.questionsTitle}</h3>
        <div class="php-questions-stack">
          ${level.questions.map((item) => createQuestionCard(item)).join("")}
        </div>
      </div>
    `
    : "";

  return `
    <section class="panel level-shell" id="${level.anchor}">
      <a class="level-anchor" href="#${level.anchor}">
        <span class="level-tag">${level.badge}</span>
        <h2>${level.title}</h2>
      </a>
      <p class="section-copy">${level.summary}</p>
      ${highlights}
      ${phases}
      ${modules}
      ${examples}
      ${questions}
    </section>
  `;
}

function bindPhpCodeToggles(language) {
  const ui = PHP_LEVEL_UI[language];
  document.querySelectorAll(".php-code-toggle").forEach((button) => {
    button.onclick = () => {
      const block = button.closest(".php-example-block");
      if (!block) {
        return;
      }
      const collapsed = block.classList.toggle("collapsed");
      button.dataset.expanded = String(!collapsed);
      button.textContent = collapsed ? ui.expandCode : ui.collapseCode;
    };
  });
}

async function renderPhp(page, language, common, pages) {
  const pageKey = document.body.dataset.page;
  const allLevels = await loadPhpLevels(language);
  const switcherHost = document.getElementById("phpLevelSwitcher");
  const resumeHost = document.getElementById("phpResumePrompt");

  document.getElementById("eyebrow").textContent = text(page.eyebrow, language);
  document.getElementById("pageTitle").textContent = text(page.title, language);
  document.getElementById("pageSubtitle").textContent = text(page.subtitle, language);
  document.getElementById("backLink").textContent = text(common.backToHome, language);
  document.getElementById("phpJourneyTitle").textContent = text(page.journeyTitle, language);
  document.getElementById("phpJourneySubtitle").textContent = text(page.journeySubtitle, language);

  if (switcherHost) {
    switcherHost.innerHTML = createPhpLevelSwitcher(pageKey, page, pages.php, allLevels, language);
    bindPhpLevelSwitcher();
  }

  if (pageKey === "php") {
    if (resumeHost) {
      resumeHost.innerHTML = createPhpResumePrompt(language, allLevels);
    }
    document.getElementById("phpLevelNav").innerHTML = allLevels
      .map((level) => createLevelNavCard(level, PHP_LEVEL_PAGE_MAP[level.key], false))
      .join("");
    const phpLevels = document.getElementById("phpLevels");
    if (phpLevels) {
      phpLevels.innerHTML = "";
    }
    return;
  }

  const selectedLevelKey = pageKey.replace("php-", "");
  const selectedLevel = allLevels.find((level) => level.key === selectedLevelKey);
  if (!selectedLevel) {
    throw new Error(`Missing loaded PHP level data for "${selectedLevelKey}"`);
  }

  saveLastPhpLevel(pageKey);
  const levelData = withPhpIds([selectedLevel]);
  document.getElementById("phpLevelNav").innerHTML = allLevels
    .map((level) => createLevelNavCard(level, PHP_LEVEL_PAGE_MAP[level.key], level.key === selectedLevelKey))
    .join("");
  renderPhpKeywordDirectory(levelData, language);
  document.getElementById("phpLevels").innerHTML = selectedLevel ? renderPhpLevel(levelData[0], language) : "";
  bindPhpCodeToggles(language);
  bindPhpKeywordJumps();
}

function applyPageMeta(page, language) {
  document.title = text(page.metaTitle, language) || text(page.title, language);
  document.documentElement.lang = language;
}

function bindLanguageSwitch(data, render) {
  document.querySelectorAll(".lang-btn").forEach((button) => {
    button.onclick = async () => {
      const language = button.dataset.lang;
      if (!data.languages.includes(language) || language === currentLanguage) {
        return;
      }

      currentLanguage = language;
      localStorage.setItem(STORAGE_KEY, language);
      await render(language);
    };
  });
}

async function init() {
  await (window.__shellReady || Promise.resolve());
  const pageKey = document.body.dataset.page;

  const render = async (language) => {
    const data = await loadContent(language);
    const page = data.pages[pageKey]
      || (isLaravelPage(pageKey) ? data.pages.laravel : null)
      || (isInterviewPage(pageKey) ? data.pages.interview : null)
      || (isVideoPage(pageKey) ? data.pages.vibeCoding : null);
    if (!page) {
      throw new Error(`Missing page config for "${pageKey}" in ${language}`);
    }

    currentLanguage = language;
    setActiveLanguage(language);
    applyTheme(getTheme());
    renderGlobalHeader(data, language, pageKey);
    renderReadingDock(language);

    if (pageKey === "landing") {
      applyPageMeta(page, language);
      renderLanding(page, language);
    } else if (pageKey === "php" || pageKey.startsWith("php-")) {
      applyPageMeta(page, language);
      await renderPhp(page, language, data.common, data.pages);
    } else if (pageKey === "laravel") {
      applyPageMeta(page, language);
      renderLaravelOverview(page, language, data.common);
    } else if (pageKey.startsWith("laravel-")) {
      const section = await loadLaravelSection(language, pageKey);
      applyPageMeta(
        {
          metaTitle: `${section.heading} | Laravel Labs`,
          title: section.heading
        },
        language
      );
      renderLaravelTopic(page, section, language, data.common, pageKey);
    } else if (pageKey === "interview") {
      applyPageMeta(page, language);
      renderInterviewOverview(page, language, data.common);
    } else if (pageKey.startsWith("interview-")) {
      const section = await loadInterviewSection(language, pageKey);
      applyPageMeta(
        {
          metaTitle: `${section.heading} | Laravel Labs`,
          title: section.heading
        },
        language
      );
      renderInterviewTopic(page, section, language, data.common, pageKey);
    } else if (pageKey === "vibe-coding" || pageKey === "videos") {
      applyPageMeta(page, language);
      renderVideoOverview(page, language, data.common);
    } else if (pageKey.startsWith("vibe-") || pageKey.startsWith("video-")) {
      const section = await loadVideoSection(language, pageKey);
      applyPageMeta(
        {
          metaTitle: `${section.heading} | Laravel Labs`,
          title: section.heading
        },
        language
      );
      renderVideoTopic(page, section, language, data.common, pageKey);
    } else {
      applyPageMeta(page, language);
      renderDetail(page, language, data.common);
    }
    renderBreadcrumbTrail(data, language, pageKey);
    bindContentProgress(language);
    bindLanguageSwitch(data, render);
    bindThemeToggle(data, language, pageKey, render);
    bindHeaderDropdowns();
    bindGlobalSearch(data, language);
  };

  currentLanguage = getLanguage();
  await render(currentLanguage);
}

init().catch((error) => {
  console.error(error);
  document.body.innerHTML = `
    <main class="detail-main">
      <section class="panel detail-section">
        <h1>Site data failed to load</h1>
        <p class="section-copy">Open this project through a local server or GitHub Pages so JSON files can be fetched correctly.</p>
      </section>
    </main>
  `;
});

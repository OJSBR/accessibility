{**
 * plugins/blocks/accessibility/templates/block.tpl
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Accessibility sidebar block: zoom in / zoom out / high contrast / reset.
 *
 *}
<div class="pkp_block block_accessibility" role="region" aria-label="{translate key="plugins.block.accessibility.title"|escape}">
	<h2 class="title">{translate key="plugins.block.accessibility.title"}</h2>
	<div class="content">
		<div class="ojsbr-a11y-controls">
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button" data-a11y-action="zoom-out"
				title="{translate key="plugins.block.accessibility.zoomOut"|escape}"
				aria-label="{translate key="plugins.block.accessibility.zoomOut"|escape}">
				<span aria-hidden="true">A&minus;</span>
			</button>
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button" data-a11y-action="zoom-in"
				title="{translate key="plugins.block.accessibility.zoomIn"|escape}"
				aria-label="{translate key="plugins.block.accessibility.zoomIn"|escape}">
				<span aria-hidden="true">A&plus;</span>
			</button>
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button" data-a11y-action="contrast" aria-pressed="false"
				title="{translate key="plugins.block.accessibility.contrast"|escape}"
				aria-label="{translate key="plugins.block.accessibility.contrast"|escape}">
				<span aria-hidden="true">&#9681;</span>
			</button>
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button ojsbr-a11y-reset" data-a11y-action="reset"
				title="{translate key="plugins.block.accessibility.reset"|escape}"
				aria-label="{translate key="plugins.block.accessibility.reset"|escape}">
				<span aria-hidden="true">&#8635;</span>
			</button>
		</div>
		<p class="ojsbr-a11y-status ojsbr-a11y-sr" aria-live="polite"></p>
	</div>
</div>

{literal}
<style>
.block_accessibility .ojsbr-a11y-controls {
	display: flex;
	flex-wrap: wrap;
	gap: 6px;
}
.block_accessibility .ojsbr-a11y-btn {
	flex: 1 1 auto;
	min-width: 40px;
	min-height: 40px;
	padding: 8px 10px;
	line-height: 1;
	border-radius: 3px;
	cursor: pointer;
	text-align: center;
}
/* Fallback colour with zero specificity: the active theme's own button rule
 * (.cmp_manuscript_button — same as the journal galley buttons) wins whenever
 * it is present, so these controls always match the journal's dynamic colour. */
:where(.block_accessibility .ojsbr-a11y-btn) {
	background: #4b7d92;
	color: #fff;
	border: none;
	font-weight: 700;
}
.block_accessibility .ojsbr-a11y-btn:focus-visible {
	outline: 2px solid #1e6292;
	outline-offset: 2px;
}
/* Active state (e.g. contrast turned on) uses the theme accent colour. */
.block_accessibility .ojsbr-a11y-btn[aria-pressed="true"] {
	background: #f7bc4a;
	color: #111;
}
/* Visually-hidden live region: keeps screen-reader zoom feedback without
 * showing the "100%" text on screen. */
.block_accessibility .ojsbr-a11y-sr {
	position: absolute;
	width: 1px;
	height: 1px;
	margin: -1px;
	padding: 0;
	border: 0;
	overflow: hidden;
	clip: rect(0 0 0 0);
	white-space: nowrap;
}

/* High-contrast mode (applied to the whole document) */
html.ojsbr-a11y-contrast,
html.ojsbr-a11y-contrast body {
	background: #000 !important;
	color: #fff !important;
}
html.ojsbr-a11y-contrast * {
	background-color: transparent !important;
	color: #fff !important;
	border-color: #fff !important;
	box-shadow: none !important;
	text-shadow: none !important;
}
html.ojsbr-a11y-contrast a,
html.ojsbr-a11y-contrast a * {
	color: #ffff00 !important;
	text-decoration: underline !important;
}
html.ojsbr-a11y-contrast a:hover,
html.ojsbr-a11y-contrast a:focus {
	color: #000 !important;
	background: #ffff00 !important;
}
html.ojsbr-a11y-contrast input,
html.ojsbr-a11y-contrast textarea,
html.ojsbr-a11y-contrast select,
html.ojsbr-a11y-contrast button {
	background: #000 !important;
	color: #fff !important;
	border: 1px solid #fff !important;
}
html.ojsbr-a11y-contrast .block_accessibility .ojsbr-a11y-btn[aria-pressed="true"] {
	background: #ffff00 !important;
	color: #000 !important;
}
html.ojsbr-a11y-contrast img {
	background: #fff !important;
}
</style>
<script>
(function () {
	"use strict";
	var ZOOM_KEY = "ojsbrA11yZoom";
	var CONTRAST_KEY = "ojsbrA11yContrast";
	var MIN = 100, MAX = 200, STEP = 10, DEFAULT = 100;

	function store(key, value) {
		try { window.localStorage.setItem(key, value); } catch (e) {}
	}
	function load(key, fallback) {
		try { var v = window.localStorage.getItem(key); return v === null ? fallback : v; }
		catch (e) { return fallback; }
	}

	function getZoom() {
		var z = parseInt(load(ZOOM_KEY, DEFAULT), 10);
		if (isNaN(z) || z < MIN) { z = MIN; }
		if (z > MAX) { z = MAX; }
		return z;
	}

	function applyZoom(z) {
		document.documentElement.style.zoom = (z / 100);
		store(ZOOM_KEY, z);
	}

	function applyContrast(on) {
		document.documentElement.classList.toggle("ojsbr-a11y-contrast", on);
		store(CONTRAST_KEY, on ? "1" : "0");
	}

	function announce(msg) {
		var el = document.querySelector(".block_accessibility .ojsbr-a11y-status");
		if (el) { el.textContent = msg || ""; }
	}
	function syncContrastButtons(on) {
		var btns = document.querySelectorAll('.block_accessibility [data-a11y-action="contrast"]');
		for (var i = 0; i < btns.length; i++) {
			btns[i].setAttribute("aria-pressed", on ? "true" : "false");
		}
	}

	// Apply saved preferences immediately (before interaction).
	var contrastOn = load(CONTRAST_KEY, "0") === "1";
	applyZoom(getZoom());
	applyContrast(contrastOn);

	function onReady() {
		syncContrastButtons(document.documentElement.classList.contains("ojsbr-a11y-contrast"));

		document.addEventListener("click", function (ev) {
			var btn = ev.target.closest ? ev.target.closest("[data-a11y-action]") : null;
			if (!btn || !btn.classList.contains("ojsbr-a11y-btn")) { return; }
			ev.preventDefault();
			var action = btn.getAttribute("data-a11y-action");
			var z = getZoom();

			if (action === "zoom-in") {
				z = Math.min(MAX, z + STEP);
				applyZoom(z);
				announce(z + "%");
			} else if (action === "zoom-out") {
				z = Math.max(MIN, z - STEP);
				applyZoom(z);
				announce(z + "%");
			} else if (action === "contrast") {
				var on = !document.documentElement.classList.contains("ojsbr-a11y-contrast");
				applyContrast(on);
				syncContrastButtons(on);
			} else if (action === "reset") {
				applyZoom(DEFAULT);
				applyContrast(false);
				syncContrastButtons(false);
				announce(DEFAULT + "%");
			}
		}, false);
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", onReady, false);
	} else {
		onReady();
	}
})();
</script>
{/literal}

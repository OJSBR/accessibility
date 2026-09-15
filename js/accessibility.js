/**
 * @file plugins/blocks/accessibility/js/accessibility.js
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Accessibility block: zoom and high contrast, remembered in the browser.
 *
 * The preferences are applied only on pages that show the block, so a reader
 * can always reach the reset button of whatever mode is on.
 */

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

	// The zoom level as the page language writes a percentage ("110%", "110 %").
	function percent(z) {
		try {
			return new Intl.NumberFormat(document.documentElement.lang || undefined, {style: "percent"}).format(z / 100);
		} catch (e) {
			return z + "%";
		}
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

	var started = false;

	function start() {
		if (started || !document.querySelector(".block_accessibility")) {
			return;
		}
		started = true;
		applyZoom(getZoom());
		applyContrast(load(CONTRAST_KEY, "0") === "1");
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
				announce(percent(z));
			} else if (action === "zoom-out") {
				z = Math.max(MIN, z - STEP);
				applyZoom(z);
				announce(percent(z));
			} else if (action === "contrast") {
				var on = !document.documentElement.classList.contains("ojsbr-a11y-contrast");
				applyContrast(on);
				syncContrastButtons(on);
			} else if (action === "reset") {
				applyZoom(DEFAULT);
				applyContrast(false);
				syncContrastButtons(false);
				announce(percent(DEFAULT));
			}
		}, false);
	}

	// Scripts usually load at the end of the page, after the sidebar: start at
	// once. A theme that loads them in the head starts when the page is parsed.
	start();
	if (!started && document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", start, false);
	}
})();

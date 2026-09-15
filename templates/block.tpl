{**
 * plugins/blocks/accessibility/templates/block.tpl
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Accessibility sidebar block: zoom in / zoom out / high contrast / reset.
 *
 *}
<link rel="stylesheet" href="{$accessibilityPluginUrl|escape}/css/accessibility.css" />
<div class="pkp_block block_accessibility" role="region" aria-label="{"plugins.block.accessibility.title"|translate|escape}">
	<h2 class="title">{translate key="plugins.block.accessibility.title"}</h2>
	<div class="content">
		<div class="ojsbr-a11y-controls">
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button" data-a11y-action="zoom-out"
				title="{"plugins.block.accessibility.zoomOut"|translate|escape}"
				aria-label="{"plugins.block.accessibility.zoomOut"|translate|escape}">
				<span aria-hidden="true">A&minus;</span>
			</button>
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button" data-a11y-action="zoom-in"
				title="{"plugins.block.accessibility.zoomIn"|translate|escape}"
				aria-label="{"plugins.block.accessibility.zoomIn"|translate|escape}">
				<span aria-hidden="true">A&plus;</span>
			</button>
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button" data-a11y-action="contrast" aria-pressed="false"
				title="{"plugins.block.accessibility.contrast"|translate|escape}"
				aria-label="{"plugins.block.accessibility.contrast"|translate|escape}">
				<span aria-hidden="true">&#9681;</span>
			</button>
			<button type="button" class="ojsbr-a11y-btn cmp_manuscript_button ojsbr-a11y-reset" data-a11y-action="reset"
				title="{"plugins.block.accessibility.reset"|translate|escape}"
				aria-label="{"plugins.block.accessibility.reset"|translate|escape}">
				<span aria-hidden="true">&#8635;</span>
			</button>
		</div>
		<p class="ojsbr-a11y-status ojsbr-a11y-sr" aria-live="polite"></p>
	</div>
</div>

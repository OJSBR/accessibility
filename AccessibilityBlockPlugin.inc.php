<?php

/**
 * @file plugins/blocks/accessibility/AccessibilityBlockPlugin.inc.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AccessibilityBlockPlugin
 * @ingroup plugins_blocks_accessibility
 *
 * @brief Sidebar block that offers readers zoom (+/-) and high-contrast controls.
 */

import('lib.pkp.classes.plugins.BlockPlugin');

class AccessibilityBlockPlugin extends BlockPlugin {

	/**
	 * @copydoc BlockPlugin::getContents()
	 *
	 * Blocks are loaded while the sidebar is rendered, after the page head, so
	 * the script is queued here (scripts are printed at the end of the page) and
	 * the stylesheet is linked from the block itself.
	 */
	function getContents($templateMgr, $request = null) {
		if (!$request) {
			$request = Application::get()->getRequest();
		}
		$pluginUrl = $request->getBaseUrl() . '/' . $this->getPluginPath();

		$templateMgr->addJavaScript('accessibilityBlock', $pluginUrl . '/js/accessibility.js', ['contexts' => 'frontend']);
		$templateMgr->assign('accessibilityPluginUrl', $pluginUrl);

		return parent::getContents($templateMgr, $request);
	}

	/**
	 * Install default settings on journal creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the display name of this plugin.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.block.accessibility.displayName');
	}

	/**
	 * Get a description of the plugin.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.block.accessibility.description');
	}
}

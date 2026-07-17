<?php

/**
 * @file AccessibilityBlockPlugin.inc.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com.br)
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

<?php

/**
 * @file plugins/blocks/accessibility/AccessibilityBlockPlugin.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AccessibilityBlockPlugin
 *
 * @brief Sidebar block that offers readers zoom (+/-) and high-contrast controls.
 */

namespace APP\plugins\blocks\accessibility;

use PKP\plugins\BlockPlugin;

class AccessibilityBlockPlugin extends BlockPlugin
{
    /**
     * Install default settings on journal creation.
     *
     * @return string
     */
    public function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    /**
     * Get the display name of this plugin.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return __('plugins.block.accessibility.displayName');
    }

    /**
     * Get a description of the plugin.
     *
     * @return string
     */
    public function getDescription()
    {
        return __('plugins.block.accessibility.description');
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\blocks\accessibility\AccessibilityBlockPlugin', '\AccessibilityBlockPlugin');
}

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

use APP\core\Application;
use PKP\plugins\BlockPlugin;

class AccessibilityBlockPlugin extends BlockPlugin
{
    /**
     * Render the block and queue its script.
     *
     * Blocks are loaded while the sidebar is rendered, after the page head, so
     * the script is queued here (scripts are printed at the end of the page) and
     * the stylesheet is linked from the block itself.
     *
     * @param null|mixed $request
     */
    public function getContents($templateMgr, $request = null): string
    {
        $request ??= Application::get()->getRequest();
        $pluginUrl = $request->getBaseUrl() . '/' . $this->getPluginPath();

        $templateMgr->addJavaScript('accessibilityBlock', $pluginUrl . '/js/accessibility.js', ['contexts' => 'frontend']);
        $templateMgr->assign('accessibilityPluginUrl', $pluginUrl);

        return parent::getContents($templateMgr, $request);
    }

    /**
     * Default settings installed for each new journal.
     */
    public function getContextSpecificPluginSettingsFile(): string
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    /**
     * Name shown in the plugins list.
     */
    public function getDisplayName(): string
    {
        return __('plugins.block.accessibility.displayName');
    }

    /**
     * Description shown in the plugins list.
     */
    public function getDescription(): string
    {
        return __('plugins.block.accessibility.description');
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\blocks\accessibility\AccessibilityBlockPlugin', '\AccessibilityBlockPlugin');
}

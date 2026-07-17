<?php

/**
 * @defgroup plugins_blocks_accessibility Accessibility Block Plugin
 */

/**
 * @file index.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com.br)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_blocks_accessibility
 * @brief Wrapper for the accessibility (zoom & contrast) block plugin.
 *
 */

require_once('AccessibilityBlockPlugin.inc.php');

return new AccessibilityBlockPlugin();

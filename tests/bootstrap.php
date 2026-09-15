<?php

/**
 * @file plugins/blocks/accessibility/tests/bootstrap.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Bootstrap for the test suite.
 *
 * Under PKP's PHPUnit configuration the application is already loaded. When the
 * suite runs standalone (`php tests/run.php`) from a plugin installed in
 * plugins/blocks/accessibility, the OJS 3.3 installation around it is
 * bootstrapped so the plugin class is compiled against the real PKP classes.
 */

if (!class_exists('PKPApplication')) {
    $ojsRoot = dirname(__DIR__, 4);
    if (!is_file($ojsRoot . '/lib/pkp/includes/bootstrap.inc.php')) {
        fwrite(STDERR, "The plugin must be installed in plugins/blocks/accessibility of an OJS 3.3 installation to run the suite.\n");
        exit(2);
    }
    chdir($ojsRoot);
    define('INDEX_FILE_LOCATION', $ojsRoot . '/index.php');
    require_once $ojsRoot . '/lib/pkp/includes/bootstrap.inc.php';
}

import('lib.pkp.classes.plugins.BlockPlugin');
require_once dirname(__DIR__) . '/AccessibilityBlockPlugin.inc.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/PoFile.php';

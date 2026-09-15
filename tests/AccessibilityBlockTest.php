<?php

/**
 * @file plugins/blocks/accessibility/tests/AccessibilityBlockTest.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AccessibilityBlockTest
 *
 * @brief The block queues its script for reader pages and keeps scripts and styles out of its markup.
 */

namespace APP\plugins\blocks\accessibility\tests;

use APP\plugins\blocks\accessibility\AccessibilityBlockPlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PKP\tests\PKPTestCase;

#[CoversClass(AccessibilityBlockPlugin::class)]
class AccessibilityBlockTest extends PKPTestCase
{
    public function testTheScriptIsQueuedForReaderPagesOnly(): void
    {
        $plugin = new class () extends AccessibilityBlockPlugin {
            public function getPluginPath()
            {
                return 'plugins/blocks/accessibility';
            }

            public function getTemplateResource($template = null, $inCore = false)
            {
                return $template;
            }
        };
        $templateMgr = new class () {
            public array $scripts = [];

            public array $assigned = [];

            public function addJavaScript($name, $url, $args = [])
            {
                $this->scripts[$name] = [$url, $args];
            }

            public function assign($name, $value = null)
            {
                $this->assigned[$name] = $value;
            }

            public function fetch($resource)
            {
                return $resource;
            }
        };
        $request = new class () {
            public function getBaseUrl()
            {
                return 'https://journal.example.org';
            }
        };

        $this->assertSame('block.tpl', $plugin->getContents($templateMgr, $request));
        $this->assertSame(
            ['accessibilityBlock' => ['https://journal.example.org/plugins/blocks/accessibility/js/accessibility.js', ['contexts' => 'frontend']]],
            $templateMgr->scripts
        );
        $this->assertSame('https://journal.example.org/plugins/blocks/accessibility', $templateMgr->assigned['accessibilityPluginUrl']);
    }

    public function testTheTemplateHasNoInlineScriptOrStyle(): void
    {
        $template = (string) file_get_contents(dirname(__DIR__) . '/templates/block.tpl');
        $this->assertFalse(stripos($template, '<script') !== false, 'The template prints a script.');
        $this->assertFalse(stripos($template, '<style') !== false, 'The template prints a style block.');
    }

    public function testTheAnnouncedLevelFollowsThePageLanguage(): void
    {
        $script = (string) file_get_contents(dirname(__DIR__) . '/js/accessibility.js');
        $this->assertTrue(strpos($script, 'Intl.NumberFormat(document.documentElement.lang || undefined, {style: "percent"})') !== false);
        $this->assertFalse(strpos($script, 'announce(z + "%")') !== false, 'The level is announced with a hard-coded percent sign.');
    }
}

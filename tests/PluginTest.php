<?php

/**
 * @file plugins/blocks/accessibility/tests/PluginTest.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PluginTest
 *
 * @brief The plugin class compiled against the PKP classes of the installation,
 *        and the assets it publishes.
 */

namespace APP\plugins\blocks\accessibility\tests;

// OJS 3.3 has no autoloader for plugin classes.
require_once __DIR__ . '/bootstrap.php';

use ReflectionClass;

class PluginTest extends TestCase
{
    public function testTheClassLoadsWithTheReturnTypesOfThisPkpVersion(): void
    {
        $this->assertTrue(is_subclass_of('AccessibilityBlockPlugin', 'BlockPlugin'));
        $reflection = new ReflectionClass('AccessibilityBlockPlugin');
        $parent = $reflection->getParentClass();
        foreach ($reflection->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== 'AccessibilityBlockPlugin' || !$parent->hasMethod($method->getName())) {
                continue;
            }
            $parentType = $parent->getMethod($method->getName())->getReturnType();
            if ($parentType !== null) {
                $this->assertSame((string) $parentType, (string) $method->getReturnType(), $method->getName() . '() must declare the return type ' . $parentType . '.');
            }
        }
    }

    public function testTheAssetsAreFilesAndNotInlineCode(): void
    {
        $root = dirname(__DIR__);
        $source = (string) file_get_contents($root . '/AccessibilityBlockPlugin.inc.php');

        $this->assertTrue(is_file($root . '/css/accessibility.css'));
        $this->assertTrue(is_file($root . '/js/accessibility.js'));
        $this->assertStringContainsString("addJavaScript('accessibilityBlock', \$pluginUrl . '/js/accessibility.js', ['contexts' => 'frontend'])", $source);
        $template = (string) file_get_contents($root . '/templates/block.tpl');
        $this->assertStringContainsString('<link rel="stylesheet" href="{$accessibilityPluginUrl|escape}/css/accessibility.css" />', $template);
    }

    public function testTheScriptActsOnlyWhereTheBlockIsShown(): void
    {
        // A reader who turned high contrast on must always reach the reset
        // button: the preferences are applied only on pages with the block.
        $script = (string) file_get_contents(dirname(__DIR__) . '/js/accessibility.js');
        $this->assertStringContainsString('if (started || !document.querySelector(".block_accessibility"))', $script);
        $this->assertSame(1, preg_match('/var MIN = (\d+), MAX = (\d+), STEP = (\d+), DEFAULT = (\d+);/', $script, $m));
        $this->assertTrue((int) $m[1] <= (int) $m[4] && (int) $m[4] < (int) $m[2], 'The default zoom must lie within the limits.');
        $this->assertStringContainsString('try { window.localStorage.setItem(key, value); } catch (e) {}', $script, 'Blocked storage must not break the page.');
    }
}

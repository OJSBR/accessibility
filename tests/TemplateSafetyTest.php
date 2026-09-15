<?php

/**
 * @file plugins/blocks/accessibility/tests/TemplateSafetyTest.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class TemplateSafetyTest
 *
 * @brief Static checks on the block template and the source files.
 */

namespace APP\plugins\blocks\accessibility\tests;

class TemplateSafetyTest extends TestCase
{
    protected function template(): string
    {
        return (string) file_get_contents(dirname(__DIR__) . '/templates/block.tpl');
    }

    public function testTranslationsInAttributesAreEscaped(): void
    {
        // {translate key="..."|escape} escapes the key, not the translation.
        $template = $this->template();
        $this->assertSame(0, preg_match('/\{translate key="[^"]+"\|escape\}/', $template));
        $this->assertSame(9, preg_match_all('/(title|aria-label)="\{"plugins\.block\.accessibility\.[a-zA-Z]+"\|translate\|escape\}"/', $template));
    }

    public function testEveryControlHasAnAccessibleName(): void
    {
        preg_match_all('/<button\b[^>]*>/s', $this->template(), $buttons);
        $this->assertCount(4, $buttons[0]);
        foreach ($buttons[0] as $button) {
            $this->assertStringContainsString('type="button"', $button);
            $this->assertStringContainsString('aria-label=', $button);
            $this->assertStringContainsString('data-a11y-action=', $button);
        }
        $this->assertStringContainsString('aria-live="polite"', $this->template());
    }

    public function testTheTemplateCarriesNoInlineScriptOrStyle(): void
    {
        $this->assertStringNotContainsString('<script', $this->template());
        $this->assertStringNotContainsString('<style', $this->template());
        $this->assertStringNotContainsString('{literal}', $this->template());
    }

    public function testTemplateHasNoHardcodedText(): void
    {
        $text = preg_replace('/\{\*.*?\*\}/s', '', $this->template());
        $text = preg_replace('/\{[^{}]*\}/', '', $text);
        $text = html_entity_decode(trim(strip_tags($text)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Only the button glyphs, hidden from assistive technology.
        $this->assertSame('A−A+◑↻', preg_replace('/\s+/u', '', $text));
    }

    public function testSourceIsWrittenInEnglishWithTheStandardHeader(): void
    {
        $root = dirname(__DIR__);
        $files = array_merge(glob($root . '/*.php'), glob(__DIR__ . '/*.php'), glob($root . '/templates/*.tpl'), glob($root . '/css/*.css'), glob($root . '/js/*.js'));
        foreach ($files as $file) {
            $source = (string) file_get_contents($file);
            $this->assertStringContainsString('Copyright (c) 2026 OJSBR (https://ojsbr.com)', $source, basename($file) . ' lacks the header.');
            $this->assertStringContainsString('plugins/blocks/accessibility/', $source, basename($file) . ' lacks the full @file path.');
        }
    }
}

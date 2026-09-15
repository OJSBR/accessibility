<?php

/**
 * @file plugins/blocks/accessibility/tests/LocaleFilesTest.php
 *
 * Copyright (c) 2026 OJSBR (https://ojsbr.com)
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class LocaleFilesTest
 *
 * @brief Translations. a key missing from a locale is rendered as ##key## in
 *        OJS 3.3, so an incomplete file is worse than none.
 */

namespace APP\plugins\blocks\accessibility\tests;

// OJS 3.3 has no autoloader for plugin classes.
require_once __DIR__ . '/bootstrap.php';

class LocaleFilesTest extends TestCase
{
    /** Locale codes shipped by the plugin: the languages of the OJS 3.3 registry, with its codes. */
    public const LOCALES = [
        'ar_IQ', 'ca_ES', 'cs_CZ', 'da_DK', 'de_DE', 'el_GR', 'en_US', 'es_ES', 'eu_ES', 'fa_IR', 'fi_FI',
        'fr_CA', 'fr_FR', 'hu_HU', 'hy_AM', 'id_ID', 'it_IT', 'ja_JP', 'mk_MK', 'nb_NO', 'nl_NL', 'pl_PL',
        'pt_BR', 'pt_PT', 'ro_RO', 'ru_RU', 'sl_SI', 'sr_RS@latin', 'sv_SE', 'tr_TR', 'uk_UA', 'vi_VN', 'zh_CN',
    ];

    /** Locales reviewed by a fluent speaker; every other one is marked fuzzy. */
    public const REVIEWED = ['en_US', 'pt_PT', 'pt_BR', 'es_ES', 'ca_ES', 'fr_FR', 'fr_CA', 'it_IT', 'de_DE', 'nl_NL'];

    /** Placeholders each key must keep, exactly once. */
    public const PLACEHOLDERS = [];

    public const PREFIX = 'plugins.block.accessibility.';

    protected function localeDir(): string
    {
        return dirname(__DIR__) . '/locale';
    }

    /** @return array<string, PoFile> */
    protected function files(): array
    {
        $files = [];
        foreach (self::LOCALES as $locale) {
            $path = $this->localeDir() . "/{$locale}/locale.po";
            if (is_file($path)) {
                $files[$locale] = new PoFile($path);
            }
        }
        return $files;
    }

    public function testShipsExactlyTheSupportedLocaleCodes(): void
    {
        $dirs = array_map('basename', glob($this->localeDir() . '/*', GLOB_ONLYDIR) ?: []);
        sort($dirs);
        $expected = self::LOCALES;
        sort($expected);

        // OJS 3.3 only loads the locales of registry/locales.xml, with five-letter
        // codes; a short code such as pt or a language outside the registry would
        // silently never load.
        $this->assertSame($expected, $dirs);
        $this->assertCount(count(self::LOCALES), $this->files());
    }

    public function testEveryLocaleHasExactlyTheKeysOfTheEnglishMaster(): void
    {
        $files = $this->files();
        $master = array_keys($files['en_US']->entries);
        $this->assertCount(7, $master);

        foreach ($files as $locale => $file) {
            $this->assertSame($master, array_keys($file->entries), "Keys of {$locale} differ from en.");
        }
    }

    public function testEveryKeyTheCodeUsesExists(): void
    {
        $sources = '';
        foreach (array_merge(glob(dirname(__DIR__) . '/*.php'), glob(dirname(__DIR__) . '/templates/*.tpl')) as $file) {
            $sources .= file_get_contents($file);
        }
        preg_match_all('/plugins\.block\.accessibility\.[a-zA-Z.]+[a-zA-Z]/', $sources, $m);
        $keys = array_keys($this->files()['en_US']->entries);

        foreach (array_unique($m[0]) as $key) {
            $this->assertTrue(in_array($key, $keys, true), "{$key} is used but not translated.");
        }
    }

    public function testNoTranslationIsEmpty(): void
    {
        foreach ($this->files() as $locale => $file) {
            foreach ($file->entries as $key => $value) {
                $this->assertNotEmpty(trim($value), "Empty translation for {$key} in {$locale}.");
            }
        }
    }

    public function testHeaderDeclaresTheLocaleAndTheTeam(): void
    {
        foreach ($this->files() as $locale => $file) {
            $this->assertStringContainsString("Language: {$locale}\n", $file->header, "Wrong Language header in {$locale}.");
            $this->assertStringContainsString("Last-Translator: OJSBR\n", $file->header, "Missing Last-Translator in {$locale}.");
            $this->assertStringContainsString("Language-Team: OJSBR\n", $file->header, "Missing Language-Team in {$locale}.");
        }
    }

    public function testUnreviewedLocalesAreFuzzyAndReviewedOnesAreNot(): void
    {
        foreach (self::LOCALES as $locale) {
            $source = (string) file_get_contents($this->localeDir() . "/{$locale}/locale.po");
            $fuzzy = substr_count($source, "#, fuzzy\n");
            $expected = in_array($locale, self::REVIEWED, true) ? 0 : 7;
            $this->assertSame($expected, $fuzzy, "Unexpected number of fuzzy entries in {$locale}.");
        }
    }

    public function testPlaceholdersAreKept(): void
    {
        foreach ($this->files() as $locale => $file) {
            // No string of this plugin takes parameters.
            $this->assertSame(0, preg_match('/\{\$/', implode("\n", $file->entries)), "Unexpected placeholder in {$locale}.");
            foreach (self::PLACEHOLDERS as $key => $placeholders) {
                $value = $file->entries[self::PREFIX . $key];
                foreach ($placeholders as $placeholder) {
                    $this->assertSame(1, substr_count($value, $placeholder), "{$placeholder} must appear once in {$key} ({$locale}).");
                }
                // "count" is reserved by __() and {translate} in PKP 3.3.
                $this->assertStringNotContainsString('{$count}', $value);
            }
        }
    }

    public function testTranslationsCarryNoMarkup(): void
    {
        foreach ($this->files() as $locale => $file) {
            foreach ($file->entries as $key => $value) {
                $this->assertSame(strip_tags($value), $value, "Markup in {$key} ({$locale}).");
            }
        }
    }

    public function testTheDescriptionCarriesNoCredit(): void
    {
        // Credit belongs to the README and version.xml, not to the settings list.
        foreach ($this->files() as $locale => $file) {
            $this->assertStringNotContainsString('OJSBR', $file->entries[self::PREFIX . 'description'], "Credit in the description of {$locale}.");
        }
    }
}

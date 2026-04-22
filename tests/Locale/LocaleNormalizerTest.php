<?php

namespace Rjds\PhpHumanize\Tests\Locale;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Locale\LocaleNormalizer;

class LocaleNormalizerTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function normalizeProvider(): array
    {
        return [
            'trims and lowercases language' => [' EN ', 'en'],
            'converts underscore and uppercases region' => ['en_us', 'en-US'],
            'normalizes mixed separators and duplicate dashes' => ['nl__nl', 'nl-NL'],
            'supports script casing' => ['zh_hant_tw', 'zh-Hant-TW'],
            'normalizes uppercase script to title case' => ['zh_HANT_tw', 'zh-Hant-TW'],
            'supports numeric region' => ['es-419', 'es-419'],
            'keeps variant lowercase' => ['sl-rozaj-biske', 'sl-rozaj-biske'],
            'normalizes uppercase variant to lowercase' => ['en-POSIX', 'en-posix'],
            'skips empty middle segments' => ['de--DE', 'de-DE'],
            'handles leading separator segment' => ['-en', '-EN'],
            'blank stays blank' => ['   ', ''],
        ];
    }

    #[DataProvider('normalizeProvider')]
    public function testNormalize(string $locale, string $expected): void
    {
        self::assertSame($expected, LocaleNormalizer::normalize($locale));
    }

    public function testNormalizeOrDefaultUsesNormalizedInputWhenPresent(): void
    {
        self::assertSame('fr-FR', LocaleNormalizer::normalizeOrDefault(' FR_fr ', 'en'));
    }

    public function testNormalizeOrDefaultUsesNormalizedDefaultWhenInputBlank(): void
    {
        self::assertSame('pt-BR', LocaleNormalizer::normalizeOrDefault('   ', ' PT_br '));
    }

    public function testNormalizeOrDefaultFallsBackToEnglishWhenBothBlank(): void
    {
        self::assertSame('en', LocaleNormalizer::normalizeOrDefault('   ', '   '));
    }

    public function testNormalizeRequiredReturnsCanonicalLocale(): void
    {
        self::assertSame('nl-NL', LocaleNormalizer::normalizeRequired('nl_nl'));
    }

    public function testNormalizeRequiredThrowsForBlankLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('locale cannot be empty.');

        LocaleNormalizer::normalizeRequired('  ');
    }

    public function testNormalizeRequiredThrowsUsingCustomFieldName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('preferredLocale cannot be empty.');

        LocaleNormalizer::normalizeRequired('  ', 'preferredLocale');
    }
}

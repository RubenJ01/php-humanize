<?php

namespace Rjds\PhpHumanize\Tests\Formatter\DateTime;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\DateLocalizedFormatter;

class DateLocalizedFormatterTest extends TestCase
{
    private DateLocalizedFormatter $formatter;

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function localizedDateProvider(): array
    {
        return [
            'english march' => ['2026-03-30 12:00:00+00:00', DateLocalizedFormatter::LOCALE_EN, 'Monday 30 March 2026'],
            'english march date only' => ['2026-03-30', DateLocalizedFormatter::LOCALE_EN, 'Monday 30 March 2026'],
            'dutch march' => ['2026-03-30 12:00:00+00:00', DateLocalizedFormatter::LOCALE_NL, 'Maandag 30 maart 2026'],
            'dutch march date only' => ['2026-03-30', DateLocalizedFormatter::LOCALE_NL, 'Maandag 30 maart 2026'],
            'dutch february date only' => [
                '2022-02-17',
                DateLocalizedFormatter::LOCALE_NL,
                'Donderdag 17 februari 2022'
            ],
            'dutch locale with region' => ['2026-03-30 12:00:00+00:00', 'nl_NL', 'Maandag 30 maart 2026'],
            'dutch uppercase locale' => ['2026-03-30 12:00:00+00:00', 'NL', 'Maandag 30 maart 2026'],
            'english leap day' => [
                '2024-02-29 12:00:00+00:00',
                DateLocalizedFormatter::LOCALE_EN,
                'Thursday 29 February 2024'
            ],
            'dutch leap day' => [
                '2024-02-29 12:00:00+00:00',
                DateLocalizedFormatter::LOCALE_NL,
                'Donderdag 29 februari 2024'
            ],
            'english january' => [
                '2026-01-05 12:00:00+00:00',
                DateLocalizedFormatter::LOCALE_EN,
                'Monday 5 January 2026'
            ],
            'dutch january' => [
                '2026-01-05 12:00:00+00:00',
                DateLocalizedFormatter::LOCALE_NL,
                'Maandag 5 januari 2026'
            ],
            'english december' => [
                '2026-12-25 12:00:00+00:00',
                DateLocalizedFormatter::LOCALE_EN,
                'Friday 25 December 2026'
            ],
            'dutch december' => [
                '2026-12-25 12:00:00+00:00',
                DateLocalizedFormatter::LOCALE_NL,
                'Vrijdag 25 december 2026'
            ],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function fallbackLocaleProvider(): array
    {
        return [
            'french fallback' => ['fr', 'Monday 30 March 2026'],
            'french fallback date only' => ['fr_FR', 'Monday 30 March 2026'],
            'german locale fallback' => ['de_DE', 'Monday 30 March 2026'],
            'unknown language fallback' => ['zz', 'Monday 30 March 2026'],
        ];
    }

    #[DataProvider('localizedDateProvider')]
    public function testItFormatsReadableLocalizedDate(string $date, string $locale, string $expected): void
    {
        $dateTime = new DateTimeImmutable($date);

        self::assertSame($expected, $this->formatter->format($dateTime, $locale));
    }

    #[DataProvider('fallbackLocaleProvider')]
    public function testItFallsBackToEnglishForUnsupportedLocales(string $locale, string $expected): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 12:00:00+00:00');

        self::assertSame($expected, $this->formatter->format($dateTime, $locale));
    }

    public function testItUsesEnglishByDefaultWhenNoLocaleIsProvided(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30');

        self::assertSame('Monday 30 March 2026', $this->formatter->format($dateTime));
    }

    public function testItThrowsWhenFirstArgumentIsNotDateTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First argument must be a DateTimeInterface');

        $this->formatter->format('2026-03-30', 'nl');
    }

    public function testItThrowsWhenLocaleIsWhitespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Second argument must be a non-empty locale string');

        $this->formatter->format(new DateTimeImmutable('2026-03-30 12:00:00+00:00'), '   ');
    }

    protected function setUp(): void
    {
        $this->formatter = new DateLocalizedFormatter();
    }
}

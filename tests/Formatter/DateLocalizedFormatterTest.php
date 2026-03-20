<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\DateLocalizedFormatter;

class DateLocalizedFormatterTest extends TestCase
{
    private DateLocalizedFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DateLocalizedFormatter();
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function localizedDateProvider(): array
    {
        return [
            'english march' => ['2026-03-30 12:00:00+00:00', 'en', 'Monday 30 March'],
            'english march date only' => ['2026-03-30', 'en', 'Monday 30 March'],
            'dutch march' => ['2026-03-30 12:00:00+00:00', 'nl', 'Maandag 30 maart'],
            'dutch march date only' => ['2026-03-30', 'nl', 'Maandag 30 maart'],
            'dutch february date only' => ['2022-02-17', 'nl', 'Donderdag 17 februari'],
            'dutch locale with region' => ['2026-03-30 12:00:00+00:00', 'nl_NL', 'Maandag 30 maart'],
            'dutch uppercase locale' => ['2026-03-30 12:00:00+00:00', 'NL', 'Maandag 30 maart'],
            'english leap day' => ['2024-02-29 12:00:00+00:00', 'en', 'Thursday 29 February'],
            'dutch leap day' => ['2024-02-29 12:00:00+00:00', 'nl', 'Donderdag 29 februari'],
            'english january' => ['2026-01-05 12:00:00+00:00', 'en', 'Monday 5 January'],
            'dutch january' => ['2026-01-05 12:00:00+00:00', 'nl', 'Maandag 5 januari'],
            'english december' => ['2026-12-25 12:00:00+00:00', 'en', 'Friday 25 December'],
            'dutch december' => ['2026-12-25 12:00:00+00:00', 'nl', 'Vrijdag 25 december'],
        ];
    }

    #[DataProvider('localizedDateProvider')]
    public function testItFormatsReadableLocalizedDate(string $date, string $locale, string $expected): void
    {
        $dateTime = new DateTimeImmutable($date);

        self::assertSame($expected, $this->formatter->format($dateTime, $locale));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function fallbackLocaleProvider(): array
    {
        return [
            'french fallback' => ['fr', 'Monday 30 March'],
            'french fallback date only' => ['fr_FR', 'Monday 30 March'],
            'german locale fallback' => ['de_DE', 'Monday 30 March'],
            'unknown language fallback' => ['zz', 'Monday 30 March'],
        ];
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

        self::assertSame('Monday 30 March', $this->formatter->format($dateTime));
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
}

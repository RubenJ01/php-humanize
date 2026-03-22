<?php

namespace Rjds\PhpHumanize\Tests\Formatter\DateTime;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DateLocalizedFormatter;

class DateFormatterTest extends TestCase
{
    private DateFormatter $formatter;

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function localizedDateProvider(): array
    {
        return [
            'english march' => ['2026-03-30 12:00:00+00:00', DateFormatter::LOCALE_EN, 'Monday 30 March 2026'],
            'english march date only' => ['2026-03-30', DateFormatter::LOCALE_EN, 'Monday 30 March 2026'],
            'dutch march' => ['2026-03-30 12:00:00+00:00', DateFormatter::LOCALE_NL, 'Maandag 30 maart 2026'],
            'dutch march date only' => ['2026-03-30', DateFormatter::LOCALE_NL, 'Maandag 30 maart 2026'],
            'dutch february date only' => [
                '2022-02-17',
                DateFormatter::LOCALE_NL,
                'Donderdag 17 februari 2022'
            ],
            'dutch locale with region' => ['2026-03-30 12:00:00+00:00', 'nl_NL', 'Maandag 30 maart 2026'],
            'dutch uppercase locale' => ['2026-03-30 12:00:00+00:00', 'NL', 'Maandag 30 maart 2026'],
            'english leap day' => [
                '2024-02-29 12:00:00+00:00',
                DateFormatter::LOCALE_EN,
                'Thursday 29 February 2024'
            ],
            'dutch leap day' => [
                '2024-02-29 12:00:00+00:00',
                DateFormatter::LOCALE_NL,
                'Donderdag 29 februari 2024'
            ],
            'english january' => [
                '2026-01-05 12:00:00+00:00',
                DateFormatter::LOCALE_EN,
                'Monday 5 January 2026'
            ],
            'dutch january' => [
                '2026-01-05 12:00:00+00:00',
                DateFormatter::LOCALE_NL,
                'Maandag 5 januari 2026'
            ],
            'english december' => [
                '2026-12-25 12:00:00+00:00',
                DateFormatter::LOCALE_EN,
                'Friday 25 December 2026'
            ],
            'dutch december' => [
                '2026-12-25 12:00:00+00:00',
                DateFormatter::LOCALE_NL,
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

    public function testItSupportsInjectedCustomLocaleTranslations(): void
    {
        $formatter = new DateFormatter([
            'fr' => [
                'weekdays' => [
                    1 => 'Lundi',
                    2 => 'Mardi',
                    3 => 'Mercredi',
                    4 => 'Jeudi',
                    5 => 'Vendredi',
                    6 => 'Samedi',
                    7 => 'Dimanche',
                ],
                'months' => [
                    1 => 'janvier',
                    2 => 'fevrier',
                    3 => 'mars',
                    4 => 'avril',
                    5 => 'mai',
                    6 => 'juin',
                    7 => 'juillet',
                    8 => 'aout',
                    9 => 'septembre',
                    10 => 'octobre',
                    11 => 'novembre',
                    12 => 'decembre',
                ],
            ],
        ]);

        self::assertSame(
            'Lundi 30 mars 2026',
            $formatter->format(new DateTimeImmutable('2026-03-30 12:00:00+00:00'), 'fr_FR')
        );
    }

    public function testItAllowsOverridingBuiltInLocaleTranslations(): void
    {
        $formatter = new DateFormatter([
            'nl' => [
                'weekdays' => [
                    1 => 'Ma',
                    2 => 'Di',
                    3 => 'Wo',
                    4 => 'Do',
                    5 => 'Vr',
                    6 => 'Za',
                    7 => 'Zo',
                ],
                'months' => [
                    1 => 'jan',
                    2 => 'feb',
                    3 => 'mrt',
                    4 => 'apr',
                    5 => 'mei',
                    6 => 'jun',
                    7 => 'jul',
                    8 => 'aug',
                    9 => 'sep',
                    10 => 'okt',
                    11 => 'nov',
                    12 => 'dec',
                ],
            ],
        ]);

        self::assertSame('Ma 30 mrt 2026', $formatter->format(new DateTimeImmutable('2026-03-30'), 'nl'));
    }

    public function testItIgnoresInjectedTranslationsWithEmptyLocaleKey(): void
    {
        $formatter = new DateFormatter([
            '' => [
                'weekdays' => [
                    1 => 'X1',
                    2 => 'X2',
                    3 => 'X3',
                    4 => 'X4',
                    5 => 'X5',
                    6 => 'X6',
                    7 => 'X7',
                ],
                'months' => [
                    1 => 'M1',
                    2 => 'M2',
                    3 => 'M3',
                    4 => 'M4',
                    5 => 'M5',
                    6 => 'M6',
                    7 => 'M7',
                    8 => 'M8',
                    9 => 'M9',
                    10 => 'M10',
                    11 => 'M11',
                    12 => 'M12',
                ],
            ],
        ]);

        self::assertSame('Monday 30 March 2026', $formatter->format(new DateTimeImmutable('2026-03-30'), 'fr'));
    }

    public function testDeprecatedDateLocalizedFormatterAliasStillWorks(): void
    {
        $formatter = new DateLocalizedFormatter();

        self::assertSame('Maandag 30 maart 2026', $formatter->format(new DateTimeImmutable('2026-03-30'), 'nl'));
        self::assertSame('readableDate', $formatter->getName());
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
        $this->formatter = new DateFormatter();
    }
}

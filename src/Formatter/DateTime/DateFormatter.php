<?php

namespace Rjds\PhpHumanize\Formatter\DateTime;

use DateTimeInterface;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\LocaleMap;

class DateFormatter implements FormatterInterface
{
    public const LOCALE_EN = LocaleMap::DEFAULT_LOCALE;
    public const LOCALE_NL = 'nl';

    /**
     * @var array<string, array{weekdays: array<int, string>, months: array<int, string>}>
     */
    private const DEFAULT_TRANSLATIONS = [
        'en' => [
            'weekdays' => [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday',
            ],
            'months' => [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ],
        ],
        'nl' => [
            'weekdays' => [
                1 => 'Maandag',
                2 => 'Dinsdag',
                3 => 'Woensdag',
                4 => 'Donderdag',
                5 => 'Vrijdag',
                6 => 'Zaterdag',
                7 => 'Zondag',
            ],
            'months' => [
                1 => 'januari',
                2 => 'februari',
                3 => 'maart',
                4 => 'april',
                5 => 'mei',
                6 => 'juni',
                7 => 'juli',
                8 => 'augustus',
                9 => 'september',
                10 => 'oktober',
                11 => 'november',
                12 => 'december',
            ],
        ],
    ];

    /**
     * @var array<string, array{weekdays: array<int, string>, months: array<int, string>}>
     */
    private array $translations;

    /**
     * @param array<string, array{weekdays: array<int, string>, months: array<int, string>}> $translations
     */
    public function __construct(array $translations = [])
    {
        $this->translations = LocaleMap::withOverrides(self::DEFAULT_TRANSLATIONS, $translations);
    }

    public function format(...$args): string
    {
        $dateTime = $args[0] ?? null;
        $locale = $args[1] ?? self::LOCALE_EN;

        if (!($dateTime instanceof DateTimeInterface)) {
            throw new \InvalidArgumentException('First argument must be a DateTimeInterface');
        }

        if (!is_string($locale) || trim($locale) === '') {
            throw new \InvalidArgumentException('Second argument must be a non-empty locale string');
        }

        return $this->formatWithTranslations($dateTime, $locale);
    }

    public function getName(): string
    {
        return 'readableDate';
    }

    private function formatWithTranslations(DateTimeInterface $dateTime, string $locale): string
    {
        $language = LocaleMap::normalize($locale);
        $translations = $this->translations[$language] ?? $this->translations[self::LOCALE_EN];

        $weekday = $translations['weekdays'][$dateTime->format('N')];
        $month = $translations['months'][$dateTime->format('n')];

        return $weekday . ' ' . $dateTime->format('j') . ' ' . $month . ' ' . $dateTime->format('Y');
    }
}

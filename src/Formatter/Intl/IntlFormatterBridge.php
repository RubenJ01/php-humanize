<?php

namespace Rjds\PhpHumanize\Formatter\Intl;

use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;
use Rjds\PhpHumanize\Locale\LocaleNormalizer;

final class IntlFormatterBridge
{
    private const DATE_PATTERN = 'EEEE d MMMM y';

    public static function formatDecimal(float $number, int $precision, string $locale): string
    {
        $formatter = new \NumberFormatter(self::normalizeLocale($locale), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $precision);
        $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);

        // @infection-ignore-all
        return self::normalizeNegativeZero((string) $formatter->format($number));
    }

    public static function formatDate(DateTimeInterface $dateTime, string $locale): string
    {
        $formatter = new IntlDateFormatter(
            self::normalizeLocale($locale),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            self::normalizeTimezone($dateTime->getTimezone()->getName()),
            IntlDateFormatter::GREGORIAN,
            self::DATE_PATTERN
        );

        // @infection-ignore-all
        $formatted = trim((string) $formatter->format($dateTime));
        return ucfirst($formatted);
    }

    private static function normalizeLocale(string $locale): string
    {
        return LocaleNormalizer::normalizeOrDefault($locale, 'en');
    }

    private static function normalizeTimezone(string $timezone): string
    {
        if (self::isUtcOffsetTimezone($timezone)) {
            return 'UTC';
        }

        return $timezone !== '' ? $timezone : (new DateTimeZone('UTC'))->getName();
    }

    private static function normalizeNegativeZero(string $value): string
    {
        $normalized = str_replace(',', '.', $value);
        $shouldNormalize = str_starts_with($value, '-0') && (float) $normalized === 0.0;
        return $shouldNormalize ? ltrim($value, '-') : $value;
    }

    private static function isUtcOffsetTimezone(string $timezone): bool
    {
        return preg_match('/^[+-]\d{2}:\d{2}$/D', $timezone) === 1;
    }
}

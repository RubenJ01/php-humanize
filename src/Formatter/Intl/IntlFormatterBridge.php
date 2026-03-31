<?php

namespace Rjds\PhpHumanize\Formatter\Intl;

use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;

final class IntlFormatterBridge
{
    private const DATE_PATTERN = 'EEEE d MMMM y';

    private function __construct()
    {
    }

    public static function formatDecimal(float $number, int $precision, string $locale): string
    {
        $formatter = new \NumberFormatter(self::normalizeLocale($locale), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $precision);
        $formatter->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);

        $formatted = $formatter->format($number);
        if (!is_string($formatted)) {
            throw new \RuntimeException('Intl failed to format decimal value.');
        }

        return self::normalizeNegativeZero($formatted);
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

        $formatted = $formatter->format($dateTime);
        if (!is_string($formatted)) {
            throw new \RuntimeException('Intl failed to format date value.');
        }

        $formatted = trim($formatted);
        if ($formatted === '') {
            throw new \RuntimeException('Intl failed to format date value.');
        }

        return ucfirst($formatted);
    }

    private static function normalizeLocale(string $locale): string
    {
        $normalized = trim($locale);
        if ($normalized === '') {
            return 'en';
        }

        return $normalized;
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
        if (!str_starts_with($value, '-0')) {
            return $value;
        }

        $normalized = str_replace(',', '.', $value);
        if ((float) $normalized === 0.0) {
            return ltrim($value, '-');
        }

        return $value;
    }

    private static function isUtcOffsetTimezone(string $timezone): bool
    {
        if (strlen($timezone) !== 6) {
            return false;
        }

        if (($timezone[0] !== '+' && $timezone[0] !== '-') || $timezone[3] !== ':') {
            return false;
        }

        return ctype_digit(substr($timezone, 1, 2)) && ctype_digit(substr($timezone, 4, 2));
    }
}

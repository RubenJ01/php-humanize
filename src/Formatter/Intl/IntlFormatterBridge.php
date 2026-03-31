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
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $precision);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $precision);
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
        if (!is_string($formatted) || trim($formatted) === '') {
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

        return str_replace('_', '-', $normalized);
    }

    private static function normalizeTimezone(string $timezone): string
    {
        if (preg_match('/^[+-]\d{2}:\d{2}$/', $timezone) === 1) {
            return 'UTC';
        }

        if ($timezone === '') {
            return (new DateTimeZone('UTC'))->getName();
        }

        return $timezone;
    }

    private static function normalizeNegativeZero(string $value): string
    {
        if (preg_match('/^-0(?:[.,]0+)?$/', $value) === 1) {
            return ltrim($value, '-');
        }

        return $value;
    }
}

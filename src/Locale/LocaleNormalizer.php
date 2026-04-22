<?php

namespace Rjds\PhpHumanize\Locale;

use InvalidArgumentException;

final class LocaleNormalizer
{
    public static function normalize(string $locale): string
    {
        $normalized = trim($locale);
        $normalized = str_replace('_', '-', $normalized);
        $collapsed = preg_replace('/-+/', '-', $normalized);
        $normalized = $collapsed ?? '';
        $parts = explode('-', $normalized);

        foreach ($parts as $index => $part) {
            if ($part === '') {
                $parts[$index] = '';
            } elseif ($index === 0) {
                $parts[$index] = strtolower($part);
            } elseif (self::isScriptCode($part)) {
                $parts[$index] = ucfirst(strtolower($part));
            } elseif (self::isRegionCode($part)) {
                $parts[$index] = strtoupper($part);
            } else {
                $parts[$index] = strtolower($part);
            }
        }

        return implode('-', $parts);
    }

    public static function normalizeOrDefault(string $locale, string $defaultLocale = 'en'): string
    {
        $normalized = self::normalize($locale);
        if ($normalized !== '') {
            return $normalized;
        }

        $fallback = self::normalize($defaultLocale);
        return $fallback !== '' ? $fallback : 'en';
    }

    public static function normalizeRequired(string $locale, string $field = 'locale'): string
    {
        $normalized = self::normalize($locale);
        if ($normalized === '') {
            throw new InvalidArgumentException(sprintf('%s cannot be empty.', $field));
        }

        return $normalized;
    }

    private static function isScriptCode(string $part): bool
    {
        return strlen($part) === 4 && ctype_alpha($part);
    }

    private static function isRegionCode(string $part): bool
    {
        return strlen($part) === 2 && ctype_alpha($part);
    }
}

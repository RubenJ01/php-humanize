<?php

namespace Rjds\PhpHumanize\Formatter;

final class LocaleMap
{
    public const DEFAULT_LOCALE = 'en';

    public static function normalize(string $locale): string
    {
        return strtolower(preg_replace('/[_-].*/', '', $locale) ?? '');
    }

    /**
     * @template TValue
     * @param array<string, TValue> $defaults
     * @param array<string, TValue> $overrides
     * @return array<string, TValue>
     */
    public static function withOverrides(array $defaults, array $overrides): array
    {
        $merged = $defaults;

        foreach ($overrides as $locale => $value) {
            $normalizedLocale = self::normalize($locale);

            if ($normalizedLocale === '') {
                continue;
            }

            $merged[$normalizedLocale] = $value;
        }

        return $merged;
    }
}

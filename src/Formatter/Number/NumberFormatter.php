<?php

namespace Rjds\PhpHumanize\Formatter\Number;

use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\LocaleMap;

class NumberFormatter implements FormatterInterface
{
    private const MIN_PRECISION = 0;

    private const LOCALE_FORMATS = [
        'en' => ['.', ','],
        'nl' => [',', '.'],
    ];

    /**
     * @var array<string, array{0: string, 1: string}>
     */
    private array $localeFormats;

    /**
     * @param array<string, array{0: string, 1: string}> $localeFormats
     */
    public function __construct(array $localeFormats = [])
    {
        $this->localeFormats = LocaleMap::withOverrides(self::LOCALE_FORMATS, $localeFormats);
    }

    public function format(...$args): string
    {
        $rawNumber = $args[0] ?? null;
        $rawPrecision = $args[1] ?? null;
        $rawLocale = $args[2] ?? DateFormatter::LOCALE_EN;

        $number = $this->normalizeNumber($rawNumber);
        $precision = $this->normalizePrecision($rawPrecision);

        if (!is_string($rawLocale) || trim($rawLocale) === '') {
            throw new \InvalidArgumentException('Third argument must be a non-empty locale string');
        }

        $language = LocaleMap::normalize($rawLocale);
        [$decimalSeparator, $thousandsSeparator] = $this->localeFormats[$language] ??
            $this->localeFormats[DateFormatter::LOCALE_EN];

        return number_format($number, $precision, $decimalSeparator, $thousandsSeparator);
    }

    public function getName(): string
    {
        return 'number';
    }

    private function normalizeNumber(mixed $value): float
    {
        if (!(is_scalar($value) && is_numeric($value))) {
            return 0.0;
        }

        return 0.0 + $value;
    }

    private function normalizePrecision(mixed $value): int
    {
        $normalized = is_scalar($value)
            ? (int) $value
            : self::MIN_PRECISION;

        return max(self::MIN_PRECISION, $normalized);
    }
}

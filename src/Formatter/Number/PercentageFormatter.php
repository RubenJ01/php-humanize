<?php

namespace Rjds\PhpHumanize\Formatter\Number;

use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\Intl\IntlFormatterBridge;

class PercentageFormatter implements FormatterInterface
{
    private const MIN_PRECISION = 0;
    private bool $preferIntl;

    public function __construct(bool $preferIntl = true)
    {
        $this->preferIntl = $preferIntl;
    }

    public function format(...$args): string
    {
        $rawValue = $args[0] ?? null;
        $rawPrecision = $args[1] ?? null;
        $rawLocale = $args[2] ?? DateFormatter::LOCALE_EN;
        $fromFraction = $this->normalizeFromFraction($args[3] ?? true);

        $value = $this->normalizeNumber($rawValue);
        $precision = $this->normalizePrecision($rawPrecision);

        if (!is_string($rawLocale) || trim($rawLocale) === '') {
            throw new \InvalidArgumentException('Third argument must be a non-empty locale string');
        }

        $percentageValue = $fromFraction ? $value * 100 : $value;

        if ($this->preferIntl) {
            return IntlFormatterBridge::formatDecimal($percentageValue, $precision, $rawLocale) . '%';
        }

        return IntlFormatterBridge::formatDecimal($percentageValue, $precision, DateFormatter::LOCALE_EN) . '%';
    }

    public function getName(): string
    {
        return 'percentage';
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

    private function normalizeFromFraction(mixed $value): bool
    {
        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            $value = !in_array($normalized, ['0', 'false', 'off', 'no'], true);
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value !== 0.0;
        }


        return true;
    }
}

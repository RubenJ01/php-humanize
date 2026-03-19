<?php

namespace Rjds\PhpHumanize\Formatter;

class DataRateFormatter implements FormatterInterface
{
    private const UNITS = ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s'];

    public function format(...$args): string
    {
        $rawBytesPerSecond = $args[0] ?? '';
        $rawPrecision = $args[1] ?? 1;

        $bytesPerSecond = is_scalar($rawBytesPerSecond)
            ? (int) $rawBytesPerSecond
            : 0;
        $precision = is_scalar($rawPrecision)
            ? (int) $rawPrecision
            : 1;

        $bytesPerSecond = max(0, $bytesPerSecond);

        $maxExponent = count(self::UNITS) - 1;
        $exponent = 0;
        $value = $bytesPerSecond;

        for (; $exponent < $maxExponent; $exponent++) {
            if ($value < 1024) {
                break;
            }

            $value /= 1024;
        }

        $formatted = number_format($value, $precision);
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . ' ' . self::UNITS[$exponent];
    }

    public function getName(): string
    {
        return 'dataRate';
    }
}

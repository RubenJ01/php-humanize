<?php

namespace Rjds\PhpHumanize\Formatter;

class FileSizeFormatter implements FormatterInterface
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    public function format(...$args): string
    {
        $rawBytes = $args[0] ?? '';
        $rawPrecision = $args[1] ?? 1;

        $bytes = is_scalar($rawBytes)
            ? (int) $rawBytes
            : 0;
        $precision = is_scalar($rawPrecision)
            ? (int) $rawPrecision
            : 1;

        $bytes = max(0, $bytes);

        $exponent = 0;
        $value = $bytes;

        while ($value >= 1024 && $exponent < count(self::UNITS) - 1) {
            $value /= 1024;
            $exponent++;
        }

        $formatted = number_format($value, $precision);
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . ' ' . self::UNITS[$exponent];
    }

    public function getName(): string
    {
        return 'fileSize';
    }
}

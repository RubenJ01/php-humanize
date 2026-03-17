<?php

namespace Rjds\PhpHumanize\Formatter;

class DataRateFormatter
{
    private const UNITS = ['B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s', 'PB/s'];

    public function format(int $bytesPerSecond, int $precision = 1): string
    {
        $bytesPerSecond = max(0, $bytesPerSecond);

        $exponent = 0;
        $value = $bytesPerSecond;

        while ($value >= 1024 && $exponent < count(self::UNITS) - 1) {
            $value /= 1024;
            $exponent++;
        }

        $formatted = number_format($value, $precision);
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . ' ' . self::UNITS[$exponent];
    }
}

<?php

namespace Rjds\PhpHumanize\Formatter;

class FileSizeFormatter
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    public function format(int $bytes, int $precision = 1): string
    {
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
}

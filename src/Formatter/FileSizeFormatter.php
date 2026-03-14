<?php

namespace Rjds\PhpHumanize\Formatter;

class FileSizeFormatter
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    public function format(int $bytes, int $precision = 1): string
    {
        $bytes = max(0, $bytes);

        if ($bytes === 0) {
            return '0 B';
        }

        $exponent = (int) floor(log($bytes, 1024));
        $exponent = min($exponent, count(self::UNITS) - 1);

        $value = $bytes / (1024 ** $exponent);
        $formatted = number_format($value, $precision);
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . ' ' . self::UNITS[$exponent];
    }
}

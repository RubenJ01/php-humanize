<?php

namespace Rjds\PhpHumanize\Formatter;

class AbbreviationFormatter
{
    private const SUFFIXES = [
        1_000_000_000_000 => 'T',
        1_000_000_000 => 'B',
        1_000_000 => 'M',
        1_000 => 'K',
    ];

    public function format(float|int $number, int $precision = 1): string
    {
        foreach (self::SUFFIXES as $threshold => $suffix) {
            if (abs($number) >= $threshold) {
                $value = $number / $threshold;
                $formatted = number_format($value, $precision);
                $formatted = rtrim(rtrim($formatted, '0'), '.');

                return $formatted . $suffix;
            }
        }

        return "{$number}";
    }
}

<?php

namespace Rjds\PhpHumanize\Formatter;

class AbbreviationFormatter implements FormatterInterface
{
    private const SUFFIXES = [
        1_000_000_000_000 => 'T',
        1_000_000_000 => 'B',
        1_000_000 => 'M',
        1_000 => 'K',
    ];

    public function format(...$args): string
    {
        $rawNumber = $args[0] ?? 0;
        $rawPrecision = $args[1] ?? 1;

        if (is_scalar($rawNumber) && is_numeric($rawNumber)) {
            $number = (float) $rawNumber;
        } else {
            $number = 0.0;
        }

        $precision = is_scalar($rawPrecision)
            ? (int) $rawPrecision
            : 1;

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

    public function getName(): string
    {
        return 'abbreviate';
    }
}

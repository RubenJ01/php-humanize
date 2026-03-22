<?php

namespace Rjds\PhpHumanize\Formatter\Data;

use Rjds\PhpHumanize\Formatter\FormatterInterface;

class FileSizeFormatter implements FormatterInterface
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    public function format(...$args): string
    {
        $rawBytes = $args[0] ?? '';
        $rawPrecision = $args[1] ?? 1;

        $bytes = is_scalar($rawBytes)
            ? max(0, (int) $rawBytes)
            : 0;
        $precision = is_scalar($rawPrecision)
            ? (int) $rawPrecision
            : 1;


        $maxExponent = count(self::UNITS) - 1;
        $exponent = 0;
        $value = $bytes;

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
        return 'fileSize';
    }
}

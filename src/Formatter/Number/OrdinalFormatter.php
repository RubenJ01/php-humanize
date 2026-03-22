<?php

namespace Rjds\PhpHumanize\Formatter\Number;

use Rjds\PhpHumanize\Formatter\FormatterInterface;

class OrdinalFormatter implements FormatterInterface
{
    public function format(...$args): string
    {
        $rawNumber = $args[0] ?? 0;
        $number = is_scalar($rawNumber)
            ? (int) $rawNumber
            : 0;

        $abs = abs($number);
        $lastTwo = $abs % 100;
        $lastOne = $abs % 10;

        if ($lastTwo >= 11 && $lastTwo <= 13) {
            $suffix = 'th';
        } else {
            $suffix = match ($lastOne) {
                1 => 'st',
                2 => 'nd',
                3 => 'rd',
                default => 'th',
            };
        }

        return $number . $suffix;
    }

    public function getName(): string
    {
        return 'ordinal';
    }
}

<?php

namespace Rjds\PhpHumanize\Formatter;

class OrdinalFormatter
{
    public function format(int $number): string
    {
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
}

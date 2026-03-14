<?php

namespace Rjds\PhpHumanize\Formatter;

class NumberToWordsFormatter
{
    private const ONES = [
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen',
    ];

    private const TENS = [
        2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
        6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety',
    ];

    /** @var array<int, string> */
    private const SCALES = [
        1_000_000_000_000 => 'trillion',
        1_000_000_000 => 'billion',
        1_000_000 => 'million',
        1_000 => 'thousand',
    ];

    public function format(int $number): string
    {
        if ($number < 0) {
            return 'negative ' . $this->format(abs($number));
        }

        if ($number < 20) {
            return self::ONES[$number];
        }

        if ($number < 100) {
            $ten = self::TENS[(int) ($number / 10)];
            $one = $number % 10;

            return $one === 0 ? $ten : $ten . '-' . self::ONES[$one];
        }

        if ($number < 1000) {
            $hundreds = self::ONES[(int) ($number / 100)] . ' hundred';
            $remainder = $number % 100;

            return $remainder === 0 ? $hundreds : $hundreds . ' ' . $this->format($remainder);
        }

        return $this->convertLargeNumber($number);
    }

    private function convertLargeNumber(int $number): string
    {
        $parts = [];

        foreach (self::SCALES as $threshold => $label) {
            if ($number >= $threshold) {
                $count = (int) ($number / $threshold);
                $parts[] = $this->format($count) . ' ' . $label;
                $number %= $threshold;
            }
        }

        if ($number > 0) {
            $parts[] = $this->format($number);
        }

        return implode(', ', $parts);
    }
}

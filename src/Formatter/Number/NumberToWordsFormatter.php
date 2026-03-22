<?php

namespace Rjds\PhpHumanize\Formatter\Number;

use Rjds\PhpHumanize\Formatter\FormatterInterface;

class NumberToWordsFormatter implements FormatterInterface
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
        1_000_000_000_000_000_000 => 'quintillion',
        1_000_000_000_000_000 => 'quadrillion',
        1_000_000_000_000 => 'trillion',
        1_000_000_000 => 'billion',
        1_000_000 => 'million',
        1_000 => 'thousand',
    ];

    public function format(...$args): string
    {
        $rawNumber = $args[0] ?? 0;
        $number = is_scalar($rawNumber)
            ? (int) $rawNumber
            : 0;

        $prefix = '';

        if ($number < 0) {
            $prefix = 'negative ';
            $number = abs($number);
        }

        return $prefix . $this->formatNonNegative($number);
    }

    private function formatNonNegative(int $number): string
    {
        return $number < 1000
            ? $this->formatSubThousand($number)
            : $this->convertLargeNumber($number);
    }

    private function convertLargeNumber(int $number): string
    {
        $parts = [];

        foreach (self::SCALES as $threshold => $label) {
            if ($number >= $threshold) {
                $count = intdiv($number, $threshold);
                $parts[] = $this->formatSubThousand($count) . ' ' . $label;
                $number %= $threshold;
            }
        }

        if ($number > 0) {
            $parts[] = $this->formatSubThousand($number);
        }

        return implode(', ', $parts);
    }

    private function formatSubThousand(int $number): string
    {
        if ($number < 100) {
            return $this->formatSubHundred($number);
        }

        if ($number <= 999) {
            $hundreds = self::ONES[intdiv($number, 100)] . ' hundred';
            $remainder = $number % 100;

            return $remainder === 0 ? $hundreds : $hundreds . ' ' . $this->formatSubHundred($remainder);
        }

        throw new \InvalidArgumentException('Sub-thousand formatter only accepts values from 0 to 999');
    }

    private function formatSubHundred(int $number): string
    {
        if ($number >= 20) {
            $ten = self::TENS[intdiv($number, 10)];
            $one = $number % 10;

            return $one === 0 ? $ten : $ten . '-' . self::ONES[$one];
        }

        if ($number >= 0) {
            return self::ONES[$number];
        }

        throw new \InvalidArgumentException('Number must be non-negative');
    }

    public function getName(): string
    {
        return 'toWords';
    }
}

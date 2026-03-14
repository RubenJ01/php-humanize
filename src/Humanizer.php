<?php

namespace Rjds\PhpHumanize;

use DateTimeImmutable;
use DateTimeInterface;

class Humanizer implements HumanizerInterface
{
    private const FILE_SIZE_UNITS = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    private const ABBREVIATION_SUFFIXES = [
        1_000_000_000_000 => 'T',
        1_000_000_000 => 'B',
        1_000_000 => 'M',
        1_000 => 'K',
    ];

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

    public function fileSize(int $bytes, int $precision = 1): string
    {
        $bytes = max(0, $bytes);

        if ($bytes === 0) {
            return '0 B';
        }

        $exponent = (int) floor(log($bytes, 1024));
        $exponent = min($exponent, count(self::FILE_SIZE_UNITS) - 1);

        $value = $bytes / (1024 ** $exponent);
        $formatted = number_format($value, $precision);
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . ' ' . self::FILE_SIZE_UNITS[$exponent];
    }

    public function ordinal(int $number): string
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

    public function abbreviate(float|int $number, int $precision = 1): string
    {
        foreach (self::ABBREVIATION_SUFFIXES as $threshold => $suffix) {
            if (abs($number) >= $threshold) {
                $value = $number / $threshold;
                $formatted = number_format($value, $precision);
                $formatted = rtrim(rtrim($formatted, '0'), '.');

                return $formatted . $suffix;
            }
        }

        return (string) $number;
    }

    public function diffForHumans(DateTimeInterface $dateTime, ?DateTimeInterface $relativeTo = null): string
    {
        $relativeTo = $relativeTo ?? new DateTimeImmutable();
        $diff = $relativeTo->diff($dateTime);
        $isFuture = $diff->invert === 0;

        if ($diff->y > 0) {
            $label = $diff->y === 1 ? 'year' : 'years';
            $value = $diff->y . ' ' . $label;
        } elseif ($diff->m > 0) {
            $label = $diff->m === 1 ? 'month' : 'months';
            $value = $diff->m . ' ' . $label;
        } elseif ($diff->d >= 7) {
            $weeks = (int) floor($diff->d / 7);
            $label = $weeks === 1 ? 'week' : 'weeks';
            $value = $weeks . ' ' . $label;
        } elseif ($diff->d > 0) {
            $label = $diff->d === 1 ? 'day' : 'days';
            $value = $diff->d . ' ' . $label;
        } elseif ($diff->h > 0) {
            $label = $diff->h === 1 ? 'hour' : 'hours';
            $value = $diff->h . ' ' . $label;
        } elseif ($diff->i > 0) {
            $label = $diff->i === 1 ? 'minute' : 'minutes';
            $value = $diff->i . ' ' . $label;
        } else {
            return 'just now';
        }

        return $isFuture ? 'in ' . $value : $value . ' ago';
    }

    /**
     * @param array<int, string> $items
     */
    public function joinList(array $items, string $conjunction = 'and', string $separator = ', '): string
    {
        $count = count($items);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $items[0];
        }

        if ($count === 2) {
            return $items[0] . ' ' . $conjunction . ' ' . $items[1];
        }

        $last = array_pop($items);

        return implode($separator, $items) . $separator . $conjunction . ' ' . $last;
    }

    public function pluralize(int $quantity, string $singular, ?string $plural = null): string
    {
        if ($quantity === 1) {
            return $quantity . ' ' . $singular;
        }

        $pluralForm = $plural ?? $singular . 's';

        return $quantity . ' ' . $pluralForm;
    }

    public function toWords(int $number): string
    {
        if ($number < 0) {
            return 'negative ' . $this->toWords(abs($number));
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

            return $remainder === 0 ? $hundreds : $hundreds . ' ' . $this->toWords($remainder);
        }

        return $this->convertLargeNumber($number);
    }

    private function convertLargeNumber(int $number): string
    {
        $parts = [];

        foreach (self::SCALES as $threshold => $label) {
            if ($number >= $threshold) {
                $count = (int) ($number / $threshold);
                $parts[] = $this->toWords($count) . ' ' . $label;
                $number %= $threshold;
            }
        }

        if ($number > 0) {
            $parts[] = $this->toWords($number);
        }

        return implode(', ', $parts);
    }
}

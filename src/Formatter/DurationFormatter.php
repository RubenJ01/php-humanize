<?php

namespace Rjds\PhpHumanize\Formatter;

class DurationFormatter
{
    /** @var array<int, array{int, string, string}> */
    private const UNITS = [
        [86400, 'day', 'days'],
        [3600, 'hour', 'hours'],
        [60, 'minute', 'minutes'],
        [1, 'second', 'seconds'],
    ];

    public function format(int $seconds, ?int $precision = null): string
    {
        $seconds = abs($seconds);

        if ($seconds === 0) {
            return '0 seconds';
        }

        $parts = [];

        foreach (self::UNITS as [$unitSeconds, $singular, $plural]) {
            if ($seconds >= $unitSeconds) {
                $count = (int) floor($seconds / $unitSeconds);
                $seconds %= $unitSeconds;
                $label = $count === 1 ? $singular : $plural;
                $parts[] = $count . ' ' . $label;
            }
        }

        if ($precision !== null) {
            $parts = array_slice($parts, 0, $precision);
        }

        return implode(', ', $parts);
    }
}

<?php

namespace Rjds\PhpHumanize\Formatter;

class DurationFormatter implements FormatterInterface
{
    /** @var array<int, array{int, string, string}> */
    private const UNITS = [
        [86400, 'day', 'days'],
        [3600, 'hour', 'hours'],
        [60, 'minute', 'minutes'],
        [1, 'second', 'seconds'],
    ];

    public function format(...$args): string
    {
        $rawSeconds = $args[0] ?? 0;
        $seconds = is_scalar($rawSeconds)
            ? (int) $rawSeconds
            : 0;

        if (!array_key_exists(1, $args)) {
            $precision = null;
        } else {
            $rawPrecision = $args[1];
            if ($rawPrecision === null) {
                $precision = null;
            } else {
                $precision = is_scalar($rawPrecision)
                    ? (int) $rawPrecision
                    : null;
            }
        }

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

    public function getName(): string
    {
        return 'duration';
    }
}

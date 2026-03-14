<?php

namespace Rjds\PhpHumanize\Formatter;

use DateTimeImmutable;
use DateTimeInterface;

class TimeDiffFormatter
{
    public function format(DateTimeInterface $dateTime, ?DateTimeInterface $relativeTo = null): string
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
}

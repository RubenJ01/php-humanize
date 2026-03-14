<?php

namespace Rjds\PhpHumanize;

use DateTimeInterface;

interface HumanizerInterface
{
    /**
     * Convert bytes to a human-readable file size.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public function fileSize(int $bytes, int $precision = 1): string;

    /**
     * Convert a number to its ordinal form.
     *
     * @param int $number
     * @return string
     */
    public function ordinal(int $number): string;

    /**
     * Abbreviate a large number to a short form.
     *
     * @param float|int $number
     * @param int $precision
     * @return string
     */
    public function abbreviate(float|int $number, int $precision = 1): string;

    /**
     * Express a datetime as a human-readable difference from now.
     *
     * @param DateTimeInterface $dateTime
     * @param DateTimeInterface|null $relativeTo
     * @return string
     */
    public function diffForHumans(DateTimeInterface $dateTime, ?DateTimeInterface $relativeTo = null): string;

    /**
     * Join a list of items into a human-readable string.
     *
     * @param array<int, string> $items
     * @param string $conjunction
     * @param string $separator
     * @return string
     */
    public function joinList(array $items, string $conjunction = 'and', string $separator = ', '): string;
}

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
     * Convert bytes per second to a human-readable data rate.
     *
     * @param int $bytesPerSecond
     * @param int $precision
     * @return string
     */
    public function dataRate(int $bytesPerSecond, int $precision = 1): string;

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

    /**
     * Convert a quantity and noun into a correctly pluralized string.
     *
     * @param int $quantity
     * @param string $singular
     * @param string|null $plural
     * @return string
     */
    public function pluralize(int $quantity, string $singular, ?string $plural = null): string;

    /**
     * Convert a number into its written word form.
     *
     * @param int $number
     * @return string
     */
    public function toWords(int $number): string;

    /**
     * Convert a number of seconds into a human-readable duration string.
     *
     * @param int $seconds
     * @param int|null $precision
     * @return string
     */
    public function duration(int $seconds, ?int $precision = null): string;
}

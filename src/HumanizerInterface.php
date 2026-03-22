<?php

namespace Rjds\PhpHumanize;

use DateTimeInterface;
use Rjds\PhpHumanize\Formatter\FormatterInterface;

interface HumanizerInterface
{
    public const LOCALE_EN = 'en';
    public const LOCALE_NL = 'nl';
    public const DEFAULT_NUMBER_PRECISION = 0;

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
     * Format a number with locale-aware thousand and decimal separators.
     *
     * @param float|int $number
     * @param int|null $precision Defaults to configured precision when null.
     * @param string|null $locale Locale identifier like en, en_US, nl, or nl_NL.
     *                            Defaults to configured locale when null.
     * @return string
     */
    public function number(
        float|int $number,
        ?int $precision = null,
        ?string $locale = null
    ): string;

    /**
     * Format a value as a locale-aware percentage string.
     *
     * @param float|int $value
     * @param int|null $precision Defaults to configured precision when null.
     * @param string|null $locale Locale identifier like en, en_US, nl, or nl_NL.
     *                            Defaults to configured locale when null.
     * @param bool $fromFraction When true, value is treated as a ratio (0.42 => 42%).
     * @return string
     */
    public function percentage(
        float|int $value,
        ?int $precision = null,
        ?string $locale = null,
        bool $fromFraction = true
    ): string;

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
     * @param string|null $conjunction Defaults to configured conjunction when null.
     * @param string $separator
     * @return string
     */
    public function joinList(array $items, ?string $conjunction = null, string $separator = ', '): string;

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

    /**
     * Truncate text at a word boundary and append a suffix.
     *
     * @param string $text
     * @param int $maxLength Maximum number of characters from the original text.
     * @param string|null $suffix Defaults to configured suffix when null.
     * @return string
     */
    public function truncate(string $text, int $maxLength, ?string $suffix = null): string;

    /**
     * Format a date as a human-readable localized string.
     *
     * @param DateTimeInterface $dateTime
     * @param string|null $locale Locale identifier like en, en_US, nl, or nl_NL. Defaults to self::LOCALE_EN when null.
     * @return string
     */
    public function readableDate(DateTimeInterface $dateTime, ?string $locale = null): string;

    /**
     * Get the formatter registry for advanced usage.
     *
     * @return FormatterRegistry
     */
    public function getRegistry(): FormatterRegistry;

    /**
     * Register a custom formatter at runtime.
     *
     * @param string $name The formatter name
     * @param FormatterInterface $formatter The formatter instance
     * @return self For fluent interface
     */
    public function register(string $name, FormatterInterface $formatter): self;

    /**
     * Apply a formatter dynamically by name.
     *
     * @param string $formatterName The name of the registered formatter
     * @param mixed ...$args Arguments to pass to the formatter
     * @return string The formatted result
     */
    public function apply(string $formatterName, ...$args): string;
}

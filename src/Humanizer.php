<?php

namespace Rjds\PhpHumanize;

use DateTimeInterface;
use Rjds\PhpHumanize\Formatter\AbbreviationFormatter;
use Rjds\PhpHumanize\Formatter\DataRateFormatter;
use Rjds\PhpHumanize\Formatter\DateLocalizedFormatter;
use Rjds\PhpHumanize\Formatter\DurationFormatter;
use Rjds\PhpHumanize\Formatter\FileSizeFormatter;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\ListJoinFormatter;
use Rjds\PhpHumanize\Formatter\NumberToWordsFormatter;
use Rjds\PhpHumanize\Formatter\OrdinalFormatter;
use Rjds\PhpHumanize\Formatter\PluralizeFormatter;
use Rjds\PhpHumanize\Formatter\TextTruncationFormatter;
use Rjds\PhpHumanize\Formatter\TimeDiffFormatter;

class Humanizer implements HumanizerInterface
{
    public const LOCALE_EN = DateLocalizedFormatter::LOCALE_EN;
    public const LOCALE_NL = DateLocalizedFormatter::LOCALE_NL;

    private FormatterRegistry $registry;

    /**
     * Constructor accepts optional formatters for dependency injection.
     * If no formatters are provided, default formatters are auto-registered.
     */
    public function __construct(
        ?FileSizeFormatter $fileSizeFormatter = null,
        ?DataRateFormatter $dataRateFormatter = null,
        ?OrdinalFormatter $ordinalFormatter = null,
        ?AbbreviationFormatter $abbreviationFormatter = null,
        ?TimeDiffFormatter $timeDiffFormatter = null,
        ?ListJoinFormatter $listJoinFormatter = null,
        ?PluralizeFormatter $pluralizeFormatter = null,
        ?NumberToWordsFormatter $numberToWordsFormatter = null,
        ?DurationFormatter $durationFormatter = null,
        ?TextTruncationFormatter $textTruncationFormatter = null,
        ?DateLocalizedFormatter $dateLocalizedFormatter = null,
    ) {
        $this->registry = new FormatterRegistry();

        // Register formatters - use provided instances or create defaults
        $this->registry->register('fileSize', $fileSizeFormatter ?? new FileSizeFormatter());
        $this->registry->register('dataRate', $dataRateFormatter ?? new DataRateFormatter());
        $this->registry->register('ordinal', $ordinalFormatter ?? new OrdinalFormatter());
        $this->registry->register('abbreviate', $abbreviationFormatter ?? new AbbreviationFormatter());
        $this->registry->register('diffForHumans', $timeDiffFormatter ?? new TimeDiffFormatter());
        $this->registry->register('joinList', $listJoinFormatter ?? new ListJoinFormatter());
        $this->registry->register('pluralize', $pluralizeFormatter ?? new PluralizeFormatter());
        $this->registry->register('toWords', $numberToWordsFormatter ?? new NumberToWordsFormatter());
        $this->registry->register('duration', $durationFormatter ?? new DurationFormatter());
        $this->registry->register('truncate', $textTruncationFormatter ?? new TextTruncationFormatter());
        $this->registry->register('readableDate', $dateLocalizedFormatter ?? new DateLocalizedFormatter());
    }

    /**
     * Get the formatter registry for advanced usage (registering custom formatters, etc).
     */
    public function getRegistry(): FormatterRegistry
    {
        return $this->registry;
    }

    /**
     * Register a custom formatter at runtime.
     *
     * @param string $name The formatter name
     * @param FormatterInterface $formatter The formatter instance
     * @return self For fluent interface
     */
    public function register(string $name, FormatterInterface $formatter): self
    {
        $this->registry->register($name, $formatter);
        return $this;
    }

    public function fileSize(int $bytes, int $precision = 1): string
    {
        return $this->registry->get('fileSize')->format($bytes, $precision);
    }

    public function dataRate(int $bytesPerSecond, int $precision = 1): string
    {
        return $this->registry->get('dataRate')->format($bytesPerSecond, $precision);
    }

    public function ordinal(int $number): string
    {
        return $this->registry->get('ordinal')->format($number);
    }

    public function abbreviate(float|int $number, int $precision = 1): string
    {
        return $this->registry->get('abbreviate')->format($number, $precision);
    }

    public function diffForHumans(DateTimeInterface $dateTime, ?DateTimeInterface $relativeTo = null): string
    {
        return $this->registry->get('diffForHumans')->format($dateTime, $relativeTo);
    }

    /**
     * @param array<int, string> $items
     */
    public function joinList(array $items, string $conjunction = 'and', string $separator = ', '): string
    {
        return $this->registry->get('joinList')->format($items, $conjunction, $separator);
    }

    public function pluralize(int $quantity, string $singular, ?string $plural = null): string
    {
        return $this->registry->get('pluralize')->format($quantity, $singular, $plural);
    }

    public function toWords(int $number): string
    {
        return $this->registry->get('toWords')->format($number);
    }

    public function duration(int $seconds, ?int $precision = null): string
    {
        return $this->registry->get('duration')->format($seconds, $precision);
    }

    public function truncate(string $text, int $maxLength, string $suffix = '…'): string
    {
        return $this->registry->get('truncate')->format($text, $maxLength, $suffix);
    }

    public function readableDate(DateTimeInterface $dateTime, ?string $locale = null): string
    {
        return $this->registry->get('readableDate')->format($dateTime, $locale ?? self::LOCALE_EN);
    }

    /**
     * Universal formatter method for dynamic usage.
     * This allows calling any registered formatter with arbitrary arguments.
     *
     * @param string $formatterName The name of the registered formatter
     * @param mixed ...$args Arguments to pass to the formatter
     * @return string The formatted result
     *
     * @example $humanizer->apply('fileSize', 1024)
     * @example $humanizer->apply('pluralize', 5, 'apple', 'apples')
     */
    public function apply(string $formatterName, ...$args): string
    {
        return $this->registry->get($formatterName)->format(...$args);
    }

    /**
     * Magic method to allow calling formatters as methods.
     * This enables dynamic method-like syntax for registered formatters.
     *
     * @example $humanizer->customFormatter($arg1, $arg2)
     * @param array<int, mixed> $args
     */
    public function __call(string $method, array $args): string
    {
        return $this->registry->get($method)->format(...$args);
    }
}

<?php

namespace Rjds\PhpHumanize;

use DateTimeInterface;
use Rjds\PhpHumanize\Formatter\Data\DataRateFormatter;
use Rjds\PhpHumanize\Formatter\Data\FileSizeFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DurationFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\TimeDiffFormatter;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\Number\AbbreviationFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberToWordsFormatter;
use Rjds\PhpHumanize\Formatter\Number\OrdinalFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;
use Rjds\PhpHumanize\Formatter\Text\ListJoinFormatter;
use Rjds\PhpHumanize\Formatter\Text\PluralizeFormatter;
use Rjds\PhpHumanize\Formatter\Text\TextTruncationFormatter;

class Humanizer implements HumanizerInterface
{
    public const LOCALE_EN = DateFormatter::LOCALE_EN;
    public const LOCALE_NL = DateFormatter::LOCALE_NL;

    private FormatterRegistry $registry;
    private HumanizerConfig $config;

    /**
     * Constructor accepts optional formatters for dependency injection and optional defaults via config.
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
        ?DateFormatter $dateFormatter = null,
        ?NumberFormatter $numberFormatter = null,
        ?HumanizerConfig $config = null,
        ?PercentageFormatter $percentageFormatter = null,
    ) {
        $this->registry = new FormatterRegistry();
        $this->config = $config ?? new HumanizerConfig();

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
        $this->registry->register('readableDate', $dateFormatter ?? new DateFormatter());
        $this->registry->register('number', $numberFormatter ?? new NumberFormatter());
        $this->registry->register('percentage', $percentageFormatter ?? new PercentageFormatter());
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
    public function joinList(array $items, ?string $conjunction = null, string $separator = ', '): string
    {
        return $this->registry->get('joinList')->format(
            $items,
            $conjunction ?? $this->config->getListConjunction(),
            $separator
        );
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

    public function truncate(string $text, int $maxLength, ?string $suffix = null): string
    {
        return $this->registry->get('truncate')->format(
            $text,
            $maxLength,
            $suffix ?? $this->config->getTruncateSuffix()
        );
    }

    public function readableDate(DateTimeInterface $dateTime, ?string $locale = null): string
    {
        return $this->registry->get('readableDate')->format($dateTime, $locale ?? $this->config->getLocale());
    }

    public function number(
        float|int $number,
        ?int $precision = null,
        ?string $locale = null
    ): string {
        return $this->registry->get('number')->format(
            $number,
            $precision ?? $this->config->getNumberPrecision(),
            $locale ?? $this->config->getLocale()
        );
    }

    public function percentage(
        float|int $value,
        ?int $precision = null,
        ?string $locale = null,
        bool $fromFraction = true
    ): string {
        return $this->registry->get('percentage')->format(
            $value,
            $precision ?? $this->config->getNumberPrecision(),
            $locale ?? $this->config->getLocale(),
            $fromFraction
        );
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
     * @param array<int, mixed> $args
     * @example $humanizer->customFormatter($arg1, $arg2)
     */
    public function __call(string $method, array $args): string
    {
        return $this->registry->get($method)->format(...$args);
    }
}

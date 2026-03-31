<?php

namespace Rjds\PhpHumanize;

use InvalidArgumentException;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\Data\DataRateFormatter;
use Rjds\PhpHumanize\Formatter\Data\FileSizeFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DurationFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\TimeDiffFormatter;
use Rjds\PhpHumanize\Formatter\Number\AbbreviationFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberToWordsFormatter;
use Rjds\PhpHumanize\Formatter\Number\OrdinalFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;
use Rjds\PhpHumanize\Formatter\Text\ListJoinFormatter;
use Rjds\PhpHumanize\Formatter\Text\PluralizeFormatter;
use Rjds\PhpHumanize\Formatter\Text\TextTruncationFormatter;

final class HumanizerFactory
{
    /**
     * Create a configured {@see Humanizer} with the built-in formatters registered.
     *
     * @param HumanizerConfig|null $config Defaults for locale, precision, conjunction and truncation.
     * @param array<array-key, mixed> $formatters Map of formatter name => formatter instance.
     *                                                       Existing built-ins will be overridden when names match.
     */
    public static function create(?HumanizerConfig $config = null, array $formatters = []): Humanizer
    {
        $registry = self::createDefaultRegistry();

        foreach ($formatters as $name => $formatter) {
            if (!is_string($name) || $name === '') {
                throw new InvalidArgumentException('Formatter name must be a non-empty string.');
            }

            if (!$formatter instanceof FormatterInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Formatter for "%s" must implement %s.',
                    $name,
                    FormatterInterface::class
                ));
            }

            $registry->register($name, $formatter);
        }

        return new Humanizer(config: $config, registry: $registry);
    }

    private static function createDefaultRegistry(): FormatterRegistry
    {
        $registry = new FormatterRegistry();

        $registry->register('fileSize', new FileSizeFormatter());
        $registry->register('dataRate', new DataRateFormatter());
        $registry->register('ordinal', new OrdinalFormatter());
        $registry->register('abbreviate', new AbbreviationFormatter());
        $registry->register('diffForHumans', new TimeDiffFormatter());
        $registry->register('joinList', new ListJoinFormatter());
        $registry->register('pluralize', new PluralizeFormatter());
        $registry->register('toWords', new NumberToWordsFormatter());
        $registry->register('duration', new DurationFormatter());
        $registry->register('truncate', new TextTruncationFormatter());
        $registry->register('readableDate', new DateFormatter());
        $registry->register('number', new NumberFormatter());
        $registry->register('percentage', new PercentageFormatter());

        return $registry;
    }
}

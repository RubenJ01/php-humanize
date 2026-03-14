<?php

namespace Rjds\PhpHumanize;

use DateTimeInterface;
use Rjds\PhpHumanize\Formatter\AbbreviationFormatter;
use Rjds\PhpHumanize\Formatter\FileSizeFormatter;
use Rjds\PhpHumanize\Formatter\ListJoinFormatter;
use Rjds\PhpHumanize\Formatter\NumberToWordsFormatter;
use Rjds\PhpHumanize\Formatter\OrdinalFormatter;
use Rjds\PhpHumanize\Formatter\PluralizeFormatter;
use Rjds\PhpHumanize\Formatter\TimeDiffFormatter;

class Humanizer implements HumanizerInterface
{
    public function __construct(
        private readonly FileSizeFormatter $fileSizeFormatter = new FileSizeFormatter(),
        private readonly OrdinalFormatter $ordinalFormatter = new OrdinalFormatter(),
        private readonly AbbreviationFormatter $abbreviationFormatter = new AbbreviationFormatter(),
        private readonly TimeDiffFormatter $timeDiffFormatter = new TimeDiffFormatter(),
        private readonly ListJoinFormatter $listJoinFormatter = new ListJoinFormatter(),
        private readonly PluralizeFormatter $pluralizeFormatter = new PluralizeFormatter(),
        private readonly NumberToWordsFormatter $numberToWordsFormatter = new NumberToWordsFormatter(),
    ) {
    }

    public function fileSize(int $bytes, int $precision = 1): string
    {
        return $this->fileSizeFormatter->format($bytes, $precision);
    }

    public function ordinal(int $number): string
    {
        return $this->ordinalFormatter->format($number);
    }

    public function abbreviate(float|int $number, int $precision = 1): string
    {
        return $this->abbreviationFormatter->format($number, $precision);
    }

    public function diffForHumans(DateTimeInterface $dateTime, ?DateTimeInterface $relativeTo = null): string
    {
        return $this->timeDiffFormatter->format($dateTime, $relativeTo);
    }

    /**
     * @param array<int, string> $items
     */
    public function joinList(array $items, string $conjunction = 'and', string $separator = ', '): string
    {
        return $this->listJoinFormatter->format($items, $conjunction, $separator);
    }

    public function pluralize(int $quantity, string $singular, ?string $plural = null): string
    {
        return $this->pluralizeFormatter->format($quantity, $singular, $plural);
    }

    public function toWords(int $number): string
    {
        return $this->numberToWordsFormatter->format($number);
    }
}

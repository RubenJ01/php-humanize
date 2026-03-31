<?php

namespace Rjds\PhpHumanize\Formatter\DateTime;

use DateTimeInterface;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\Intl\IntlFormatterBridge;

class DateFormatter implements FormatterInterface
{
    public const LOCALE_EN = 'en';
    public const LOCALE_NL = 'nl';
    private bool $preferIntl;

    public function __construct(bool $preferIntl = true)
    {
        $this->preferIntl = $preferIntl;
    }

    public function format(...$args): string
    {
        $dateTime = $args[0] ?? null;
        $locale = $args[1] ?? self::LOCALE_EN;

        if (!($dateTime instanceof DateTimeInterface)) {
            throw new \InvalidArgumentException('First argument must be a DateTimeInterface');
        }

        if (!is_string($locale) || trim($locale) === '') {
            throw new \InvalidArgumentException('Second argument must be a non-empty locale string');
        }

        if ($this->preferIntl) {
            return IntlFormatterBridge::formatDate($dateTime, $locale);
        }

        return IntlFormatterBridge::formatDate($dateTime, self::LOCALE_EN);
    }

    public function getName(): string
    {
        return 'readableDate';
    }
}

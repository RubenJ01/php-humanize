<?php

namespace Rjds\PhpHumanize\Formatter\Text;

use Rjds\PhpHumanize\Formatter\FormatterInterface;

class PluralizeFormatter implements FormatterInterface
{
    public function format(...$args): string
    {
        $rawQuantity = $args[0] ?? 0;
        $quantity = is_scalar($rawQuantity)
            ? (int) $rawQuantity
            : 0;
        $singular = $args[1] ?? '';
        $plural = $args[2] ?? null;

        if (!is_string($singular)) {
            throw new \InvalidArgumentException('Singular form must be a string');
        }

        if ($plural !== null && !is_string($plural)) {
            throw new \InvalidArgumentException('Plural form must be a string or null');
        }

        if ($quantity === 1) {
            return $quantity . ' ' . $singular;
        }

        $pluralForm = $plural ?? $singular . 's';

        return $quantity . ' ' . $pluralForm;
    }

    public function getName(): string
    {
        return 'pluralize';
    }
}

<?php

namespace Rjds\PhpHumanize\Formatter;

class PluralizeFormatter
{
    public function format(int $quantity, string $singular, ?string $plural = null): string
    {
        if ($quantity === 1) {
            return $quantity . ' ' . $singular;
        }

        $pluralForm = $plural ?? $singular . 's';

        return $quantity . ' ' . $pluralForm;
    }
}

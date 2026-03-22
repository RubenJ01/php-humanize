<?php

namespace Rjds\PhpHumanize\Formatter\Text;

use Rjds\PhpHumanize\Formatter\FormatterInterface;

class ListJoinFormatter implements FormatterInterface
{
    /**
     * @param mixed ...$args
     */
    public function format(...$args): string
    {
        $items = isset($args[0]) && is_array($args[0]) ? $args[0] : [];
        $conjunction = $args[1] ?? 'and';
        $separator = $args[2] ?? ', ';

        if (!is_string($conjunction)) {
            throw new \InvalidArgumentException('Conjunction must be a string');
        }

        if (!is_string($separator)) {
            throw new \InvalidArgumentException('Separator must be a string');
        }

        foreach ($items as $item) {
            if (!is_string($item)) {
                throw new \InvalidArgumentException('All list items must be strings');
            }
        }

        /** @var array<int, string> $items */
        $items = array_values($items);

        $count = count($items);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $items[0];
        }

        if ($count === 2) {
            return $items[0] . ' ' . $conjunction . ' ' . $items[1];
        }

        $last = array_pop($items);

        return implode($separator, $items) . $separator . $conjunction . ' ' . $last;
    }

    public function getName(): string
    {
        return 'joinList';
    }
}

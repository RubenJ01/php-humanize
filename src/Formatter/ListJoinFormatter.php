<?php

namespace Rjds\PhpHumanize\Formatter;

class ListJoinFormatter
{
    /**
     * @param array<int, string> $items
     */
    public function format(array $items, string $conjunction = 'and', string $separator = ', '): string
    {
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
}

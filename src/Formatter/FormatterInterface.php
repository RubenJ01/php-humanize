<?php

namespace Rjds\PhpHumanize\Formatter;

/**
 * Interface for all formatters in the Humanize library.
 * All formatters must implement this interface to be compatible with the registry.
 */
interface FormatterInterface
{
    /**
     * Format the given arguments into a human-readable string.
     *
     * Implementations can define their own specific parameter signatures.
     * The variadic nature is only for dynamic invocation via the registry.
     *
     * @param mixed ...$args The arguments to format
     * @return string The formatted result
     */
    public function format(...$args): string;

    /**
     * Get the name of this formatter.
     * This is used for registry lookup and magic method calls.
     *
     * @return string The formatter name (e.g., 'fileSize', 'ordinal')
     */
    public function getName(): string;
}

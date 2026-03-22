<?php

namespace Rjds\PhpHumanize;

use Rjds\PhpHumanize\Formatter\FormatterInterface;
use RuntimeException;

/**
 * Registry for managing and retrieving formatters.
 * This enables dynamic registration of formatters at runtime.
 */
class FormatterRegistry
{
    /**
     * @var array<string, FormatterInterface>
     */
    private array $formatters = [];

    /**
     * Register a formatter in the registry.
     *
     * @param string $name The name to register the formatter under
     * @param FormatterInterface $formatter The formatter instance
     * @return self For fluent interface
     */
    public function register(string $name, FormatterInterface $formatter): self
    {
        $this->formatters[$name] = $formatter;
        return $this;
    }

    /**
     * Retrieve a registered formatter.
     *
     * @param string $name The name of the formatter
     * @return FormatterInterface The formatter instance
     * @throws RuntimeException If the formatter is not registered
     */
    public function get(string $name): FormatterInterface
    {
        if (!$this->has($name)) {
            throw new RuntimeException("Formatter '{$name}' is not registered");
        }

        return $this->formatters[$name];
    }

    /**
     * Check if a formatter is registered.
     *
     * @param string $name The name of the formatter
     * @return bool True if registered, false otherwise
     */
    public function has(string $name): bool
    {
        return isset($this->formatters[$name]);
    }

    /**
     * Get all registered formatter names.
     *
     * @return array<int, string> Array of formatter names
     */
    public function getNames(): array
    {
        return array_keys($this->formatters);
    }

    /**
     * Get all registered formatters.
     *
     * @return array<string, FormatterInterface>
     */
    public function all(): array
    {
        return $this->formatters;
    }

    /**
     * Auto-discover and register formatters from a directory.
     * Looks for classes implementing FormatterInterface in the given directory.
     *
     * @param string $directory The directory to scan
     * @param string $namespace The namespace prefix for classes in this directory
     * @return self For fluent interface
     */
    public function autoDiscover(string $directory, string $namespace = 'Rjds\PhpHumanize\Formatter'): self
    {
        if (!is_dir($directory)) {
            throw new RuntimeException("Directory '{$directory}' does not exist");
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php' || $file->getFilename() === 'FormatterInterface.php') {
                continue;
            }

            $relativePath = ltrim(substr($file->getPathname(), strlen($directory)), '\\/');
            $relativeClass = str_replace(['/', '\\'], '\\', substr($relativePath, 0, -4));

            $candidates = [
                $namespace . '\\' . $relativeClass,
                $namespace . '\\' . $file->getBasename('.php'),
            ];

            foreach (array_unique($candidates) as $fullClassName) {
                if (!class_exists($fullClassName)) {
                    continue;
                }

                $reflectionClass = new \ReflectionClass($fullClassName);

                if (
                    $reflectionClass->implementsInterface(FormatterInterface::class) &&
                    !$reflectionClass->isAbstract() &&
                    !$reflectionClass->isInterface()
                ) {
                    $instance = new $fullClassName();

                    if ($instance instanceof FormatterInterface) {
                        $this->register($instance->getName(), $instance);
                    }

                    break;
                }
            }
        }

        return $this;
    }
}

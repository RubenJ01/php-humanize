<?php

namespace Rjds\PhpHumanize\Tests;

use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\FormatterRegistry;
use RuntimeException;

class FormatterRegistryTest extends TestCase
{
    public function testItRegistersAndRetrievesFormatter(): void
    {
        $registry = new FormatterRegistry();
        $formatter = new class implements FormatterInterface {
            public function format(...$args): string
            {
                return 'ok';
            }

            public function getName(): string
            {
                return 'dummy';
            }
        };

        self::assertFalse($registry->has('dummy'));

        $result = $registry->register('dummy', $formatter);

        self::assertSame($registry, $result);
        self::assertTrue($registry->has('dummy'));
        self::assertSame($formatter, $registry->get('dummy'));
        self::assertSame(['dummy'], $registry->getNames());
        self::assertSame(['dummy' => $formatter], $registry->all());
    }

    public function testItThrowsWhenFormatterIsMissing(): void
    {
        $registry = new FormatterRegistry();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Formatter 'missing' is not registered");

        $registry->get('missing');
    }

    public function testItOverwritesFormatterWhenRegisteringSameNameTwice(): void
    {
        $registry = new FormatterRegistry();
        $first = new class implements FormatterInterface {
            public function format(...$args): string
            {
                return 'first';
            }

            public function getName(): string
            {
                return 'duplicate';
            }
        };

        $second = new class implements FormatterInterface {
            public function format(...$args): string
            {
                return 'second';
            }

            public function getName(): string
            {
                return 'duplicate';
            }
        };

        $registry->register('duplicate', $first);
        $registry->register('duplicate', $second);

        self::assertSame($second, $registry->get('duplicate'));
        self::assertSame(['duplicate'], $registry->getNames());
    }

    public function testAutoDiscoverThrowsForMissingDirectory(): void
    {
        $registry = new FormatterRegistry();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Directory 'C:\\definitely\\missing\\path' does not exist");

        $registry->autoDiscover('C:\\definitely\\missing\\path');
    }

    public function testAutoDiscoverRegistersOnlyConcreteFormatterClasses(): void
    {
        $registry = new FormatterRegistry();
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-humanize-autodiscover-' . uniqid('', true);
        $namespace = 'Rjds\\PhpHumanize\\Tests\\AutoDiscover';

        mkdir($directory, 0777, true);

        try {
            file_put_contents($directory . DIRECTORY_SEPARATOR . 'FormatterInterface.php', "<?php\n");
            file_put_contents($directory . DIRECTORY_SEPARATOR . 'NotPhp.txt', 'ignored');
            file_put_contents($directory . DIRECTORY_SEPARATOR . 'MissingFormatter.php', "<?php\n");

            file_put_contents($directory . DIRECTORY_SEPARATOR . 'ZzzAutoFormatter.php', <<<PHP
<?php

namespace {$namespace};

class ZzzAutoFormatter implements \Rjds\PhpHumanize\Formatter\FormatterInterface
{
    public function format(...\$args): string
    {
        return 'discovered';
    }

    public function getName(): string
    {
        return 'discovered';
    }
}
PHP
            );

            file_put_contents($directory . DIRECTORY_SEPARATOR . 'AbstractAutoFormatter.php', <<<PHP
<?php

namespace {$namespace};

abstract class AbstractAutoFormatter implements \Rjds\PhpHumanize\Formatter\FormatterInterface
{
    public function format(...\$args): string
    {
        return 'abstract';
    }
}
PHP
            );

            file_put_contents($directory . DIRECTORY_SEPARATOR . 'InterfaceAutoFormatter.php', <<<PHP
<?php

namespace {$namespace};

interface InterfaceAutoFormatter extends \Rjds\PhpHumanize\Formatter\FormatterInterface
{
}
PHP
            );

            require_once $directory . DIRECTORY_SEPARATOR . 'ZzzAutoFormatter.php';
            require_once $directory . DIRECTORY_SEPARATOR . 'AbstractAutoFormatter.php';
            require_once $directory . DIRECTORY_SEPARATOR . 'InterfaceAutoFormatter.php';

            $result = $registry->autoDiscover($directory, $namespace);

            self::assertSame($registry, $result);
            self::assertTrue($registry->has('discovered'));
            self::assertSame('discovered', $registry->get('discovered')->format());
            self::assertSame(['discovered'], $registry->getNames());
        } finally {
            $this->deleteDirectory($directory);
        }
    }

    private function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = scandir($directory);

        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
                continue;
            }

            unlink($path);
        }

        rmdir($directory);
    }
}

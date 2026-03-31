<?php

namespace Rjds\PhpHumanize\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Humanizer;
use Rjds\PhpHumanize\HumanizerConfig;
use Rjds\PhpHumanize\HumanizerFactory;

class HumanizerFactoryTest extends TestCase
{
    public function testFactoryRegistersAllBuiltInFormatters(): void
    {
        $humanizer = HumanizerFactory::create();

        $names = $humanizer->getRegistry()->getNames();
        sort($names);

        self::assertSame(
            [
                'abbreviate',
                'dataRate',
                'diffForHumans',
                'duration',
                'fileSize',
                'joinList',
                'number',
                'ordinal',
                'percentage',
                'pluralize',
                'readableDate',
                'toWords',
                'truncate',
            ],
            $names
        );
    }
    public function testFactoryCreatesHumanizerWithDefaults(): void
    {
        $humanizer = HumanizerFactory::create();

        self::assertSame('1.6 KB', $humanizer->fileSize(1600));
        self::assertSame('1st', $humanizer->ordinal(1));
    }

    public function testFactoryCanOverrideFormatterByName(): void
    {
        $formatter = new class implements FormatterInterface {
            public function format(...$args): string
            {
                return 'OVERRIDE';
            }

            public function getName(): string
            {
                return 'fileSize';
            }
        };

        $humanizer = HumanizerFactory::create(formatters: ['fileSize' => $formatter]);

        self::assertSame('OVERRIDE', $humanizer->fileSize(1));
        self::assertSame('1st', $humanizer->ordinal(1)); // still uses built-in formatter
    }

    public function testFactoryUsesConfigDefaults(): void
    {
        $config = new HumanizerConfig(locale: Humanizer::LOCALE_NL);
        $humanizer = HumanizerFactory::create(config: $config);

        $dateTime = new DateTimeImmutable('2026-03-30 10:00:00+00:00');
        self::assertSame('Maandag 30 maart 2026', $humanizer->readableDate($dateTime));
    }

    public function testFactoryThrowsForNonStringFormatterNameKey(): void
    {
        $formatter = new class implements FormatterInterface {
            public function format(...$args): string
            {
                return 'x';
            }

            public function getName(): string
            {
                return 'fileSize';
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter name must be a non-empty string.');

        HumanizerFactory::create(formatters: [0 => $formatter]);
    }

    public function testFactoryThrowsForInvalidFormatterInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formatter for "fileSize" must implement');

        HumanizerFactory::create(formatters: ['fileSize' => 'invalid']);
    }

    public function testCreateDefaultRegistryCanBeInvokedViaReflection(): void
    {
        $reflection = new \ReflectionClass(HumanizerFactory::class);
        $method = $reflection->getMethod('createDefaultRegistry');
        $method->setAccessible(true);

        /** @var \Rjds\PhpHumanize\FormatterRegistry $registry */
        $registry = $method->invoke(null);
        $names = $registry->getNames();
        sort($names);

        self::assertSame(
            [
                'abbreviate',
                'dataRate',
                'diffForHumans',
                'duration',
                'fileSize',
                'joinList',
                'number',
                'ordinal',
                'percentage',
                'pluralize',
                'readableDate',
                'toWords',
                'truncate',
            ],
            $names
        );
    }
}

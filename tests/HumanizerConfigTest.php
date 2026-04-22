<?php

namespace Rjds\PhpHumanize\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\HumanizerConfig;

class HumanizerConfigTest extends TestCase
{
    public function testItProvidesExpectedDefaults(): void
    {
        $config = new HumanizerConfig();

        self::assertSame('en', $config->getLocale());
        self::assertSame(0, $config->getNumberPrecision());
        self::assertSame('and', $config->getListConjunction());
        self::assertSame('…', $config->getTruncateSuffix());
    }

    public function testWithMethodsReturnNewImmutableInstances(): void
    {
        $config = new HumanizerConfig();

        $updated = $config
            ->withLocale('nl')
            ->withNumberPrecision(2)
            ->withListConjunction('or')
            ->withTruncateSuffix('...');

        self::assertNotSame($config, $updated);
        self::assertSame('en', $config->getLocale());
        self::assertSame(0, $config->getNumberPrecision());
        self::assertSame('and', $config->getListConjunction());
        self::assertSame('…', $config->getTruncateSuffix());

        self::assertSame('nl', $updated->getLocale());
        self::assertSame(2, $updated->getNumberPrecision());
        self::assertSame('or', $updated->getListConjunction());
        self::assertSame('...', $updated->getTruncateSuffix());
    }

    public function testItNormalizesLocaleAliases(): void
    {
        $config = new HumanizerConfig(locale: ' EN_us ');

        self::assertSame('en-US', $config->getLocale());
        self::assertSame('nl-NL', $config->withLocale('nl_nl')->getLocale());
    }

    public function testItTrimsListConjunction(): void
    {
        $config = new HumanizerConfig(listConjunction: '  or  ');

        self::assertSame('or', $config->getListConjunction());
    }

    public function testItRejectsEmptyLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('locale cannot be empty.');

        new HumanizerConfig(locale: '  ');
    }

    public function testItRejectsNegativeNumberPrecision(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('numberPrecision must be greater than or equal to 0.');

        new HumanizerConfig(numberPrecision: -1);
    }

    public function testItRejectsEmptyListConjunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('listConjunction cannot be empty.');

        new HumanizerConfig(listConjunction: '');
    }
}

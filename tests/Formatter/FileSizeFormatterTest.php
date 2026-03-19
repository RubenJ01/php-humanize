<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\FileSizeFormatter;

class FileSizeFormatterTest extends TestCase
{
    private FileSizeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new FileSizeFormatter();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function fileSizeProvider(): array
    {
        return [
            'zero bytes' => [0, '0 B'],
            'negative bytes are clamped to zero' => [-1, '0 B'],
            'bytes' => [500, '500 B'],
            'exactly one kilobyte' => [1024, '1 KB'],
            'default precision uses one decimal place' => [1601, '1.6 KB'],
            'kilobytes' => [1536, '1.5 KB'],
            'just below one megabyte stays in kilobytes' => [1024 ** 2 - 1, '1,024 KB'],
            'megabytes' => [5452595, '5.2 MB'],
            'gigabytes' => [2147483648, '2 GB'],
            'petabytes are capped to largest configured unit' => [PHP_INT_MAX, '8,192 PB'],
        ];
    }

    #[DataProvider('fileSizeProvider')]
    public function testItFormatsFileSize(int $bytes, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($bytes));
    }

    public function testItFormatsFileSizeWithCustomPrecision(): void
    {
        self::assertSame('1.5625 KB', $this->formatter->format(1600, 4));
    }

    public function testItFormatsExactPowerOf1024AsWholeUnit(): void
    {
        self::assertSame('1 MB', $this->formatter->format(1024 ** 2));
    }

    public function testItUsesBinaryBaseForScalingWhenUsingHighPrecision(): void
    {
        self::assertSame('1.563477 KB', $this->formatter->format(1601, 6));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0 B', $this->formatter->format());
    }

    public function testItCastsBytesAndPrecisionArguments(): void
    {
        self::assertSame('0 B', $this->formatter->format('foo'));
        self::assertSame('2 KB', $this->formatter->format(1536, '0foo'));
    }
}

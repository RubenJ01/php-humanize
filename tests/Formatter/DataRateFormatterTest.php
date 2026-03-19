<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\DataRateFormatter;

class DataRateFormatterTest extends TestCase
{
    private DataRateFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DataRateFormatter();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function dataRateProvider(): array
    {
        return [
            'zero bytes per second' => [0, '0 B/s'],
            'negative bytes per second are clamped to zero' => [-1, '0 B/s'],
            'bytes per second' => [500, '500 B/s'],
            'default precision uses one decimal place' => [1601, '1.6 KB/s'],
            'kilobytes per second' => [1536, '1.5 KB/s'],
            'megabytes per second' => [1048576, '1 MB/s'],
            'gigabytes per second' => [1073741824, '1 GB/s'],
            'petabytes per second are capped to largest configured unit' => [PHP_INT_MAX, '8,192 PB/s'],
        ];
    }

    #[DataProvider('dataRateProvider')]
    public function testItFormatsDataRate(int $bytesPerSecond, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($bytesPerSecond));
    }

    public function testItFormatsDataRateWithCustomPrecision(): void
    {
        self::assertSame('1.5625 KB/s', $this->formatter->format(1600, 4));
    }

    public function testItUsesBinaryBaseForScalingWhenUsingHighPrecision(): void
    {
        self::assertSame('1.563477 KB/s', $this->formatter->format(1601, 6));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0 B/s', $this->formatter->format());
    }

    public function testItCastsBytesPerSecondAndPrecisionArguments(): void
    {
        self::assertSame('0 B/s', $this->formatter->format('foo'));
        self::assertSame('2 KB/s', $this->formatter->format(1536, '0foo'));
    }
}

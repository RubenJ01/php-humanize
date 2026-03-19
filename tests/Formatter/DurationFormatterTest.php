<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\DurationFormatter;

class DurationFormatterTest extends TestCase
{
    private DurationFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DurationFormatter();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function durationProvider(): array
    {
        return [
            'zero seconds' => [0, '0 seconds'],
            'singular second' => [1, '1 second'],
            'plural seconds' => [45, '45 seconds'],
            'one minute and thirty seconds' => [90, '1 minute, 30 seconds'],
            'one hour one minute one second' => [3661, '1 hour, 1 minute, 1 second'],
            'exactly one day' => [86400, '1 day'],
            'multiple days' => [172800, '2 days'],
            'complex duration' => [90061, '1 day, 1 hour, 1 minute, 1 second'],
        ];
    }

    #[DataProvider('durationProvider')]
    public function testItFormatsDuration(int $seconds, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($seconds));
    }

    /**
     * @return array<string, array{int, int, string}>
     */
    public static function precisionProvider(): array
    {
        return [
            'precision 1' => [3661, 1, '1 hour'],
            'precision 2' => [3661, 2, '1 hour, 1 minute'],
            'precision 3' => [3661, 3, '1 hour, 1 minute, 1 second'],
            'precision exceeds units' => [90, 5, '1 minute, 30 seconds'],
        ];
    }

    #[DataProvider('precisionProvider')]
    public function testItFormatsDurationWithPrecision(int $seconds, int $precision, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($seconds, $precision));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0 seconds', $this->formatter->format());
    }

    public function testItCastsSecondsAndPrecisionArguments(): void
    {
        self::assertSame('0 seconds', $this->formatter->format('foo'));
        self::assertSame('1 hour', $this->formatter->format(3661, '1foo'));
    }
}

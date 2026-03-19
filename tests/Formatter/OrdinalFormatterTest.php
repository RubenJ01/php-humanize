<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\OrdinalFormatter;

class OrdinalFormatterTest extends TestCase
{
    private OrdinalFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new OrdinalFormatter();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function ordinalProvider(): array
    {
        return [
            'first' => [1, '1st'],
            'second' => [2, '2nd'],
            'third' => [3, '3rd'],
            'fourth' => [4, '4th'],
            'eleventh' => [11, '11th'],
            'twelfth' => [12, '12th'],
            'thirteenth' => [13, '13th'],
            'twenty-first' => [21, '21st'],
            'one hundred eleventh' => [111, '111th'],
            'one hundred thirteenth' => [113, '113th'],
            'one hundred first' => [101, '101st'],
            'one hundred ninety-ninth' => [199, '199th'],
        ];
    }

    #[DataProvider('ordinalProvider')]
    public function testItFormatsOrdinals(int $number, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($number));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0th', $this->formatter->format());
    }

    public function testItCastsInputToInteger(): void
    {
        self::assertSame('0th', $this->formatter->format('foo'));
    }
}

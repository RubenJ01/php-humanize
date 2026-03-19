<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\AbbreviationFormatter;

class AbbreviationFormatterTest extends TestCase
{
    private AbbreviationFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new AbbreviationFormatter();
    }

    /**
     * @return array<string, array{float|int, string}>
     */
    public static function abbreviationProvider(): array
    {
        return [
            'thousands' => [1500, '1.5K'],
            'default precision rounds to one decimal' => [1250, '1.3K'],
            'millions' => [2300000, '2.3M'],
            'billions' => [1000000000, '1B'],
            'small numbers unchanged' => [999, '999'],
            'small float numbers keep decimals' => [999.5, '999.5'],
            'negative numbers' => [-1500, '-1.5K'],
        ];
    }

    #[DataProvider('abbreviationProvider')]
    public function testItAbbreviatesNumbers(float|int $number, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($number));
    }

    public function testItAbbreviatesWithCustomPrecision(): void
    {
        self::assertSame('1.25K', $this->formatter->format(1250, 2));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0', $this->formatter->format());
    }

    public function testItCastsPrecisionToInteger(): void
    {
        self::assertSame('2K', $this->formatter->format(1500, '0foo'));
    }
}

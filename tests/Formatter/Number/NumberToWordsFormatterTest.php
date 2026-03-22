<?php

namespace Rjds\PhpHumanize\Tests\Formatter\Number;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Rjds\PhpHumanize\Formatter\NumberToWordsFormatter;

class NumberToWordsFormatterTest extends TestCase
{
    private NumberToWordsFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NumberToWordsFormatter();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function numberToWordsProvider(): array
    {
        return [
            'zero' => [0, 'zero'],
            'single digit' => [5, 'five'],
            'teen' => [13, 'thirteen'],
            'tens' => [42, 'forty-two'],
            'even tens' => [20, 'twenty'],
            'hundreds' => [100, 'one hundred'],
            'hundreds boundary before two hundred' => [199, 'one hundred ninety-nine'],
            'hundreds with remainder' => [512, 'five hundred twelve'],
            'hundreds upper boundary' => [999, 'nine hundred ninety-nine'],
            'thousand' => [1000, 'one thousand'],
            'thousand with hundreds remainder' => [1100, 'one thousand, one hundred'],
            'large number' => [1234567, 'one million, two hundred thirty-four thousand, five hundred sixty-seven'],
            'negative number' => [-42, 'negative forty-two'],
            'one million' => [1000000, 'one million'],
            'one billion' => [1000000000, 'one billion'],
        ];
    }

    #[DataProvider('numberToWordsProvider')]
    public function testItConvertsNumbersToWords(int $number, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($number));
    }

    public function testPrivateSubThousandFallbackForValuesAboveRange(): void
    {
        $method = new ReflectionMethod($this->formatter, 'formatSubThousand');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sub-thousand formatter only accepts values from 0 to 999');

        $method->invoke($this->formatter, 1001);
    }

    public function testPrivateSubHundredRejectsNegativeValues(): void
    {
        $method = new ReflectionMethod($this->formatter, 'formatSubHundred');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Number must be non-negative');

        $method->invoke($this->formatter, -1);
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('zero', $this->formatter->format());
    }

    public function testItCastsNumericInputArgument(): void
    {
        self::assertSame('zero', $this->formatter->format('foo'));
        self::assertSame('zero', $this->formatter->format([]));
    }
}

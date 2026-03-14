<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
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
            'hundreds with remainder' => [512, 'five hundred twelve'],
            'thousand' => [1000, 'one thousand'],
            'large number' => [1234567, 'one million, two hundred thirty-four thousand, five hundred sixty-seven'],
            'negative number' => [-42, 'negative forty-two'],
            'one million' => [1000000, 'one million'],
            'one billion' => [1000000000, 'one billion'],
        ];
    }

    #[DataProvider('numberToWordsProvider')]
    public function testItConvertsNumbersToWords(int $number, string $expected): void
    {
        $this->assertEquals($expected, $this->formatter->format($number));
    }
}

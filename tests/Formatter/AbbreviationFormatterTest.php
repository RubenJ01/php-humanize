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
            'millions' => [2300000, '2.3M'],
            'billions' => [1000000000, '1B'],
            'small numbers unchanged' => [999, '999'],
            'negative numbers' => [-1500, '-1.5K'],
        ];
    }

    #[DataProvider('abbreviationProvider')]
    public function testItAbbreviatesNumbers(float|int $number, string $expected): void
    {
        $this->assertEquals($expected, $this->formatter->format($number));
    }

    public function testItAbbreviatesWithCustomPrecision(): void
    {
        $this->assertEquals('1.5K', $this->formatter->format(1500, 2));
    }
}

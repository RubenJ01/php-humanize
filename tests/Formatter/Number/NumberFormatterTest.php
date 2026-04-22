<?php

namespace Rjds\PhpHumanize\Tests\Formatter\Number;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;

class NumberFormatterTest extends TestCase
{
    private NumberFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter();
    }

    /**
     * @return array<string, array{float|int, int, string, string}>
     */
    public static function localizedNumberProvider(): array
    {
        return [
            'english default separators' => [1234567.89, 2, 'en', '1,234,567.89'],
            'english locale with region' => [1234.5, 1, 'en_US', '1,234.5'],
            'english locale alias with hyphen and mixed case' => [1234.5, 1, 'EN-us', '1,234.5'],
            'dutch separators' => [1234567.89, 2, 'nl', '1.234.567,89'],
            'dutch locale with region' => [1234.5, 1, 'nl_NL', '1.234,5'],
            'dutch uppercase locale' => [1234.5, 1, 'NL', '1.234,5'],
            'german locale uses intl conventions' => [1234.5, 1, 'de_DE', '1.234,5'],
            'negative values' => [-1234.56, 2, 'nl', '-1.234,56'],
            'zero precision rounds number' => [1234.5, 0, 'en', '1,235'],
        ];
    }

    #[DataProvider('localizedNumberProvider')]
    public function testItFormatsLocalizedNumbers(
        float|int $number,
        int $precision,
        string $locale,
        string $expected
    ): void {
        self::assertSame($expected, $this->formatter->format($number, $precision, $locale));
    }

    public function testItUsesEnglishDefaultsWhenNoOptionalArgumentsAreProvided(): void
    {
        self::assertSame('1,235', $this->formatter->format(1234.56));
    }

    public function testItUsesEnglishFallbackWhenIntlPreferenceIsDisabled(): void
    {
        $formatter = new NumberFormatter(preferIntl: false);

        self::assertSame('1,234.5', $formatter->format(1234.5, 1, 'nl'));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0', $this->formatter->format());
    }

    public function testItNormalizesNumericStringsAndPrecision(): void
    {
        self::assertSame('1,234.57', $this->formatter->format('1234.567', '2foo', 'en'));
    }

    public function testItClampsNegativePrecisionToZero(): void
    {
        self::assertSame('1.235', $this->formatter->format(1234.56, -2, 'nl'));
    }

    public function testItDefaultsToZeroPrecisionWhenPrecisionIsNotScalar(): void
    {
        self::assertSame('1,235', $this->formatter->format(1234.56, [], 'en'));
    }

    public function testItDefaultsToZeroWhenNumberCannotBeParsed(): void
    {
        self::assertSame('0', $this->formatter->format([], 0, 'en'));
    }

    public function testItNormalizesTrueToZero(): void
    {
        self::assertSame('0', $this->formatter->format(true, 0, 'en'));
    }

    public function testItThrowsWhenLocaleIsWhitespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Third argument must be a non-empty locale string');

        $this->formatter->format(1234, 0, '   ');
    }
}

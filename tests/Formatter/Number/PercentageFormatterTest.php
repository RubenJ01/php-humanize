<?php

namespace Rjds\PhpHumanize\Tests\Formatter\Number;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;

class PercentageFormatterTest extends TestCase
{
    private PercentageFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new PercentageFormatter();
    }

    /**
     * @return array<string, array{float|int, int, string, bool, string}>
     */
    public static function localizedPercentageProvider(): array
    {
        return [
            'english from fraction' => [0.153, 1, 'en', true, '15.3%'],
            'english direct percentage' => [15.3, 1, 'en', false, '15.3%'],
            'english locale with region' => [0.5, 0, 'en_US', true, '50%'],
            'dutch separators' => [0.153, 1, 'nl', true, '15,3%'],
            'dutch locale with region' => [0.5, 0, 'nl_NL', true, '50%'],
            'german locale uses intl conventions' => [0.153, 1, 'de_DE', true, '15,3%'],
            'negative values' => [-0.125, 1, 'nl', true, '-12,5%'],
            'large values include grouping' => [1234.56, 2, 'en', false, '1,234.56%'],
        ];
    }

    #[DataProvider('localizedPercentageProvider')]
    public function testItFormatsLocalizedPercentages(
        float|int $value,
        int $precision,
        string $locale,
        bool $fromFraction,
        string $expected
    ): void {
        self::assertSame($expected, $this->formatter->format($value, $precision, $locale, $fromFraction));
    }

    public function testItUsesEnglishDefaultsWhenNoOptionalArgumentsAreProvided(): void
    {
        self::assertSame('123,456%', $this->formatter->format(1234.56));
    }

    public function testItUsesEnglishFallbackWhenIntlPreferenceIsDisabled(): void
    {
        $formatter = new PercentageFormatter(preferIntl: false);

        self::assertSame('12.5%', $formatter->format(0.125, 1, 'nl', true));
    }

    public function testItDefaultsToZeroWhenNoArgumentsAreProvided(): void
    {
        self::assertSame('0%', $this->formatter->format());
    }

    public function testItNormalizesNumericStringsAndPrecision(): void
    {
        self::assertSame('123.46%', $this->formatter->format('1.234567', '2foo', 'en', true));
    }

    public function testItClampsNegativePrecisionToZero(): void
    {
        self::assertSame('123%', $this->formatter->format(1.234, -2, 'en', true));
    }

    public function testItDefaultsToZeroPrecisionWhenPrecisionIsNotScalar(): void
    {
        self::assertSame('123%', $this->formatter->format(1.234, [], 'en', true));
    }

    public function testItDefaultsToZeroWhenValueCannotBeParsed(): void
    {
        self::assertSame('0%', $this->formatter->format([], 0, 'en', true));
    }

    public function testItNormalizesTrueToZero(): void
    {
        self::assertSame('0%', $this->formatter->format(true, 0, 'en', true));
    }

    public function testItThrowsWhenLocaleIsWhitespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Third argument must be a non-empty locale string');

        $this->formatter->format(0.125, 1, '   ', true);
    }

    public function testItNormalizesNegativeZeroOutput(): void
    {
        self::assertSame('0.00%', $this->formatter->format(-0.00001, 2, 'en', false));
    }

    public function testItKeepsNegativeSignForNonZeroValues(): void
    {
        self::assertSame('-0.1%', $this->formatter->format(-0.1, 1, 'en', false));
    }

    public function testItCanInterpretStringFalseForFromFraction(): void
    {
        self::assertSame('15.3%', $this->formatter->format(15.3, 1, 'en', 'false'));
    }

    public function testItCanInterpretUppercaseStringFalseForFromFraction(): void
    {
        self::assertSame('15.3%', $this->formatter->format(15.3, 1, 'en', 'FALSE'));
    }

    public function testItCanInterpretTrimmedStringFalseForFromFraction(): void
    {
        self::assertSame('15.3%', $this->formatter->format(15.3, 1, 'en', ' false '));
    }

    public function testItCanInterpretNumericZeroForFromFraction(): void
    {
        self::assertSame('15.3%', $this->formatter->format(15.3, 1, 'en', 0));
    }

    public function testItCanInterpretStringTrueForFromFraction(): void
    {
        self::assertSame('15.3%', $this->formatter->format(0.153, 1, 'en', 'true'));
    }

    public function testItFallsBackToFractionModeWhenFromFractionStringIsUnknown(): void
    {
        self::assertSame('15.3%', $this->formatter->format(0.153, 1, 'en', 'maybe'));
    }

    public function testItFallsBackToFractionModeWhenFromFractionTypeIsUnsupported(): void
    {
        self::assertSame('15.3%', $this->formatter->format(0.153, 1, 'en', new \stdClass()));
    }

    public function testItExposesExpectedFormatterName(): void
    {
        self::assertSame('percentage', $this->formatter->getName());
    }
}

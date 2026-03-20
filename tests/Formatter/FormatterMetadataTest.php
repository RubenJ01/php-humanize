<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\AbbreviationFormatter;
use Rjds\PhpHumanize\Formatter\DataRateFormatter;
use Rjds\PhpHumanize\Formatter\DateLocalizedFormatter;
use Rjds\PhpHumanize\Formatter\DurationFormatter;
use Rjds\PhpHumanize\Formatter\FileSizeFormatter;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\ListJoinFormatter;
use Rjds\PhpHumanize\Formatter\NumberToWordsFormatter;
use Rjds\PhpHumanize\Formatter\OrdinalFormatter;
use Rjds\PhpHumanize\Formatter\PluralizeFormatter;
use Rjds\PhpHumanize\Formatter\TextTruncationFormatter;
use Rjds\PhpHumanize\Formatter\TimeDiffFormatter;
use ReflectionClass;

class FormatterMetadataTest extends TestCase
{
    /** @var array<class-string<FormatterInterface>, string> */
    private const EXPECTED_FORMATTER_NAMES = [
        FileSizeFormatter::class => 'fileSize',
        DataRateFormatter::class => 'dataRate',
        OrdinalFormatter::class => 'ordinal',
        AbbreviationFormatter::class => 'abbreviate',
        TimeDiffFormatter::class => 'diffForHumans',
        ListJoinFormatter::class => 'joinList',
        PluralizeFormatter::class => 'pluralize',
        NumberToWordsFormatter::class => 'toWords',
        DurationFormatter::class => 'duration',
        TextTruncationFormatter::class => 'truncate',
        DateLocalizedFormatter::class => 'readableDate',
    ];

    /**
     * @return array<string, array{class-string<FormatterInterface>, string}>
     */
    public static function formatterNameProvider(): array
    {
        $cases = [];

        foreach (self::EXPECTED_FORMATTER_NAMES as $className => $expectedName) {
            $cases[$className] = [$className, $expectedName];
        }

        return $cases;
    }

    #[DataProvider('formatterNameProvider')]
    /**
     * @param class-string<FormatterInterface> $className
     */
    public function testFormatterExposesExpectedName(string $className, string $expectedName): void
    {
        $formatter = new $className();

        self::assertInstanceOf(FormatterInterface::class, $formatter);

        self::assertSame($expectedName, $formatter->getName());
    }

    public function testFormatterNameMapContainsAllConcreteBuiltInFormatters(): void
    {
        $discovered = self::discoverBuiltInFormatters();
        $expected = array_keys(self::EXPECTED_FORMATTER_NAMES);

        sort($discovered);
        sort($expected);

        self::assertSame($expected, $discovered);
    }

    public function testFormatterNamesAreUniqueAcrossBuiltIns(): void
    {
        $names = [];

        foreach (array_keys(self::EXPECTED_FORMATTER_NAMES) as $className) {
            $names[] = (new $className())->getName();
        }

        self::assertCount(count($names), array_unique($names));
    }

    /**
     * @return list<class-string<FormatterInterface>>
     */
    private static function discoverBuiltInFormatters(): array
    {
        $files = glob(__DIR__ . '/../../src/Formatter/*.php');

        if ($files === false) {
            return [];
        }

        $formatters = [];

        foreach ($files as $file) {
            $className = 'Rjds\\PhpHumanize\\Formatter\\' . pathinfo($file, PATHINFO_FILENAME);

            if (!class_exists($className)) {
                continue;
            }

            if (!is_a($className, FormatterInterface::class, true)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            if (!$reflection->isInstantiable() || !$reflection->implementsInterface(FormatterInterface::class)) {
                continue;
            }

            $formatters[] = $className;
        }

        return $formatters;
    }
}

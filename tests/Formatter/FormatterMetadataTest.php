<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\Data\DataRateFormatter;
use Rjds\PhpHumanize\Formatter\Data\FileSizeFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\DurationFormatter;
use Rjds\PhpHumanize\Formatter\DateTime\TimeDiffFormatter;
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Formatter\Number\AbbreviationFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberToWordsFormatter;
use Rjds\PhpHumanize\Formatter\Number\OrdinalFormatter;
use Rjds\PhpHumanize\Formatter\Text\ListJoinFormatter;
use Rjds\PhpHumanize\Formatter\Text\PluralizeFormatter;
use Rjds\PhpHumanize\Formatter\Text\TextTruncationFormatter;
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
        DateFormatter::class => 'readableDate',
        NumberFormatter::class => 'number',
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
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__ . '/../../src/Formatter')
        );

        $formatters = [];

        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen(__DIR__ . '/../../src/Formatter') + 1);


            $relativeClass = str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relativePath);
            $className = 'Rjds\\PhpHumanize\\Formatter\\' . $relativeClass;

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

            $docComment = $reflection->getDocComment();

            if (is_string($docComment) && str_contains($docComment, '@deprecated')) {
                continue;
            }

            $formatters[] = $className;
        }

        return $formatters;
    }
}

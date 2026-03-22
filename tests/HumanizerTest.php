<?php

namespace Rjds\PhpHumanize\Tests;

use DateTimeImmutable;
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
use Rjds\PhpHumanize\FormatterRegistry;
use Rjds\PhpHumanize\Humanizer;
use RuntimeException;

class HumanizerTest extends TestCase
{
    private Humanizer $humanizer;

    protected function setUp(): void
    {
        $this->humanizer = new Humanizer();
    }

    public function testItDelegatesToFileSizeFormatter(): void
    {
        self::assertSame('1.6 KB', $this->humanizer->fileSize(1600));
    }

    public function testItDelegatesToDataRateFormatter(): void
    {
        self::assertSame('1.6 KB/s', $this->humanizer->dataRate(1600));
    }

    public function testItDelegatesToOrdinalFormatter(): void
    {
        self::assertSame('1st', $this->humanizer->ordinal(1));
    }

    public function testItDelegatesToAbbreviationFormatter(): void
    {
        self::assertSame('1.3K', $this->humanizer->abbreviate(1250));
    }

    public function testItDelegatesToTimeDiffFormatter(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-5 minutes');

        self::assertSame('5 minutes ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItDelegatesToListJoinFormatter(): void
    {
        self::assertSame('Alice and Bob', $this->humanizer->joinList(['Alice', 'Bob']));
    }

    public function testItDelegatesToPluralizeFormatter(): void
    {
        self::assertSame('5 items', $this->humanizer->pluralize(5, 'item'));
    }

    public function testItDelegatesToNumberToWordsFormatter(): void
    {
        self::assertSame('forty-two', $this->humanizer->toWords(42));
    }

    public function testItDelegatesToDurationFormatter(): void
    {
        self::assertSame('1 hour, 1 minute, 1 second', $this->humanizer->duration(3661));
    }

    public function testItDelegatesToTextTruncationFormatter(): void
    {
        self::assertSame('The quick brown fox…', $this->humanizer->truncate('The quick brown fox jumps', 20));
    }

    public function testItDelegatesToDateFormatter(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 10:00:00+00:00');

        self::assertSame('Maandag 30 maart 2026', $this->humanizer->readableDate($dateTime, Humanizer::LOCALE_NL));
    }

    public function testReadableDateUsesEnglishAsDefaultLocale(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 10:00:00+00:00');

        self::assertSame('Monday 30 March 2026', $this->humanizer->readableDate($dateTime));
    }

    public function testReadableDateUsesEnglishWhenLocaleIsNull(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 10:00:00+00:00');

        self::assertSame('Monday 30 March 2026', $this->humanizer->readableDate($dateTime, null));
    }

    public function testItDelegatesToNumberFormatter(): void
    {
        self::assertSame('1.234,57', $this->humanizer->number(1234.567, 2, Humanizer::LOCALE_NL));
    }

    public function testNumberUsesZeroPrecisionByDefault(): void
    {
        self::assertSame('1,235', $this->humanizer->number(1234.56));
    }

    public function testItExposesFormatterRegistry(): void
    {
        self::assertInstanceOf(FormatterRegistry::class, $this->humanizer->getRegistry());
    }

    public function testItRegistersAndAppliesCustomFormatter(): void
    {
        $formatter = new class implements FormatterInterface {
            public function format(...$args): string
            {
                $value = $args[0] ?? '';

                return strtoupper(self::toString($value));
            }

            private static function toString(mixed $value): string
            {
                if (is_string($value)) {
                    return $value;
                }

                if (is_int($value) || is_float($value) || is_bool($value)) {
                    return (string) $value;
                }

                return '';
            }

            public function getName(): string
            {
                return 'shout';
            }
        };

        $result = $this->humanizer->register('shout', $formatter);

        self::assertSame($this->humanizer, $result);
        self::assertSame('HELLO', $this->humanizer->apply('shout', 'hello'));
    }

    public function testMagicCallInvokesRegisteredFormatter(): void
    {
        $formatter = new class implements FormatterInterface {
            public function format(...$args): string
            {
                return implode(':', array_map([self::class, 'toString'], $args));
            }

            private static function toString(mixed $value): string
            {
                if (is_string($value)) {
                    return $value;
                }

                if (is_int($value) || is_float($value) || is_bool($value)) {
                    return (string) $value;
                }

                return '';
            }

            public function getName(): string
            {
                return 'concat';
            }
        };

        $this->humanizer->register('concat', $formatter);

        self::assertSame('a:b:3', $this->humanizer->__call('concat', ['a', 'b', 3]));
    }

    public function testApplyThrowsForUnknownFormatter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Formatter 'missing' is not registered");

        $this->humanizer->apply('missing');
    }

    public function testMagicCallThrowsForUnknownFormatter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Formatter 'missing' is not registered");

        $this->humanizer->__call('missing', []);
    }

    public function testConstructorUsesProvidedFormatterInstances(): void
    {
        $humanizer = new Humanizer(
            new class extends FileSizeFormatter {
                public function format(...$args): string
                {
                    return 'fs';
                }
            },
            new class extends DataRateFormatter {
                public function format(...$args): string
                {
                    return 'dr';
                }
            },
            new class extends OrdinalFormatter {
                public function format(...$args): string
                {
                    return 'ord';
                }
            },
            new class extends AbbreviationFormatter {
                public function format(...$args): string
                {
                    return 'abbr';
                }
            },
            new class extends TimeDiffFormatter {
                public function format(...$args): string
                {
                    return 'diff';
                }
            },
            new class extends ListJoinFormatter {
                public function format(...$args): string
                {
                    return 'join';
                }
            },
            new class extends PluralizeFormatter {
                public function format(...$args): string
                {
                    return 'plural';
                }
            },
            new class extends NumberToWordsFormatter {
                public function format(...$args): string
                {
                    return 'words';
                }
            },
            new class extends DurationFormatter {
                public function format(...$args): string
                {
                    return 'dur';
                }
            },
            new class extends TextTruncationFormatter {
                public function format(...$args): string
                {
                    return 'trunc';
                }
            },
            new class extends DateFormatter {
                public function format(...$args): string
                {
                    return 'date';
                }
            },
            new class extends NumberFormatter {
                public function format(...$args): string
                {
                    return 'num';
                }
            },
        );

        self::assertSame('fs', $humanizer->fileSize(1));
        self::assertSame('dr', $humanizer->dataRate(1));
        self::assertSame('ord', $humanizer->ordinal(1));
        self::assertSame('abbr', $humanizer->abbreviate(1));
        self::assertSame('diff', $humanizer->diffForHumans(new DateTimeImmutable()));
        self::assertSame('join', $humanizer->joinList(['a', 'b']));
        self::assertSame('plural', $humanizer->pluralize(2, 'x'));
        self::assertSame('words', $humanizer->toWords(2));
        self::assertSame('dur', $humanizer->duration(2));
        self::assertSame('trunc', $humanizer->truncate('abc', 1));
        self::assertSame('date', $humanizer->readableDate(new DateTimeImmutable()));
        self::assertSame('num', $humanizer->number(1));
    }
}

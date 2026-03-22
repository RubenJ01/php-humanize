<?php

namespace Rjds\PhpHumanize\Tests\Formatter\DateTime;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\TimeDiffFormatter;

class TimeDiffFormatterTest extends TestCase
{
    private TimeDiffFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new TimeDiffFormatter();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function timeDiffProvider(): array
    {
        return [
            'seconds ago as just now' => ['-10 seconds', 'just now'],
            'minutes ago' => ['-5 minutes', '5 minutes ago'],
            'singular minute' => ['-1 minute', '1 minute ago'],
            'hours ago' => ['-3 hours', '3 hours ago'],
            'singular day' => ['-1 day', '1 day ago'],
            'days ago' => ['-4 days', '4 days ago'],
            'exactly one week ago' => ['-7 days', '1 week ago'],
            'eleven days ago rounds down to one week' => ['-11 days', '1 week ago'],
            'thirteen days ago stays one week' => ['-13 days', '1 week ago'],
            'weeks ago' => ['-14 days', '2 weeks ago'],
            'singular month' => ['-1 month', '1 month ago'],
            'months ago' => ['-2 months', '2 months ago'],
            'singular year' => ['-1 year', '1 year ago'],
            'years ago' => ['-2 years', '2 years ago'],
            'future diff' => ['+2 hours', 'in 2 hours'],
        ];
    }

    #[DataProvider('timeDiffProvider')]
    public function testItFormatsTimeDiff(string $modifier, string $expected): void
    {
        $now = new DateTimeImmutable();
        $dateTime = $now->modify($modifier);

        self::assertSame($expected, $this->formatter->format($dateTime, $now));
    }

    public function testItUsesCurrentTimeWhenRelativeDateIsNotProvided(): void
    {
        self::assertSame('just now', $this->formatter->format(new DateTimeImmutable()));
    }

    public function testItThrowsWhenFirstArgumentIsNotDateTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First argument must be a DateTimeInterface');

        $this->formatter->format('invalid');
    }

    public function testItThrowsWhenSecondArgumentIsNotDateTimeOrNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Second argument must be a DateTimeInterface or null');

        $this->formatter->format(new DateTimeImmutable(), 'invalid');
    }
}

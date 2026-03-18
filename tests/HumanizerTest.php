<?php

namespace Rjds\PhpHumanize\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Humanizer;
use Rjds\PhpHumanize\HumanizerInterface;

class HumanizerTest extends TestCase
{
    private HumanizerInterface $humanizer;

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
}

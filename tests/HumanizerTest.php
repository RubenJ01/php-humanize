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
        $this->assertEquals('1.5 KB', $this->humanizer->fileSize(1536));
    }

    public function testItDelegatesToOrdinalFormatter(): void
    {
        $this->assertEquals('1st', $this->humanizer->ordinal(1));
    }

    public function testItDelegatesToAbbreviationFormatter(): void
    {
        $this->assertEquals('1.5K', $this->humanizer->abbreviate(1500));
    }

    public function testItDelegatesToTimeDiffFormatter(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-5 minutes');

        $this->assertEquals('5 minutes ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItDelegatesToListJoinFormatter(): void
    {
        $this->assertEquals('Alice and Bob', $this->humanizer->joinList(['Alice', 'Bob']));
    }

    public function testItDelegatesToPluralizeFormatter(): void
    {
        $this->assertEquals('5 items', $this->humanizer->pluralize(5, 'item'));
    }

    public function testItDelegatesToNumberToWordsFormatter(): void
    {
        $this->assertEquals('forty-two', $this->humanizer->toWords(42));
    }

    public function testItDelegatesToDurationFormatter(): void
    {
        $this->assertEquals('1 hour, 1 minute, 1 second', $this->humanizer->duration(3661));
    }
}

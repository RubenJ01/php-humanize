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

    // -- fileSize --

    public function testItFormatsZeroBytes(): void
    {
        $this->assertEquals('0 B', $this->humanizer->fileSize(0));
    }

    public function testItFormatsBytes(): void
    {
        $this->assertEquals('500 B', $this->humanizer->fileSize(500));
    }

    public function testItFormatsKilobytes(): void
    {
        $this->assertEquals('1.5 KB', $this->humanizer->fileSize(1536));
    }

    public function testItFormatsMegabytes(): void
    {
        $this->assertEquals('5.2 MB', $this->humanizer->fileSize(5452595));
    }

    public function testItFormatsGigabytes(): void
    {
        $this->assertEquals('2 GB', $this->humanizer->fileSize(2147483648));
    }

    public function testItFormatsFileSizeWithCustomPrecision(): void
    {
        $this->assertEquals('1.5 KB', $this->humanizer->fileSize(1536, 2));
    }

    // -- ordinal --

    public function testItFormatsFirstOrdinal(): void
    {
        $this->assertEquals('1st', $this->humanizer->ordinal(1));
    }

    public function testItFormatsSecondOrdinal(): void
    {
        $this->assertEquals('2nd', $this->humanizer->ordinal(2));
    }

    public function testItFormatsThirdOrdinal(): void
    {
        $this->assertEquals('3rd', $this->humanizer->ordinal(3));
    }

    public function testItFormatsFourthOrdinal(): void
    {
        $this->assertEquals('4th', $this->humanizer->ordinal(4));
    }

    public function testItFormatsEleventhOrdinal(): void
    {
        $this->assertEquals('11th', $this->humanizer->ordinal(11));
    }

    public function testItFormatsTwelfthOrdinal(): void
    {
        $this->assertEquals('12th', $this->humanizer->ordinal(12));
    }

    public function testItFormatsThirteenthOrdinal(): void
    {
        $this->assertEquals('13th', $this->humanizer->ordinal(13));
    }

    public function testItFormatsTwentyFirstOrdinal(): void
    {
        $this->assertEquals('21st', $this->humanizer->ordinal(21));
    }

    // -- abbreviate --

    public function testItAbbreviatesThousands(): void
    {
        $this->assertEquals('1.5K', $this->humanizer->abbreviate(1500));
    }

    public function testItAbbreviatesMillions(): void
    {
        $this->assertEquals('2.3M', $this->humanizer->abbreviate(2300000));
    }

    public function testItAbbreviatesBillions(): void
    {
        $this->assertEquals('1B', $this->humanizer->abbreviate(1000000000));
    }

    public function testItDoesNotAbbreviateSmallNumbers(): void
    {
        $this->assertEquals('999', $this->humanizer->abbreviate(999));
    }

    public function testItAbbreviatesWithCustomPrecision(): void
    {
        $this->assertEquals('1.5K', $this->humanizer->abbreviate(1500, 2));
    }

    public function testItAbbreviatesNegativeNumbers(): void
    {
        $this->assertEquals('-1.5K', $this->humanizer->abbreviate(-1500));
    }

    // -- diffForHumans --

    public function testItShowsSecondsAgoAsJustNow(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-10 seconds');

        $this->assertEquals('just now', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItShowsMinutesAgo(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-5 minutes');

        $this->assertEquals('5 minutes ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItShowsSingularMinute(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-1 minute');

        $this->assertEquals('1 minute ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItShowsHoursAgo(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-3 hours');

        $this->assertEquals('3 hours ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItShowsDaysAgo(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-4 days');

        $this->assertEquals('4 days ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItShowsWeeksAgo(): void
    {
        $now = new DateTimeImmutable();
        $past = $now->modify('-14 days');

        $this->assertEquals('2 weeks ago', $this->humanizer->diffForHumans($past, $now));
    }

    public function testItShowsFutureDiff(): void
    {
        $now = new DateTimeImmutable();
        $future = $now->modify('+2 hours');

        $this->assertEquals('in 2 hours', $this->humanizer->diffForHumans($future, $now));
    }

    // -- joinList --

    public function testItJoinsEmptyList(): void
    {
        $this->assertEquals('', $this->humanizer->joinList([]));
    }

    public function testItJoinsSingleItem(): void
    {
        $this->assertEquals('Alice', $this->humanizer->joinList(['Alice']));
    }

    public function testItJoinsTwoItems(): void
    {
        $this->assertEquals('Alice and Bob', $this->humanizer->joinList(['Alice', 'Bob']));
    }

    public function testItJoinsThreeItems(): void
    {
        $this->assertEquals('Alice, Bob, and Charlie', $this->humanizer->joinList(['Alice', 'Bob', 'Charlie']));
    }

    public function testItJoinsWithCustomConjunction(): void
    {
        $this->assertEquals('Alice or Bob', $this->humanizer->joinList(['Alice', 'Bob'], 'or'));
    }

    public function testItJoinsWithCustomSeparator(): void
    {
        $this->assertEquals(
            'Alice; Bob; and Charlie',
            $this->humanizer->joinList(['Alice', 'Bob', 'Charlie'], 'and', '; ')
        );
    }

    // -- pluralize --

    public function testItPluralizesSingularItem(): void
    {
        $this->assertEquals('1 item', $this->humanizer->pluralize(1, 'item'));
    }

    public function testItPluralizesMultipleItems(): void
    {
        $this->assertEquals('5 items', $this->humanizer->pluralize(5, 'item'));
    }

    public function testItPluralizesZeroItems(): void
    {
        $this->assertEquals('0 items', $this->humanizer->pluralize(0, 'item'));
    }

    public function testItPluralizesWithCustomPluralForm(): void
    {
        $this->assertEquals('3 children', $this->humanizer->pluralize(3, 'child', 'children'));
    }

    public function testItUsesSingularWithCustomPluralFormWhenOne(): void
    {
        $this->assertEquals('1 child', $this->humanizer->pluralize(1, 'child', 'children'));
    }

    public function testItPluralizesNegativeQuantity(): void
    {
        $this->assertEquals('-1 items', $this->humanizer->pluralize(-1, 'item'));
    }

    public function testItPluralizesNegativeMultipleQuantity(): void
    {
        $this->assertEquals('-3 items', $this->humanizer->pluralize(-3, 'item'));
    }
}

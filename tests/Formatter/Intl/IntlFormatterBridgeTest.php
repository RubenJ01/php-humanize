<?php

namespace Rjds\PhpHumanize\Tests\Formatter\Intl;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\Intl\IntlFormatterBridge;

class IntlFormatterBridgeTest extends TestCase
{
    public function testFormatDecimalUsesRequestedFractionDigits(): void
    {
        self::assertSame('1.20', IntlFormatterBridge::formatDecimal(1.2, 2, 'en'));
        self::assertSame('1,20', IntlFormatterBridge::formatDecimal(1.2, 2, 'nl'));
    }

    public function testFormatDecimalNormalizesNegativeZeroOutput(): void
    {
        self::assertSame('0.00', IntlFormatterBridge::formatDecimal(-0.00001, 2, 'en'));
        self::assertSame('0,00', IntlFormatterBridge::formatDecimal(-0.00001, 2, 'nl'));
    }

    public function testFormatDateSupportsTrimmedLocaleInput(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 12:00:00+00:00');

        self::assertSame('Monday 30 March 2026', IntlFormatterBridge::formatDate($dateTime, ' en '));
    }

    public function testFormatDateHandlesUtcOffsetTimezoneNames(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 12:00:00+00:00');

        self::assertSame('Monday 30 March 2026', IntlFormatterBridge::formatDate($dateTime, 'en'));
    }
}

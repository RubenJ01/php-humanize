<?php

namespace Rjds\PhpHumanize\Tests\Formatter\Intl;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\Intl\IntlFormatterBridge;
use Throwable;

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

    public function testFormatDecimalKeepsNegativeTenthsForCommaLocales(): void
    {
        self::assertSame('-0,10', IntlFormatterBridge::formatDecimal(-0.1, 2, 'nl'));
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

    public function testFormatDateKeepsNamedTimezonesInsteadOfForcingUtc(): void
    {
        $dateTime = new DateTimeImmutable('2026-03-30 00:30:00', new DateTimeZone('Asia/Tokyo'));

        self::assertSame('Monday 30 March 2026', IntlFormatterBridge::formatDate($dateTime, 'en'));
    }

    public function testFormatDateThrowsForMalformedOffsetTimezoneWithInvalidFirstDigits(): void
    {
        $this->expectException(Throwable::class);

        IntlFormatterBridge::formatDate(
            $this->withCustomTimezoneName(new DateTimeImmutable('2026-03-30 12:00:00+00:00'), '+0A:00'),
            'en'
        );
    }

    public function testFormatDateThrowsForMalformedOffsetTimezoneWithInvalidSecondDigits(): void
    {
        $this->expectException(Throwable::class);

        IntlFormatterBridge::formatDate(
            $this->withCustomTimezoneName(new DateTimeImmutable('2026-03-30 12:00:00+00:00'), '+00:0A'),
            'en'
        );
    }

    public function testNormalizeLocaleDefaultsToEnglishWhenBlank(): void
    {
        self::assertSame('en', $this->invokePrivate('normalizeLocale', '   '));
    }

    public function testNormalizeLocaleCanonicalizesLocaleAlias(): void
    {
        self::assertSame('nl-NL', $this->invokePrivate('normalizeLocale', 'nl_NL'));
    }

    public function testNormalizeTimezoneReturnsUtcForSignedOffset(): void
    {
        self::assertSame('UTC', $this->invokePrivate('normalizeTimezone', '+02:00'));
    }

    public function testNormalizeTimezoneReturnsUtcForEmptyString(): void
    {
        self::assertSame('UTC', $this->invokePrivate('normalizeTimezone', ''));
    }

    public function testNormalizeTimezoneKeepsNamedTimezone(): void
    {
        self::assertSame('Europe/Amsterdam', $this->invokePrivate('normalizeTimezone', 'Europe/Amsterdam'));
    }

    public function testNormalizeNegativeZeroDoesNotModifyNonNegativeInput(): void
    {
        self::assertSame('1.00', $this->invokePrivate('normalizeNegativeZero', '1.00'));
    }

    public function testIsUtcOffsetTimezoneAcceptsValidPattern(): void
    {
        self::assertTrue($this->invokePrivate('isUtcOffsetTimezone', '-12:30'));
    }

    public function testIsUtcOffsetTimezoneRejectsInvalidPattern(): void
    {
        self::assertFalse($this->invokePrivate('isUtcOffsetTimezone', '+0A:30'));
        self::assertFalse($this->invokePrivate('isUtcOffsetTimezone', 'A0:000'));
        self::assertFalse($this->invokePrivate('isUtcOffsetTimezone', 'UTC'));
        self::assertFalse($this->invokePrivate('isUtcOffsetTimezone', 'x+01:00'));
        self::assertFalse($this->invokePrivate('isUtcOffsetTimezone', '+01:00x'));
        self::assertFalse($this->invokePrivate('isUtcOffsetTimezone', "+01:00\n"));
    }

    private function withCustomTimezoneName(DateTimeImmutable $base, string $timezoneName): DateTimeImmutable
    {
        $timezone = new class ($timezoneName) extends DateTimeZone {
            public function __construct(private readonly string $customName)
            {
                parent::__construct('UTC');
            }

            public function getName(): string
            {
                return $this->customName;
            }
        };

        return new class ($base->format('Y-m-d H:i:s.uP'), $timezone) extends DateTimeImmutable {
            public function __construct(
                string $dateTime,
                private readonly DateTimeZone $timezone
            ) {
                parent::__construct($dateTime);
            }

            public function getTimezone(): DateTimeZone
            {
                return $this->timezone;
            }
        };
    }

    private function invokePrivate(string $methodName, string $value): mixed
    {
        $reflection = new \ReflectionClass(IntlFormatterBridge::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invoke(null, $value);
    }
}

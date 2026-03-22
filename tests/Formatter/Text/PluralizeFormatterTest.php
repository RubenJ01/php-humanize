<?php

namespace Rjds\PhpHumanize\Tests\Formatter\Text;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\PluralizeFormatter;

class PluralizeFormatterTest extends TestCase
{
    private PluralizeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new PluralizeFormatter();
    }

    /**
     * @return array<string, array{int, string, string|null, string}>
     */
    public static function pluralizeProvider(): array
    {
        return [
            'singular item' => [1, 'item', null, '1 item'],
            'multiple items' => [5, 'item', null, '5 items'],
            'zero items' => [0, 'item', null, '0 items'],
            'custom plural form' => [3, 'child', 'children', '3 children'],
            'singular with custom plural' => [1, 'child', 'children', '1 child'],
            'negative quantity' => [-1, 'item', null, '-1 items'],
            'negative multiple' => [-3, 'item', null, '-3 items'],
        ];
    }

    #[DataProvider('pluralizeProvider')]
    public function testItPluralizesCorrectly(int $quantity, string $singular, ?string $plural, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($quantity, $singular, $plural));
    }

    public function testItDefaultsArgumentsWhenNoneAreProvided(): void
    {
        self::assertSame('0 s', $this->formatter->format());
    }

    public function testItCastsQuantityAndWordArguments(): void
    {
        self::assertSame('0 s', $this->formatter->format('foo'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Singular form must be a string');

        $this->formatter->format(2, 5);
    }

    public function testItRejectsInvalidPluralType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Plural form must be a string or null');

        $this->formatter->format(2, 'item', []);
    }
}

<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\ListJoinFormatter;

class ListJoinFormatterTest extends TestCase
{
    private ListJoinFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ListJoinFormatter();
    }

    /**
     * @return array<string, array{array<int, string>, string}>
     */
    public static function listJoinProvider(): array
    {
        return [
            'empty list' => [[], ''],
            'single item' => [['Alice'], 'Alice'],
            'two items' => [['Alice', 'Bob'], 'Alice and Bob'],
            'three items' => [['Alice', 'Bob', 'Charlie'], 'Alice, Bob, and Charlie'],
        ];
    }

    /**
     * @param array<int, string> $items
     */
    #[DataProvider('listJoinProvider')]
    public function testItJoinsLists(array $items, string $expected): void
    {
        self::assertSame($expected, $this->formatter->format($items));
    }

    public function testItJoinsWithCustomConjunction(): void
    {
        self::assertSame('Alice or Bob', $this->formatter->format(['Alice', 'Bob'], 'or'));
    }

    public function testItJoinsWithCustomSeparator(): void
    {
        self::assertSame(
            'Alice; Bob; and Charlie',
            $this->formatter->format(['Alice', 'Bob', 'Charlie'], 'and', '; ')
        );
    }

    public function testItTreatsNonArrayItemsAsEmptyList(): void
    {
        self::assertSame('', $this->formatter->format('not-an-array'));
    }

    public function testItRejectsNonStringConjunction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Conjunction must be a string');

        $this->formatter->format(['Alice', 'Bob'], []);
    }

    public function testItRejectsNonStringSeparator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Separator must be a string');

        $this->formatter->format(['Alice', 'Bob', 'Charlie'], 'and', []);
    }

    public function testItRejectsNonStringItems(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('All list items must be strings');

        $this->formatter->format(['Alice', 123]);
    }

    public function testItNormalizesArrayKeysBeforeJoining(): void
    {
        self::assertSame('Alice and Bob', $this->formatter->format([1 => 'Alice', 2 => 'Bob']));
    }
}

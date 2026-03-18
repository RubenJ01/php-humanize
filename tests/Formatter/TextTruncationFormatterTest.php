<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\TextTruncationFormatter;

class TextTruncationFormatterTest extends TestCase
{
    private TextTruncationFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new TextTruncationFormatter();
    }

    /**
     * @return array<string, array{string, int, string, string}>
     */
    public static function truncationProvider(): array
    {
        return [
            'issue example with ellipsis' => [
                'The quick brown fox jumps over the lazy dog',
                20,
                '…',
                'The quick brown fox…',
            ],
            'issue example without truncation' => [
                'Hello World',
                50,
                '…',
                'Hello World',
            ],
            'issue example with custom suffix' => [
                'The quick brown fox',
                15,
                '...',
                'The quick brown...',
            ],
            'exact max length returns original text' => [
                'Hello World',
                11,
                '…',
                'Hello World',
            ],
            'removes partial trailing word when cut is inside word' => [
                'The quick brown fox',
                17,
                '…',
                'The quick brown…',
            ],
            'returns suffix when first word exceeds max length' => [
                'Supercalifragilisticexpialidocious',
                5,
                '...',
                '...',
            ],
            'returns suffix when max length is zero and text needs truncation' => [
                'Hello World',
                0,
                '...',
                '...',
            ],
            'max length zero truncates single-character text' => [
                'A',
                0,
                '...',
                '...',
            ],
            'negative max length behaves like zero' => [
                'Hello World',
                -1,
                '...',
                '...',
            ],
            'trims trailing spaces before suffix' => [
                'The quick brown    fox',
                16,
                '…',
                'The quick brown…',
            ],
            'handles multibyte characters by character count' => [
                'cafe naïve society',
                10,
                '…',
                'cafe naïve…',
            ],
        ];
    }

    #[DataProvider('truncationProvider')]
    public function testItTruncatesAtWordBoundaries(
        string $text,
        int $maxLength,
        string $suffix,
        string $expected
    ): void {
        self::assertSame($expected, $this->formatter->format($text, $maxLength, $suffix));
    }
}

<?php

namespace Rjds\PhpHumanize\Tests\Formatter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rjds\PhpHumanize\Formatter\FileSizeFormatter;

class FileSizeFormatterTest extends TestCase
{
    private FileSizeFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new FileSizeFormatter();
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function fileSizeProvider(): array
    {
        return [
            'zero bytes' => [0, '0 B'],
            'bytes' => [500, '500 B'],
            'kilobytes' => [1536, '1.5 KB'],
            'megabytes' => [5452595, '5.2 MB'],
            'gigabytes' => [2147483648, '2 GB'],
        ];
    }

    #[DataProvider('fileSizeProvider')]
    public function testItFormatsFileSize(int $bytes, string $expected): void
    {
        $this->assertEquals($expected, $this->formatter->format($bytes));
    }

    public function testItFormatsFileSizeWithCustomPrecision(): void
    {
        $this->assertEquals('1.5 KB', $this->formatter->format(1536, 2));
    }
}

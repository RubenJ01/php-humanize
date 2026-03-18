<?php

namespace Rjds\PhpHumanize\Formatter;

class TextTruncationFormatter
{
    private const WHITESPACE_CHARACTERS = [
        ' ',
        "\t",
        "\n",
        "\r",
        "\v",
        "\f",
    ];

    public function format(string $text, int $maxLength, string $suffix = '…'): string
    {
        $maxLength = max(0, $maxLength);

        $characters = $this->splitCharacters($text);

        if (count($characters) <= $maxLength) {
            return $text;
        }

        $chunk = array_slice($characters, 0, $maxLength);
        $nextCharacter = $characters[$maxLength] ?? null;

        if ($nextCharacter !== null && !$this->isWhitespace($nextCharacter)) {
            // Drop a partial trailing word until we hit whitespace.
            while ($chunk !== [] && !$this->isWhitespace($chunk[array_key_last($chunk)])) {
                array_pop($chunk);
            }
        }

        $chunk = $this->trimTrailingWhitespace($chunk);

        return implode('', $chunk) . $suffix;
    }

    /**
     * @return array<int, string>
     */
    private function splitCharacters(string $text): array
    {
        $characters = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

        return $characters === false ? str_split($text) : $characters;
    }

    private function isWhitespace(string $character): bool
    {
        return in_array($character, self::WHITESPACE_CHARACTERS, true);
    }

    /**
     * @param array<int, string> $characters
     * @return array<int, string>
     */
    private function trimTrailingWhitespace(array $characters): array
    {
        while ($characters !== [] && $this->isWhitespace($characters[array_key_last($characters)])) {
            array_pop($characters);
        }

        return $characters;
    }
}

<?php

namespace Rjds\PhpHumanize;

use InvalidArgumentException;
use Rjds\PhpHumanize\Locale\LocaleNormalizer;

final class HumanizerConfig
{
    public const DEFAULT_LOCALE = HumanizerInterface::LOCALE_EN;
    public const DEFAULT_NUMBER_PRECISION = HumanizerInterface::DEFAULT_NUMBER_PRECISION;
    public const DEFAULT_LIST_CONJUNCTION = 'and';
    public const DEFAULT_TRUNCATE_SUFFIX = '…';

    public function __construct(
        private string $locale = self::DEFAULT_LOCALE,
        private int $numberPrecision = self::DEFAULT_NUMBER_PRECISION,
        private string $listConjunction = self::DEFAULT_LIST_CONJUNCTION,
        private string $truncateSuffix = self::DEFAULT_TRUNCATE_SUFFIX,
    ) {
        $this->locale = LocaleNormalizer::normalizeRequired($this->locale, 'locale');
        $this->numberPrecision = self::normalizePrecision($this->numberPrecision);
        $this->listConjunction = self::normalizeRequiredString($this->listConjunction, 'listConjunction');
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getNumberPrecision(): int
    {
        return $this->numberPrecision;
    }

    public function getListConjunction(): string
    {
        return $this->listConjunction;
    }

    public function getTruncateSuffix(): string
    {
        return $this->truncateSuffix;
    }

    public function withLocale(string $locale): self
    {
        return new self($locale, $this->numberPrecision, $this->listConjunction, $this->truncateSuffix);
    }

    public function withNumberPrecision(int $numberPrecision): self
    {
        return new self($this->locale, $numberPrecision, $this->listConjunction, $this->truncateSuffix);
    }

    public function withListConjunction(string $listConjunction): self
    {
        return new self($this->locale, $this->numberPrecision, $listConjunction, $this->truncateSuffix);
    }

    public function withTruncateSuffix(string $truncateSuffix): self
    {
        return new self($this->locale, $this->numberPrecision, $this->listConjunction, $truncateSuffix);
    }

    private static function normalizeRequiredString(string $value, string $field): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidArgumentException(sprintf('%s cannot be empty.', $field));
        }

        return $normalized;
    }

    private static function normalizePrecision(int $numberPrecision): int
    {
        if ($numberPrecision < 0) {
            throw new InvalidArgumentException('numberPrecision must be greater than or equal to 0.');
        }

        return $numberPrecision;
    }
}

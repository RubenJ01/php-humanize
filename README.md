# PHP Humanize

![Version](https://img.shields.io/github/v/release/RubenJ01/php-humanize?label=version)
[![Packagist Downloads](https://img.shields.io/packagist/dt/rjds/php-humanize)](https://packagist.org/packages/rjds/php-humanize)
[![codecov](https://codecov.io/github/RubenJ01/php-humanize/graph/badge.svg?token=RE8FQRMMCL)](https://codecov.io/github/RubenJ01/php-humanize)
![License](https://img.shields.io/github/license/RubenJ01/php-humanize)

A PHP library to convert machine data into human-readable strings.

## Installation

Install from Packagist:

```bash
composer require rjds/php-humanize
```

If you use a private Composer mirror, add your repository configuration first and then run the same require command.

## Overview

```php
use DateTimeImmutable;
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();
$fiveMinutesAgo = new DateTimeImmutable('-5 minutes');

$humanizer->fileSize(5452595);                // "5.2 MB"
$humanizer->dataRate(1536);                   // "1.5 KB/s"
$humanizer->ordinal(21);                      // "21st"
$humanizer->abbreviate(2300000);              // "2.3M"
$humanizer->number(1234567.89, 2, Humanizer::LOCALE_NL); // "1.234.567,89"
$humanizer->percentage(0.153, 1);             // "15.3%"
$humanizer->toWords(42);                      // "forty-two"
$humanizer->duration(3661);                   // "1 hour, 1 minute, 1 second"
$humanizer->diffForHumans($fiveMinutesAgo);   // "5 minutes ago"
$humanizer->readableDate(new DateTimeImmutable('2026-03-30'), Humanizer::LOCALE_NL); // "Maandag 30 maart 2026"
$humanizer->pluralize(3, 'child', 'children');// "3 children"
$humanizer->joinList(['A', 'B', 'C']);        // "A, B, and C"
$humanizer->truncate('The quick brown fox jumps over the lazy dog', 20); // "The quick brown fox…"
```

For detailed usage and all available options, see the [GitHub Wiki](https://github.com/RubenJ01/php-humanize/wiki).

## Quick Start

The overview above covers all built-in formatters. For more details on each one, visit the wiki pages for [File Size](https://github.com/RubenJ01/php-humanize/wiki/File-Size), [Numbers](https://github.com/RubenJ01/php-humanize/wiki/Numbers), [Durations](https://github.com/RubenJ01/php-humanize/wiki/Duration), [Date Formatting](https://github.com/RubenJ01/php-humanize/wiki/Date-Formatting), and [more](https://github.com/RubenJ01/php-humanize/wiki).

### Configure defaults globally

Use `HumanizerConfig` to centralize locale, precision, conjunction, and truncation defaults across your entire application:

```php
use Rjds\PhpHumanize\Humanizer;
use Rjds\PhpHumanize\HumanizerConfig;

$config = new HumanizerConfig(
    locale: Humanizer::LOCALE_NL,
    numberPrecision: 2,
    listConjunction: 'or',
    truncateSuffix: '...'
);

$humanizer = new Humanizer(config: $config);

echo $humanizer->number(1234.56);           // 1.234,56
echo $humanizer->percentage(0.125);         // 12,50%
echo $humanizer->joinList(['A', 'B']);      // A or B
echo $humanizer->truncate('long text', 5); // lo...
```

### Intl-powered formatting

As of v3, `ext-intl` (ICU) is a hard dependency. Built-in number, percentage, and date formatters now rely on ICU locale rules instead of internal locale mapping tables.

```php
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;

$number = new NumberFormatter();        // ICU locale-aware formatting
$percent = new PercentageFormatter();   // ICU locale-aware formatting
$date = new DateFormatter();            // ICU locale-aware formatting

// Force English-only fallback behavior:
$stableNumber = new NumberFormatter(preferIntl: false);
$stablePercent = new PercentageFormatter(preferIntl: false);
$stableDate = new DateFormatter(preferIntl: false);
```

## Upgrade to v3

v3 introduces breaking changes around localization and deprecated APIs.

- `ext-intl` is now required.
- `Rjds\PhpHumanize\Formatter\DateTime\DateLocalizedFormatter` was removed; use `DateFormatter`.
- `NumberFormatter` constructor changed from `__construct(array $localeFormats = [])` to `__construct(bool $preferIntl = true)`.
- `PercentageFormatter` constructor changed from `__construct(array $localeFormats = [])` to `__construct(bool $preferIntl = true)`.

Before:

```php
use Rjds\PhpHumanize\Formatter\DateTime\DateLocalizedFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;

$date = new DateLocalizedFormatter();
$number = new NumberFormatter(['fr' => [',', ' ']]);
$percent = new PercentageFormatter(['fr' => [',', ' ']]);
```

After:

```php
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;

$date = new DateFormatter();
$number = new NumberFormatter(); // ICU locale-aware
$percent = new PercentageFormatter(); // ICU locale-aware
```

### Extend with custom formatters

```php
use Rjds\PhpHumanize\Formatter\FormatterInterface;
use Rjds\PhpHumanize\Humanizer;

class MyFormatter implements FormatterInterface {
    public function format(...$args): string {
        return "custom: " . $args[0];
    }

    public function getName(): string {
        return 'my';
    }
}

$humanizer = new Humanizer();
$humanizer->register('my', new MyFormatter());
echo $humanizer->my('hello'); // custom: hello
```

See [Custom Formatters](https://github.com/RubenJ01/php-humanize/wiki/Custom-Formatters) in the wiki for patterns and best practices.

## Development

```bash
composer install
php vendor/bin/grumphp run
```

Run mutation testing:

```bash
php vendor/bin/infection --threads=4
```

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines, local quality checks, and pull request workflow.

## License

This project is released under the MIT License. See [LICENSE](LICENSE) for details and [CHANGELOG.md](CHANGELOG.md) for release history.

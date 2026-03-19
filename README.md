# PHP Humanize

![Version](https://img.shields.io/github/v/release/RubenJ01/php-humanize?label=version)
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
$humanizer->toWords(42);                      // "forty-two"
$humanizer->duration(3661);                   // "1 hour, 1 minute, 1 second"
$humanizer->diffForHumans($fiveMinutesAgo);   // "5 minutes ago"
$humanizer->pluralize(3, 'child', 'children');// "3 children"
$humanizer->joinList(['A', 'B', 'C']);        // "A, B, and C"
$humanizer->truncate('The quick brown fox jumps over the lazy dog', 20); // "The quick brown fox…"
```

For detailed usage and all available options, see the [GitHub Wiki](https://github.com/RubenJ01/php-humanize/wiki).

## Common Usage

### File sizes

```php
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();

echo $humanizer->fileSize(5452595); // 5.2 MB
```

### Durations

```php
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();

echo $humanizer->duration(3661); // 1 hour, 1 minute, 1 second
```

### Time differences

```php
use DateTimeImmutable;
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();
$fiveMinutesAgo = new DateTimeImmutable('-5 minutes');

echo $humanizer->diffForHumans($fiveMinutesAgo); // 5 minutes ago
```

### Pluralization

```php
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();

echo $humanizer->pluralize(3, 'child', 'children'); // 3 children
```

### Custom Formatters

Need a formatter that's not built-in? Create and register your own in seconds:

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

See [Custom Formatters](https://github.com/RubenJ01/php-humanize/wiki/Custom-Formatters) in the wiki for more details, examples, and best practices.

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

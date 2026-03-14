# PHP Humanize

![Version](https://img.shields.io/github/v/release/RubenJ01/php-humanize?label=version)
[![codecov](https://codecov.io/github/RubenJ01/php-humanize/graph/badge.svg)](https://codecov.io/github/RubenJ01/php-humanize)
![License](https://img.shields.io/github/license/RubenJ01/php-humanize)

A PHP library to convert machine data into human-readable strings.

## Installation

Add these lines to your composer.json file, or add a new repository URL if you already have one or more:

```json
{
    "repositories": [
        {"type": "composer", "url": "https://ruben-jakob-digital-solutions.repo.repman.rubenjakob.com"}
    ]
}
```

Then require the package:

```bash
composer require rjds/php-humanize
```

## Overview

```php
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();

$humanizer->fileSize(5452595);                // "5.2 MB"
$humanizer->ordinal(21);                      // "21st"
$humanizer->abbreviate(2300000);              // "2.3M"
$humanizer->toWords(42);                      // "forty-two"
$humanizer->duration(3661);                   // "1 hour, 1 minute, 1 second"
$humanizer->diffForHumans($fiveMinutesAgo);   // "5 minutes ago"
$humanizer->pluralize(3, 'child', 'children');// "3 children"
$humanizer->joinList(['A', 'B', 'C']);        // "A, B, and C"
```

For detailed usage and all available options, see the [full documentation](docs/usage.md).

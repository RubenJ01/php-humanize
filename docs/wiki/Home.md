# PHP Humanize Wiki

Welcome to the php-humanize wiki.

## Getting Started

- [Installation](Installation)
- [File Size](File-Size)
- [Data Rate](Data-Rate)
- [Ordinals](Ordinals)
- [Numbers](Numbers)
- [Duration](Duration)
- [Time Difference](Time-Difference)
- [Pluralization](Pluralization)
- [List Joining](List-Joining)

## Quick Example

```php
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();

echo $humanizer->fileSize(1536);      // 1.5 KB
echo $humanizer->dataRate(1048576);   // 1 MB/s
echo $humanizer->ordinal(21);         // 21st
```


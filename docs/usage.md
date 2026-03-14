# Usage

Getting started with php-humanize is simple.

```php
use Rjds\PhpHumanize\Humanizer;

$humanizer = new Humanizer();
```

## File Size

Convert bytes to a human-readable file size:

```php
// Outputs: 1.5 KB
echo $humanizer->fileSize(1536);

// Outputs: 5.2 MB
echo $humanizer->fileSize(5452595);

// Outputs: 2 GB
echo $humanizer->fileSize(2147483648);

// Custom precision
// Outputs: 1.5 KB
echo $humanizer->fileSize(1536, 2);
```

## Ordinals

Convert a number to its ordinal form:

```php
// Outputs: 1st
echo $humanizer->ordinal(1);

// Outputs: 2nd
echo $humanizer->ordinal(2);

// Outputs: 3rd
echo $humanizer->ordinal(3);

// Outputs: 11th
echo $humanizer->ordinal(11);

// Outputs: 21st
echo $humanizer->ordinal(21);
```

## Number Abbreviation

Abbreviate large numbers to a short form:

```php
// Outputs: 1.5K
echo $humanizer->abbreviate(1500);

// Outputs: 2.3M
echo $humanizer->abbreviate(2300000);

// Outputs: 1B
echo $humanizer->abbreviate(1000000000);

// Small numbers are returned as-is
// Outputs: 999
echo $humanizer->abbreviate(999);

// Negative numbers are supported
// Outputs: -1.5K
echo $humanizer->abbreviate(-1500);
```

## Number to Words

Convert a number into its written word form:

```php
// Outputs: forty-two
echo $humanizer->toWords(42);

// Outputs: one thousand
echo $humanizer->toWords(1000);

// Outputs: one million, two hundred thirty-four thousand, five hundred sixty-seven
echo $humanizer->toWords(1234567);

// Negative numbers are supported
// Outputs: negative forty-two
echo $humanizer->toWords(-42);
```

## Time Difference

Express a datetime as a human-readable difference:

```php
use DateTimeImmutable;

$now = new DateTimeImmutable();

// Outputs: 5 minutes ago
echo $humanizer->diffForHumans($now->modify('-5 minutes'), $now);

// Outputs: 3 hours ago
echo $humanizer->diffForHumans($now->modify('-3 hours'), $now);

// Outputs: 2 weeks ago
echo $humanizer->diffForHumans($now->modify('-14 days'), $now);

// Outputs: in 2 hours
echo $humanizer->diffForHumans($now->modify('+2 hours'), $now);

// Outputs: just now
echo $humanizer->diffForHumans($now->modify('-10 seconds'), $now);
```

## Quantity Pluralization

Convert a quantity and noun into a correctly pluralized string:

```php
// Outputs: 1 item
echo $humanizer->pluralize(1, 'item');

// Outputs: 5 items
echo $humanizer->pluralize(5, 'item');

// Custom plural form for irregular nouns
// Outputs: 1 child
echo $humanizer->pluralize(1, 'child', 'children');

// Outputs: 3 children
echo $humanizer->pluralize(3, 'child', 'children');

// Zero uses the plural form
// Outputs: 0 items
echo $humanizer->pluralize(0, 'item');
```

## List Joining

Join an array of items into a natural-language list:

```php
// Outputs: Alice, Bob, and Charlie
echo $humanizer->joinList(['Alice', 'Bob', 'Charlie']);

// Outputs: Alice and Bob
echo $humanizer->joinList(['Alice', 'Bob']);

// Custom conjunction
// Outputs: Alice or Bob
echo $humanizer->joinList(['Alice', 'Bob'], 'or');

// Custom separator
// Outputs: Alice; Bob; and Charlie
echo $humanizer->joinList(['Alice', 'Bob', 'Charlie'], 'and', '; ');
```

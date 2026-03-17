# Numbers

## Number Abbreviation

Use `abbreviate()` to shorten large numbers.

```php
// 1.5K
echo $humanizer->abbreviate(1500);

// 2.3M
echo $humanizer->abbreviate(2300000);

// 1B
echo $humanizer->abbreviate(1000000000);
```

## Number to Words

Use `toWords()` to write numbers in words.

```php
// forty-two
echo $humanizer->toWords(42);

// one thousand
echo $humanizer->toWords(1000);

// one million, two hundred thirty-four thousand, five hundred sixty-seven
echo $humanizer->toWords(1234567);
```


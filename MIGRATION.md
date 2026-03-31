# Migration Guide

Use this document for all major-version upgrade notes.

## v2 to v3

v3 introduces breaking changes around localization and deprecated APIs.

### Breaking Changes

- `ext-intl` is now required.
- `Rjds\PhpHumanize\Formatter\DateTime\DateLocalizedFormatter` was removed; use `DateFormatter`.
- `NumberFormatter` constructor changed from `__construct(array $localeFormats = [])` to `__construct(bool $preferIntl = true)`.
- `PercentageFormatter` constructor changed from `__construct(array $localeFormats = [])` to `__construct(bool $preferIntl = true)`.

### Before

```php
use Rjds\PhpHumanize\Formatter\DateTime\DateLocalizedFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;

$date = new DateLocalizedFormatter();
$number = new NumberFormatter(['fr' => [',', ' ']]);
$percent = new PercentageFormatter(['fr' => [',', ' ']]);
```

### After

```php
use Rjds\PhpHumanize\Formatter\DateTime\DateFormatter;
use Rjds\PhpHumanize\Formatter\Number\NumberFormatter;
use Rjds\PhpHumanize\Formatter\Number\PercentageFormatter;

$date = new DateFormatter();
$number = new NumberFormatter(); // ICU locale-aware
$percent = new PercentageFormatter(); // ICU locale-aware
```

### Notes

- v3 uses ICU locale rules through `ext-intl` for built-in number, percentage, and date formatting.
- If needed, you can force English-only behavior in these formatters with `preferIntl: false`.

## v1 to v2

v2 introduced dynamic formatter registry support and runtime formatter registration.

### Breaking Changes

- `HumanizerInterface` added:
  - `getRegistry(): FormatterRegistry`
  - `register(string $name, FormatterInterface $formatter): self`
  - `apply(string $formatterName, ...$args): string`

### Who Needs to Change Code

- If you implemented your own `HumanizerInterface` class, implement the new methods above.
- If you only used the built-in `Humanizer` class methods (`fileSize`, `duration`, `truncate`, etc.), no migration changes are required for this step.

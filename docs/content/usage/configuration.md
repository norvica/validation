---
title: "Configuration"
description: ""
summary: ""
date: 2024-04-21T19:54:40+02:00
lastmod: 2024-04-21T19:54:40+02:00
draft: false
weight: 190
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The [**Options**](https://github.com/norvica/validation/blob/main/src/Options.php) class enables control over how your validator processes data and handles errors.

- `throw` (boolean, default: `true`): Controls whether the validator throws an exception immediately on the first
  violation (`true`) or aggregates all violations into the result (`false`).
- `strict` (boolean, default: `true`): Determines if an exception is thrown when data properties lack corresponding
  validation rules.

**Providing Options**:

1. **Per Validation**: Pass an `Options` instance as an optional argument to the `validate()` method to customize
   behavior for a specific validation run.
2. **Validator-Wide Defaults**: During validator instantiation, provide an `Options` object to set default behaviors for
   all subsequent validations performed by that validator instance.

**Example**:

```php
use Norvica\Validation\Options;
use Norvica\Validation\Validator;

// Configure the validator's default behavior to aggregate violations
// instead of throwing an exception on the first error.
$options = new Options(throw: false);
$validator = new Validator(options: $options);

// Perform validation using the (non-throwing) behavior.
$result = $validator->validate(value: $data, rules: $rules);

// Override the default behavior for a single validation run. This will
// throw an exception on the first violation encountered.
$validator->validate(value: $data, rules: $rules, options: new Options(throw: true));
```

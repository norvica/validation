---
title: "Composing Rule Sets"
description: ""
summary: ""
date: 2024-04-21T17:45:42+02:00
lastmod: 2024-05-23T19:58:35+02:00
draft: false
weight: 150
toc: true
seo:
  title: "" # custom title (optional)
  description: "Learn how to compose complex validation rules using logical operators (AND/OR), handle optional values, and dynamically create rule sets based on data." # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The library offers multiple methods for composing rule sets, giving you control over how you build your validation
logic.

## Logical Operators

- `AndX`: Use this instruction when you want **_all_** of a set of rules to pass for a given value.
- `OrX`:  Use this instruction when you want **_at least one_** of a set of rules to pass for a given value.

Example:

```php
use Norvica\Validation\Instruction\AndX;
use Norvica\Validation\Instruction\OrX;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Uuid;

// Value must be either a valid email OR a valid UUID
$validator->validate('john.doe@example.com', new OrX(new Email(), new Uuid())); // will pass
$validator->validate('e2575f66-47ea-4152-ba1e-0ed63dec1e4f', new OrX(new Email(), new Uuid())); // will pass

// Value must be BOTH a valid email AND should not exist in your system (your imaginary custom rule)
$validator->validate('john.doe@example.com', new AndX(new Email(), new Unique(table: 'users', column: 'username')));
```

## Optional Values

Use the `OptionalX` instruction to mark a rule as optional. This prevents validation errors if the corresponding
property is not present in your data or if its value is `null`.

```php
use Norvica\Validation\Instruction\OptionalX;
use Norvica\Validation\Rule\Url;

$data = [];  // or `['website' => null]`

// without `OptionalX`: would throw an exception if 'website' is missing or `null`
$validator->validate(value: $data, rules: ['website' => new Url()]);

// with `OptionalX`: validation of 'website' is skipped if missing or `null`
$validator->validate(value: $data, rules: ['website' => new OptionalX(new Url())]);
```

## Dynamic Rules

For more complex scenarios, create rule sets dynamically based on data. This allows fine-grained
control over which rules are applied.

```php
class YourRulesRegistry
{
    public static function profileRules(array $data): array
    {
        $rules = [];
        if (!empty($data['website'])) {
            $rules['website'] = new Url();
        }

        return $rules;
    }
}

$data = [];  // or `['website' => null]`

// compose rules based on provided data
$rules = YourRulesRegistry::profileRules($data);

// validate
$validator->validate(value: $data, rules: $rules);
```

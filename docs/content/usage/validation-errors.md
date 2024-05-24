---
title: "Validation Errors"
description: ""
summary: ""
date: 2024-04-21T19:33:08+02:00
lastmod: 2024-05-23T19:58:35+02:00
draft: false
weight: 170
toc: true
seo:
  title: "" # custom title (optional)
  description: "Handle PHP validation errors by either catching thrown exceptions for immediate feedback or aggregating violations for comprehensive error reporting." # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The `validate()` method offers flexibility in how validation errors are handled. Here's how to work with the different
modes.

## Throwing Exceptions (Default Behavior)

By default, the first validation failure will throw a `PropertyRuleViolation` exception. This is useful for scenarios
where you want immediate feedback and error handling. `PropertyRuleViolation` indicates that a specific property has
violated a validation rule.

**Example**:

```php
use Norvica\Validation\Exception\PropertyRuleViolation;

$data = ['email' => 'john.doe', 'password' => 'P4$$w0rd'];
$rules = ['email' => new Email(), 'password' => new Password()];

try {
    $validator->validate($data, $rules);
} catch (PropertyRuleViolation $e) {
    $e->getMessage(); // "email: Value must be a valid E-mail address"
    $e->getPath(); // "email"
    $e->getText(); // "Value must be a valid E-mail address"
}
```

## Aggregating Violations

To collect all validation violations instead of stopping at the first one, pass an `Options` instance to
the `validate()`
method, setting the `throw` option to `false`.

**Example**:

```php
use Norvica\Validation\Result;
use Norvica\Validation\Options;
use Norvica\Validation\Exception\PropertyRuleViolation;

$data = ['email' => 'john.doe', 'password' => 'P4$$w0rd'];
$rules = ['email' => new Email(), 'password' => new Password()];

$result = $validator->validate($data, $rules, new Options(throw: false));

if (!empty($result->violations)) {
    // handle all errors the way you'd like, for instance:
    foreach ($result->violations as $violation) {
        echo $violation->getPath() . ": " . $violation->getText() . "\n";
    }
}
```

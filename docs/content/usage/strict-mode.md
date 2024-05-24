---
title: "Strict Mode"
description: ""
summary: ""
date: 2024-04-21T19:44:18+02:00
lastmod: 2024-05-23T19:58:35+02:00
draft: false
weight: 180
toc: true
seo:
  title: "" # custom title (optional)
  description: "This page explains strict mode, which enforces explicit validation rules for all data properties and how to disable it." # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

By default, the validator operates in "strict" mode. This means that the library expects you to define explicit
validation rules for **_all_** properties within an object or keys within an array that you pass for validation. If a
property/key lacks a corresponding rule, the library will throw an exception.

## Purpose

Strict mode helps enforce data integrity and can prevent unexpected behavior. By requiring explicit validation, it
encourages developers to think carefully about the expected format and constraints of the data they are handling.

**Example (Strict Mode):**

```php
$data = ['email' => 'john.doe@example.com'];
$rules = [];

$validator->validate($data, $rules); // will throw a `LogicException` with message "email: Validation rule is not configured."
```

## Disabling Strict Mode

If needed, you can disable strict mode by passing the `strict: false` flag to the `validate()` method. This allows you
to validate only the data for which you've explicitly provided rules, while ignoring other properties or array elements.

**Example (Non-Strict Mode):**

```php
use Norvica\Validation\Options;

$data = ['email' => 'john.doe@example.com'];
$rules = [];

$validator->validate($data, $rules, new Options(strict: false)); // will pass
```

{{< callout context="caution" icon="alert-triangle" >}}
Use non-strict mode with caution. Always consider the data integrity requirements of your application.
{{< /callout >}}

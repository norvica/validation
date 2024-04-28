---
title: "Quick Start"
description: ""
summary: ""
date: 2024-04-21T17:45:42+02:00
lastmod: 2024-04-21T17:45:42+02:00
draft: false
weight: 110
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

This PHP validation library aims to provide a powerful yet streamlined solution for validating your data. It offers a
core set of commonly used validation rules, along with the essential tools to easily define your own custom rules. The
focus is on simplicity, organization, and flexibility.

Requires **PHP 8.2+**.

{{< callout context="tip" icon="square-check" >}}
Use the validator for DTOs (Data Transfer Objects) and structured data. Avoid using it for validating complex objects
like domain entities, which should enforce their validity through internal logic.
{{< /callout >}}

## Install

This library is installed using Composer. If you don't have Composer, you can get it from
[getcomposer.org](https://getcomposer.org).

In your project's root directory, run the following command:

```bash
composer require norvica/validation
```

## Instantiate

To start using the library, you'll first create an instance of the Validator class:

```php
use Norvica\Validation\Validator;

$validator = new Validator();
```

## Validate

To validate a single value, use the `Validator::validate()`.

```php
use Norvica\Validation\Rule\Email;

$validator->validate('john.doe@example.com', new Email());
```

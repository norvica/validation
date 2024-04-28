---
title : "Validation"
description: ""
lead: "A simple and extensible PHP validation library"
date: 2024-04-21T19:33:08+02:00
lastmod: 2024-04-21T19:33:08+02:00
draft: false
seo:
 title: "PHP validation library" # custom title (optional)
 description: "" # custom description (recommended)
 canonical: "" # custom canonical URL (optional)
 noindex: false # false (default) or true
---

## Validate Scalar Values

Validate single values such as strings, numbers, booleans.

```php
$validator->validate(
    value: 'john.doe@example.com',
    rules: new Email(),
);
```

[Learn more about scalar values validation →](/usage/validation/#single-value)

## Validate Arrays

Validate arrays of data, applying rules to individual elements.

```php
$validator->validate(
    value: ['email' => 'john.doe@example.com'],
    rules: ['email' => new Email()],
);
```

[Learn more about array validation →](/usage/validation/#arrays)

## Validate Objects

Validate data objects, ensuring that their properties conform to specific rules.

```php
readonly class SomeDto {
    public function __construct(
        #[Email]
        public string $email,
    ) {}
}

$validator->validate(
    value: new SomeDto(email: 'john.doe@example.com'),
);
```

[Learn more about object validation →](/usage/validation/#objects)

## Validate Collections

Validate collections of values.

```php
$validator->validate(
    value: ['127.0.0.1', '0.0.0.0'],
    rules: new EachX(new Ip()),
);
```

[Learn more about collection validation →](/usage/validation/#collections)

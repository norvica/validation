---
title: "Custom Rules"
description: ""
summary: ""
date: 2024-04-21T18:51:00+02:00
lastmod: 2024-05-23T19:58:35+02:00
draft: false
weight: 140
toc: true
seo:
  title: "This page explains how to create custom validation rules by defining rule and validator classes, using optional normalizers, and managing validator dependencies." # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The library provides flexibility by allowing you to extend its functionality with custom validation rules. Here's the
process for defining your own rules.

## Create a Rule Class

- Your rule class must implement the `Rule` interface provided by the library.
- Use constructor arguments to allow for customization of your rule's behavior (e.g., the `transparency` option in
  the `HexColor` example).
- Include the `#[\Attribute]` annotation if you want to use your rule as an attribute (optional).
- Implement the `validator()` method, returning the fully qualified name of your validator class that you'll create
  next.

Example:

```php
use Norvica\Validation\Rule\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class HexColor implements Rule
{
    public function __construct(
        public bool $transparency = false,
    ) {}

    public static function validator(): string
    {
        return HexColorValidation::class;
    }
}
```

## Create a Validator Class

- Your validator should be a callable class (often a single-method class, as in the example).
- The validator's __invoke() method will be called during the validation process. It receives:
  - `$value`: The value being validated.
  - `$rule`: An instance of your rule class.
- Inside the `__invoke()` method, perform the necessary validation logic. If the validation fails, throw a
  `ValueRuleViolation` exception.

Example:

```php
use Norvica\Validation\Exception\ValueRuleViolation;

final class HexColorValidation
{
    public function __invoke(
        string $value,
        HexColor $rule,
    ): void {
        if (!preg_match($rule->transparency ? '/^#(?:[0-9a-fA-F]{3,4}){1,2}$/' : '/^#(?:[0-9a-fA-F]{3}){1,2}$/', $value)) {
            throw new ValueRuleViolation('Value must be a HEX color');
        }
    }
}
```

If your validator requires some **dependencies**, please refer to the [Validator Dependencies](#validator-dependencies)
documentation section.

## Use Your Custom Rule

Instantiate your custom rule object and include it in your validation rules array, just like any built-in rule.

Example:

```php
$data = ['color' => '#5e759cff'];
$rules = ['color' => new HexColor(transparency: true)];

$validator->validate($data, $rules);
```

## Use Built-in or Custom Normalizers (Optional)

The library allows you to apply normalizers to your data before validation, providing a way to preprocess and clean
up values. To use normalizers with your custom rule:

- Implement the `Normalizable` interface: Add the `Normalizable` interface to your rule class.
- Implement the `normalizers()` method: This method should return an array of normalizer objects provided by the
  library or your own custom normalizers.

```php
use Norvica\Validation\Rule\Rule;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class HexColor implements Rule, Normalizable
{
    // ...

    public function normalizers(): array
    {
        return [
            new Trim(),
        ];
    }
}
```

You can use one of the [built-in normalizers](https://github.com/norvica/validation/tree/main/src/Normalizer)
(which are simple callables) or create your own.

- `Binary`: Converts values to boolean `true` or `false`. Recognizes various representations of "on" (e.g., "On",
  "Yes", "1") and "off" (e.g., "Off", "No", "0").
- `DateTime`: Converts value to a `DateTimeImmutable` instance.
- `Lower`: Converts all characters within a string to lowercase.
- `Upper`: Converts all characters within a string to uppercase.
- `Numeric`: Converts numeric string to a number.
- `Spaceless`: Removes all space characters from a string.
- `Trim`: Removes leading and trailing whitespace from a string.

{{< callout context="note" icon="info-circle" >}}
Normalizers modify the value **before** it reaches your validator. This can be useful for tasks like trimming
whitespace, converting to lowercase, or other transformations.
{{< /callout >}}

{{< callout context="note" icon="info-circle" >}}
Normalizers **do not** change the original data; they provide a normalized copy for the validation process.
{{< /callout >}}

## Validator Dependencies

When creating custom validators that have external dependencies (like database connections or other services), you'll
need a way to provide these dependencies to your validator instances. The library offers flexibility in how you approach
this.

Let's assume you have a custom rule `UniqueConstraint` and its corresponding validator `UniqueConstraintValidation`,
which requires a `PDO` instance.

**Your rule**:

```php
use Norvica\Validation\Rule\Rule;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class UniqueConstraint implements Rule
{
    public function __construct(
        public string $table,
        public string $column,
    ) {
    }

    public static function validator(): string
    {
        return UniqueConstraintValidator::class;
    }
}
```

**Your validator**:

```php
final readonly class UniqueConstraintValidation
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function __invoke(string $value, UniqueConstraint $rule): void
    {
        // ...
    }
}
```

### Pass In a Map (Simplest)

For straightforward use cases, create a map of validator class names to their instances and pass this directly to the
Validator constructor.

```php
use Norvica\Validation\Validator;

$pdo = new PDO('<your-connection-parameters>');
$validation = new UniqueConstraintValidation($pdo);

$validator = new Validator(registry: [
    UniqueConstraintValidation::class => $validation,
]);
```

### Implement Your Registry

Create a custom `Registry` implementation to manage the creation of validator instances and their dependencies.

**Your registry**:

```php
use Norvica\Validation\Exception\LogicException;
use Norvica\Validation\Registry\Registry;

class YourRegistry implements Registry
{
    public function get(string $validator): callable
    {
        if (!$this->has($validator)) {
            throw new LogicException("Validator '{$validator}' not found.");
        }

        return ($this->instances()[$validator])();
    }

    public function has(string $validator): bool
    {
        return isset($this->instances()[$validator]);
    }

    private function instances(): array
    {
        return [
            UniqueConstraintValidation::class => function () {
                $pdo = new PDO('<your-connection-parameters>');

                return new UniqueConstraintValidation($pdo);
            },
        ];
    }
}
```

**Use**:

```php
$validator = new Validator(registry: $yourRegistry);
```

### Implement a Container Adapter

If you're using a PSR-11 compatible dependency injection container (or any other DI container), create an adapter to
leverage it.

**Your adapter**:

```php
use Norvica\Validation\Exception\LogicException;
use Norvica\Validation\Registry\Registry;

class YourAdapter implements Registry
{
    public function __construct(
        private \Psr\Container\ContainerInterface $container, // assuming you're using PSR container
    ) {
    }

    public function get(string $validator): callable
    {
        return $this->container->get($validator);
    }

    public function has(string $validator): bool
    {
        return $this->container->has($validator);
    }
}
```

**Use**:

```php
$validator = new Validator(registry: $yourAdapter);
```

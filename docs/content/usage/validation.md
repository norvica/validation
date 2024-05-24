---
title: "Validation"
description: ""
summary: ""
date: 2024-04-21T17:45:42+02:00
lastmod: 2024-05-23T19:58:35+02:00
draft: false
weight: 120
toc: true
seo:
  title: "" # custom title (optional)
  description: "Learn how to validate single values, arrays, objects, and collections." # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

## Single Value

To validate a single value, use the `Validator::validate()`. The method takes two arguments:

1. The value to validate: This is a data you want to check.
2. A rule object: This object represents the validation rule you want to apply. The library provides various built-in
   rules, and you can also create custom rules.

```php
use Norvica\Validation\Rule\Email;

$validator->validate('john.doe@example.com', new Email());
```

## Arrays

The library can validate arrays of data. Here's how you would do it:

1. Create an array of data: This array will contain the key-value pairs you want to validate.
2. Create an array of rules: The keys of this array should match the keys of your data array. The values are rule
   objects that define the validation criteria for each field.
3. Pass data and rules to the `validate()` method: The validator will check each value in the data array against its
   corresponding rule.

```php
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Password;

$data = ['email' => 'john.doe@example.com', 'password' => 'P4$$w0rd'];

$rules = ['email' => new Email(), 'password' => new Password()];

$validator->validate($data, $rules);
```

## Objects

### Instances of `stdClass`

You can apply validation rules to objects as well. When working with `stdClass` instances, the process is similar to
validating arrays:

1. Create an instance of `stdClass`: This object will hold the properties you want to validate.
2. Create an array of rules: The keys of your rules array should correspond to the property names within your stdClass
   object. The values will be rule objects.
3. Pass the object and rules to the `validate()` method: The validator will treat the properties of your object as
   individual values and apply the corresponding rules.

```php
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Password;

$data = new \stdClass();
$data->email = 'john.doe@example.com';
$data->password = 'P4$$w0rd';

$rules = ['email' => new Email(), 'password' => new Password()];

$validator->validate($data, $rules);
```

### Data Transfer Objects

Data Transfer Objects (DTOs) are simple objects designed to carry data between different parts of your application. Your
validation library can streamline the process of ensuring that DTOs contain valid data. Here are two common approaches:

1. **Using an array of rules**
    - Similar to validating stdClass objects, create an array of rules where the keys match the names of the properties
      on your DTO.
    - Pass the DTO and the rules array to the `validate()` method.

    ```php
    use Norvica\Validation\Rule\Email;
    use Norvica\Validation\Rule\Password;

    readonly class LoginDto {
        public function __construct(
            public string $email,
            public string $password,
        ) {}
    }

    $data = new LoginDto(email: 'john.doe@example.com', password: 'P4$$w0rd');

    $rules = ['email' => new Email(), 'password' => new Password()];

    $validator->validate($data, $rules);
    ```

2. **Using rule attributes**
    - Define rule attributes on the DTO properties.
    - Pass the DTO to the `validate()` method (the validator would need to be able to read and interpret these
      attributes).

    ```php
    use Norvica\Validation\Rule\Email;
    use Norvica\Validation\Rule\Password;

    readonly class LoginDto {
        public function __construct(
            #[Email]
            public string $email,
            #[Password]
            public string $password,
        ) {}
    }

    $data = new LoginDto(email: 'john.doe@example.com', password: 'P4$$w0rd');

    $validator->validate($data);
    ```

## Collections

The library provides seamless ways to validate collections.

### Arrays of Values with `EachX`

Use the `EachX` instruction to apply the same validation rule to every element within an array.

Example:

```php
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Rule\Ip;

$validator->validate(['127.0.0.1', '0.0.0.0'], new EachX(new Ip()));

```

### Nested Arrays or Objects

Create nested validation rule arrays to define rules for specific elements within arrays or objects.
The keys in your rules array should match the keys of the data being validated.

Example:

```php
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Rule\Uuid;

$data = [
    'users' => [
        ['id' => '82de6575-7853-42d3-b962-dc466de7ce10'],
        ['id' => 'e2575f66-47ea-4152-ba1e-0ed63dec1e4f'],
    ]
];

$rules = [
    'users' => new EachX(['id' => new Uuid(4)]),
];

$validator->validate($data, $rules);
```

# Validation

[![Latest Stable Version](https://poser.pugx.org/norvica/validation/v/stable.png)](https://packagist.org/packages/norvica/validation)
[![Checks](https://github.com/norvica/validation/actions/workflows/checks.yml/badge.svg)](https://github.com/norvica/validation/actions/workflows/checks.yml)

This PHP validation library aims to provide a powerful yet streamlined solution for validating your data. It offers a
core set of commonly used validation rules, along with the essential tools to easily define your own custom rules. The
focus is on simplicity, organization, and flexibility.

> [!TIP]
> Use the validator for DTOs (Data Transfer Objects) and structured data. Avoid using it for validating complex objects
> like domain entities, which should enforce their validity through internal logic.

## Table Of Contents

- [Install](#install)
- [Instantiate Validator](#instantiate-validator)
- [Validate Single Value](#validate-single-value)
- [Validate Arrays](#validate-arrays)
- [Validate Objects](#validate-objects)
- [Validate Collections](#validate-collections)
- [Logical Combinations With `AndX`, `OrX`](#logical-combinations-with-andx-and-orx)
- [Optional Values](#optional-values)
- [Validation Results and Errors](#validation-results-and-errors)
- [Strict Mode](#strict-mode)
- [Accessing Normalized Data](#accessing-normalized-data)
- [Configuring Validation Behavior with Options](#configuring-validation-behavior-with-options)
- [Creating Your Own Rules](#creating-your-own-rules)
- [Validator Registry](#validator-registry)
- [Built-in Rules](#built-in-rules)

## Install

This library is installed using Composer. If you don't have Composer, you can get it from https://getcomposer.org/.

In your project's root directory, run the following command:

```bash
composer require norvica/validation
```

## Instantiate Validator

To start using the library, you'll first create an instance of the Validator class:

```php
use Norvica\Validation\Validator;

$validator = new Validator();
```

## Validate Single Value

To validate a single value, use the `Validator::validate()`. The method takes two arguments:

1. The value to validate: This is a data you want to check.
2. A rule object: This object represents the validation rule you want to apply. The library provides various built-in
   rules, and you can also create custom rules.

```php
use Norvica\Validation\Rule\Email;

$validator->validate('john.doe@example.com', new Email());
```

## Validate Arrays

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

## Validate Objects

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

## Validate Collections

The library provides seamless ways to validate collections. Here are the common approaches:

1. Validating Arrays of Values with `EachX`

   Use the `EachX` instruction to apply the same validation rule to every element within an array.

   Example:

    ```php
    use Norvica\Validation\Instruction\EachX;
    use Norvica\Validation\Rule\Ip;
    
    $validator->validate(['127.0.0.1', '0.0.0.0'], new EachX(new Ip())); 
    
    ```

2. Validating Nested Arrays or Objects

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

## Logical Combinations with `AndX` and `OrX`

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

The library provides flexibility when dealing with data that may contain optional parameters. Here are the two main
approaches.

### Wrapping Rules With `OptionalX`

Use the `OptionalX` instruction to mark a rule as optional. This prevents validation errors if the corresponding property
is not present in your data or if its value is `null`.

```php
use Norvica\Validation\Instruction\OptionalX;
use Norvica\Validation\Rule\Url;

$data = [];  // or `['website' => null]`

// without `OptionalX`: would throw an exception if 'website' is missing or `null`
$validator->validate(value: $data, rules: ['website' => new Url()]);

// with `OptionalX`: validation of 'website' is skipped if missing or `null` 
$validator->validate(value: $data, rules: ['website' => new OptionalX(new Url())]);
```

### Dynamic Rule Composition

For more complex scenarios, create rule sets dynamically based on the existence of data. This allows fine-grained
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

## Validation Results and Errors

The  `validate()` method offers flexibility in how validation errors are handled. Here's how to work with the different
modes.

### Throwing Exceptions (Default Behavior)

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

### Aggregating Violations

To collect all validation violations instead of stopping at the first one, pass an `Options` instance to the `validate()`
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

## Strict Mode

### Default Behavior

By default, the validator operates in "strict" mode. This means that the library expects you to define explicit
validation rules for **_all_** properties within an object or keys within an array that you pass for validation. If a
property/key lacks a corresponding rule, the library will throw an exception.

### Purpose

Strict mode helps enforce data integrity and can prevent unexpected behavior. By requiring explicit validation, it
encourages developers to think carefully about the expected format and constraints of the data they are handling.

**Example (Strict Mode)**

```php
$data = ['email' => 'john.doe@example.com'];
$rules = [];

$validator->validate($data, $rules); // will throw a `LogicException` with message "email: Validation rule is not configured."
```

### Disabling Strict Mode

If needed, you can disable strict mode by passing the `strict: false` flag to the `validate()` method. This allows you
to validate only the data for which you've explicitly provided rules, while ignoring other properties or array elements.

**Example (Non-Strict Mode)**

```php
use Norvica\Validation\Options;

$data = ['email' => 'john.doe@example.com'];
$rules = [];

$validator->validate($data, $rules, new Options(strict: false)); // will pass
```

> [!IMPORTANT]  
> Use non-strict mode with caution. Always consider the data integrity requirements of your application.

## Accessing Normalized Data

You can access a normalized version of the original data from the `$result->normalized` property.

**Example**: 

```php
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Flag;

readonly class SubscriptionDto
{
    public function __construct(
        #[Email]
        public string $email,
        #[Flag(value: true)]
        public bool $consent,
    ) {}
}

$dto = new SubscriptionDto(' john.doe@EXAMPLE.com ', ' yes ');

$result = $validator->validate($dto);
$result->normalized; // will contain: ['email' => 'john.doe@example.com', 'consent' => true]
```

## Configuring Validation Behavior with Options

The [**Options**](./src/Options.php) class enables control over how your validator processes data and handles errors.

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
$validator = new Validator($options);

// Perform validation using the (non-throwing) behavior.
$result = $validator->validate($data, $rules);

// Override the default behavior for a single validation run. This will 
// throw an exception on the first violation encountered.
$validator->validate($data, $rules, new Options(throw: true));
```

## Creating Your Own Rules

The library provides flexibility by allowing you to extend its functionality with custom validation rules. Here's the
process for defining your own rules:

1. **Create a Rule Class**

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
        ) {
        }
    
        public static function validator(): string
        {
            return HexColorValidation::class;
        }
    }
    ```

2. **Create a Validator Class**

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
   
   If your validator requires some **dependencies**, please refer to the [Validator Registry](#validator-registry) 
   documentation section.

3. **Use Your Custom Rule**

   Instantiate your custom rule object and include it in your validation rules array, just like any built-in rule:

   Example:

    ```php
    $data = ['color' => '#5e759cff'];
    $rules = ['color' => new HexColor(transparency: true)];
    
    $validator->validate($data, $rules);
    ```

4. **Use Built-in or Custom Normalizers (Optional)**

   The library allows you to apply normalizers to your data before validation, providing a way to preprocess and clean
   up values. To use normalizers with your custom rule:

    - Implement the `Normalizable` interface: Add the `Normalizable` interface to your rule class.
    - Implement the `normalizers()` method: This method should return an array of normalizer objects provided by the
      library or your own custom normalizers.

   Example:

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

   You can use one of the [built-in normalizers](./src/Normalizer) (which are simple callables) or create your own.

    - `Binary`: Converts values to boolean `true` or `false`. Recognizes various representations of "on" (e.g., "On", 
      "Yes", "1") and "off" (e.g., "Off", "No", "0").
    - `Lower`: Converts all characters within a string to lowercase.
    - `Upper`: Converts all characters within a string to uppercase.
    - `Numeric`: Converts numeric string to a number.
    - `Spaceless`: Removes all space characters from a string.
    - `Trim`: Removes leading and trailing whitespace from a string.

> [!NOTE]
> Normalizers modify the value **before** it reaches your validator. This can be useful for tasks like trimming
> whitespace, converting to lowercase, or other transformations.

> [!NOTE]
> Normalizers **do not** change the original data; they provide a normalized copy for the validation process.

## Validator Registry

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

**Your validation**:

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

$validator = new Validator([UniqueConstraintValidation::class => $validation]);
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
$validator = new Validator($yourRegistry);
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
$validator = new Validator($yourAdapter);
```

## Built-in Rules

[**DateTime**](./src/Rule/DateTime.php)

- **Purpose**: Validates whether a value represents a valid date and/or time according to a specified format.
- **Options**:
    - `min` (`DateTimeImmutable`, optional): Sets a minimum allowed date/time.
    - `max` (`DateTimeImmutable`, optional): Sets a maximum allowed date/time.
    - `format` (string): Specifies the expected date/time format. See [PHP documentation](https://www.php.net/manual/en/datetime.format.php).
- **Examples**:
  ```php
  use Norvica\Validation\Rule\DateTime;

  // ISO8601 format (e.g. "2014-04-06T15:05:45+00:00")
  $rule = new DateTime(format: DateTime::ISO8601);

  // ISO8601 for the date portion only (e.g. "2014-04-06")
  $rule = new DateTime(format: DateTime::ISO8601_DATE);

  // ISO8601 for the time portion only (e.g. "15:05:45")
  $rule = new DateTime(format: DateTime::ISO8601_TIME);

  // ISO8601 with milliseconds (e.g. "2014-04-06T15:05:45.844+00:00")
  $rule = new DateTime(format: DateTime::ISO8601_WITH_MILLISECONDS);

  // ISO8601 with microseconds (e.g. "2014-04-06T15:05:45.844188+00:00")
  $rule = new DateTime(format: DateTime::ISO8601_WITH_MICROSECONDS);

  // dates before 18 years ago (age validation)
  $rule = new DateTime(
      max: new DateTimeImmutable('-18 years'),
      format: DateTime::ISO8601_DATE,
  );

  // dates after Unix epoch start
  $rule = new DateTime(
      min: new DateTimeImmutable('1970-01-01'),
      format: DateTime::ISO8601_DATE,
  );

  // custom format, hours and minutes (e.g. "15:45")
  $rule = new DateTime(format: 'H:i');
  ```

[**Email**](./src/Rule/Email.php)

- **Purpose**: Validates whether a value is a well-formatted email address.
- **Options**:
    - `dns` (boolean, optional): If set to true, the rule performs additional DNS checks to verify that the email
      domain exists and has valid MX records. (Defaults to false).
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Email;

  // no DNS checks
  $rule = new Email();
  
  // with DNS checks
  $rule = new Email(dns: true);
  ```

[**Flag**](./src/Rule/Flag.php)

- **Purpose**: Enforces that a value is either `true` or `false` (a boolean flag)
  and [normalizes](#accessing-normalized-data) it if necessary.
- **Options**:
    - `value` (boolean, default `null`): Enforces a specific value for the flag (true or false). Defaults to allowing
      both `true` and `false`.
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Flag;
  
  // normalized result will contain the boolean value (`true` or `false`)
  $rule = new Flag();

  // ensure value is `false`
  $rule = new Flag(value: false);
  
  // ensure value is `true`
  $rule = new Flag(value: true);
  ```

[**Hostname**](./src/Rule/Hostname.php)

- **Purpose**: Validates whether a value represents a valid hostname.
- **Options**:
    - `hosts` (array of strings, optional): If provided, restricts the validation to only allow URLs with specified
      hostnames.
    - `dns` (boolean, default: `false`): If set to `true`, performs DNS record checks.
    - `reserved` (boolean, default: false): If set to `true`, allows using reserved TLDs (e.g., 'localhost', 
      'example', 'test', and 'invalid').
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Hostname;

  // check for a valid hostname
  $rule = new Hostname();
  
  // allow only specific host 'example.com'
  $rule = new Hostname(hosts: ['example.com']);
  
  // allow only specific host 'example.com' and its subdomains
  $rule = new Hostname(hosts: ['*.example.com']);

  // perform PHPs built-in `checkdnsrr` DNS checks
  $rule = new Hostname(dns: true);
  ```

[**Iban**](./src/Rule/Iban.php)

- **Purpose**: Validates whether a value conforms to the International Bank Account Number (IBAN) format.
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Iban;

  $rule = new Iban();
  ```

[**Ip**](./src/Rule/Ip.php)

- **Purpose**: Validates whether a value represents a valid IP address (either IPv4 or IPv6).
- **Options**:
    - `version` (int, optional): Specifies the desired IP version to validate.
        - If `4`, validates only IPv4 addresses.
        - If `6`, validates only IPv6 addresses.
        - If `null` (default), validates both IPv4 and IPv6 addresses.
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Ip;
  
  // allow both IPv4 and IPv6
  $rule = new Ip();
  
  // allow only IPv4
  $rule = new Ip(4);
  ```

[**Number**](./src/Rule/Number.php)

- **Purpose**: Validates whether a value is a number within a specified range.
- **Options**:
    - `min` (int, float, optional): The minimum allowed value. Defaults to `null` (no minimum).
    - `max` (int, float, optional): The maximum allowed value. Defaults to `null` (no maximum).
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Number;
  
  // allow a number between 10 and 20 (inclusive)
  $rule = new Number(min: 10, max: 20);
      
  // allow a number greater than or equal to 10
  $rule = new Number(min: 10);
      
  // allow a number less than or equal to 20
  $rule = new Number(max: 20);
  ```

[**Option**](./src/Rule/Option.php)

- **Purpose**: Validates whether a value (or values) exists within a predefined set of allowed options.
- **Options**:
    - `options` (array of strings): Specifies the list of valid options.
    - `multiple` (boolean):
        - If `true`, allows the value to be an array containing multiple valid options.
        - If `false`, the value must be a single element from the options array.
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Option;
  
  // allow single option
  $rule = new Option(options: ['red', 'green', 'blue'], multiple: false);
  
  // allow multiple options (value must be an array containing elements from the options)
  $rule = new Option(options: ['red', 'green', 'blue'], multiple: true);
  ```

[**Password**](./src/Rule/Password.php)

- **Purpose**: Enforces password complexity requirements, ensuring that user passwords meet a certain level of security.
- **Options**:
    - `min` (int, default: 8): Specifies the minimum required password length.
    - `upper` (bool, default: `true`): Requires at least one uppercase letter.
    - `lower` (bool, default: `true`): Requires at least one lowercase letter.
    - `number` (bool, default: `true`): Requires at least one numeric character.
    - `special` (bool, default: `true`): Requires at least one special character.
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Password;
  
  // stricter password requirements
  $rule = new Password(min: 12, upper: true, lower: true, number: true, special: true);
  
  // more relaxed requirements
  $rule = new Password(upper: false, lower: false);
  ```

[**Slug**](./src/Rule/Slug.php)

- **Purpose**: Specifically validates strings intended to be used as URL-friendly slugs (e.g., in blog post titles or
  product identifiers).
- **Inheritance**: The Slug rule inherits from the [**Text**](./src/Rule/Text.php) rule, re-using text validator.
- **Default Behavior**: By default, enforces these constraints:
    - **Minimum length**: 2 characters
    - **Maximum length**: 64 characters
    - **Allowed characters**: Lowercase letters, numbers, hyphens (`-`), and underscores (`_`).
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Slug;
  
  // a standard slug
  $rule = new Slug();
      
  // enforce a stricter slug format (only lowercase letters and hyphens)
  $rule = new Slug(regExp: '/^[a-z-]+$/');
  ```

[**Text**](./src/Rule/Text.php)

- **Purpose**: Validates textual input, providing flexible constraints based on length and regular expression patterns.
- **Options**:
    - `minLength` (int, optional): Specifies the minimum allowed length of the text. Defaults to `null` (no minimum).
    - `maxLength` (int, optional): Specifies the maximum allowed length of the text. Defaults to `null` (no maximum).
    - `regExp` (string, optional): Provides a regular expression pattern that the text must match. Defaults
      to `null` (no pattern-based validation).
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Text;
  
  // allow text between 5 and 20 characters
  $rule = new Text(minLength: 5, maxLength: 20);
      
  // allow only numbers
  $rule = new Text(regExp: '/^\d+$/');
      
  // allow a username format (letters, numbers, underscores, 6-12 characters)
  $rule = new Text(minLength: 6, maxLength: 12, regExp: '/^[a-zA-Z0-9_]+$/');
  ```

[**Url**](./src/Rule/Url.php)

- **Purpose**: Validates whether a value represents a well-formed URL (Uniform Resource Locator).
- **Options**:
    - `schemes` (array of strings, default: `['http', 'https']`): Specifies a list of allowed URL schemes (e.g., 'http', 
      'https', 'ftp', etc.).
    - `hosts` (array of strings, optional): If provided, restricts the validation to only allow URLs with specified
      hostnames.
    - `dns` (boolean, default: `false`): If set to `true`, performs DNS record checks.
    - `reserved` (boolean, default: false): If set to `true`, allows using reserved TLDs (e.g., 'localhost', 
      'example', 'test', and 'invalid').
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Url;

  // allow standard 'http' or 'https' URLs
  $rule = new Url();

  // allow only 'https' URLs for a specific host 'example.com'
  $rule = new Url(schemes: ['https'], hosts: ['example.com']);
  
  // allow only 'https' URLs for a specific host 'example.com' and its subdomains
  $rule = new Url(schemes: ['https'], hosts: ['*.example.com']);

  // perform PHPs built-in `checkdnsrr` DNS checks
  $rule = new Url(dns: true);
  ```

[**Uuid**](./src/Rule/Uuid.php)

- **Purpose**: Validates whether a value conforms to the Universally Unique Identifier (UUID) format, optionally
  specifying a particular UUID version.
- **Options**:
    - `version` (int, optional): Specifies the required UUID version. Valid options include:
        - `1`: Validates Version 1 UUIDs
        - `3`: Validates Version 3 UUIDs
        - `4`: Validates Version 4 UUIDs
        - `5`: Validates Version 5 UUIDs
        - `7`: Validates Version 7 UUIDs
        - If `null` (default), validates any valid UUID version.
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Uuid;

  // allow any valid UUID versions
  $rule = new Uuid();
  
  // allow only Version 4 UUIDs
  $rule = new Uuid(4);
  ```

## Alternative Validation Libraries

Here are some other PHP validation libraries that you might consider depending on your project's requirements:

- [Symfony Validation](https://symfony.com/doc/current/validation.html)
- [Respect/Validation](https://github.com/Respect/Validation)

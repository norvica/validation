---
title: "Rules"
description: ""
summary: ""
date: 2024-04-21T18:34:17+02:00
lastmod: 2024-04-21T18:34:17+02:00
draft: false
weight: 130
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

While the library makes it easy to create your own custom validation rules, it also provides a collection of built-in
rules for common use cases.

## DateTime

Validates whether a value represents a valid date and/or time according to a specified format.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/DateTime.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/DateTimeValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Email

Validates whether a value is a well-formatted email address.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Email.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/EmailValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Flag

Enforces that a value is either `true` or `false` (a boolean flag) and [normalizes](#accessing-normalized-data) it if necessary.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Flag.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/FlagValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Hostname

Validates whether a value represents a valid hostname.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Hostname.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/HostnameValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Iban

Validates whether a value conforms to the International Bank Account Number (IBAN) format.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Iban.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/IbanValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- **Examples**:
  ```php
  use Norvica\Validation\Rule\Iban;

  $rule = new Iban();
  ```

## Ip

Validates whether a value represents a valid IP address (either IPv4 or IPv6).

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Ip.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/IpValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Number

Validates whether a value is a number within a specified range.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Number.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/NumberValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Option

Validates whether a value (or values) exists within a predefined set of allowed options.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Option.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/OptionValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Password

Enforces password complexity requirements, ensuring that user passwords meet a certain level of security.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Password.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/PasswordValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Slug

Specifically validates strings intended to be used as URL-friendly slugs (e.g., in blog post titles or
  product identifiers).

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Slug.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/TextValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Text

Validates textual input, providing flexible constraints based on length and regular expression patterns.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Text.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/TextValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Url

Validates whether a value represents a well-formed URL (Uniform Resource Locator).

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Url.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/UrlValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

## Uuid

Validates whether a value conforms to the Universally Unique Identifier (UUID) format, optionally
specifying a particular UUID version.

- <a href="https://github.com/norvica/validation/blob/main/src/Rule/Uuid.php" target="_blank">Rule {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
- <a href="https://github.com/norvica/validation/blob/main/src/Validation/UuidValidation.php" target="_blank">Validator {{< inline-svg src="external-link" class="svg-inline-custom" >}}</a>
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

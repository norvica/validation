---
title: "Normalization"
description: ""
summary: ""
date: 2024-04-21T17:45:42+02:00
lastmod: 2024-04-21T17:45:42+02:00
draft: false
weight: 160
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

To ensure data cleanliness before validation, the library applies a set of normalizers (configured in the `normalizers()`
method of each rule).

{{< callout context="note" icon="info-circle" >}}
Normalizers **do not** change the original data; they provide a normalized copy for the validation process.
{{< /callout >}}

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

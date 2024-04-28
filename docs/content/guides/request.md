---
title: "HTTP Request"
description: "Validating HTTP request payload using validation library."
summary: ""
date: 2024-04-21T17:45:42+02:00
lastmod: 2024-04-21T17:45:42+02:00
draft: false
weight: 810
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

This small guide demonstrates how to integrate the validation library with your HTTP API built using a framework that
implements PSR-7 request and response interfaces.

Scenario:

- We'll assume you're using a framework (not mandatory) that provides mechanisms to retrieve request data. Adapt the
  code based on your framework's specifics.
- This example focuses on JSON payloads, but the core concepts apply to other data formats as well.

```php
use Norvica\Validation\Exception\PropertyRuleViolation;
use Norvica\Validation\Rule\Url;
use Norvica\Validation\Validator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ExampleController
{
    public function post(RequestInterface $request): ResponseInterface
    {
        // Extract payload (assuming JSON)
        $payload = json_decode($request->getBody()->getContents(), true);

        // Define validation rules
        $rules = [
            'website' => new Url(),
            // ... more rules
        ];

        // Instantiate validator (or inject as a dependency)
        $validator = new Validator();

        try {
            $result = $validator->validate($payload, $rules);
            $data = $result->normalized;

            // Access normalized data
            $email = $data['email'];

            // Validation successful
            // ... your business logic here ...
        } catch (PropertyRuleViolation $e) {
            // Handle validation error
            return new Response(
                status: 400,
                headers: ['Content-Type' => 'application/json'],
                body: json_encode(['errors' => [$e->getPath() => $e->getText()]]),
            );
        }

        return new Response(
            status: 201,
            headers: ['Content-Type' => 'application/json'],
            body: '{}',
        );
    }
}
```

Here's an example of how an error response body might look when a validation error occurs, allowing your client app to
highlight specific fields:

```json
{
  "errors": {
    "website": "Value must be a valid URL"
  }
}
```

<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Email;
use Norvica\Validation\Exception\ConstraintViolation;

final class EmailValidation
{
    public function __invoke(string $value, Email $constraint): void
    {
        $message = 'Value must be a valid E-mail address';

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ConstraintViolation($message);
        }

        [$local, $domain] = explode('@', $value, 2);

        if (!preg_match('/^[a-zA-Z0-9.!$%&\'*+\/=?^_`{|}~-]{1,64}$/', $local)) {
            throw new ConstraintViolation($message);
        }

        // DNS check (A/MX records)
        if ($constraint->dns && !checkdnsrr($domain) && !checkdnsrr($domain, 'A')) {
            throw new ConstraintViolation($message);
        }
    }
}

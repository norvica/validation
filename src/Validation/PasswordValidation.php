<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Password;
use Norvica\Validation\Exception\ConstraintViolation;

final class PasswordValidation
{
    public function __invoke(string $value, Password $constraint): void
    {
        $length = strlen($value);

        if ($length > 128) {
            throw new ConstraintViolation("Password must not be longer than 128 characters");
        }

        if ($length < $constraint->min) {
            throw new ConstraintViolation("Password must be at least {$constraint->min} characters long");
        }

        $hasUpper = preg_match('#[A-Z]#', $value);
        $hasLower = preg_match('#[a-z]#', $value);
        $hasNumber = preg_match('#\d#', $value);
        $hasSpecial = preg_match('#[^A-Za-z0-9]#', $value);

        if ($constraint->upper && !$hasUpper) {
            throw new ConstraintViolation("Password must contain at least 1 upper case character");
        }

        if ($constraint->lower && !$hasLower) {
            throw new ConstraintViolation("Password must contain at least 1 lower case character");
        }

        if ($constraint->number && !$hasNumber) {
            throw new ConstraintViolation("Password must contain at least 1 number");
        }

        if ($constraint->special && !$hasSpecial) {
            throw new ConstraintViolation("Password must contain at least 1 special character");
        }
    }
}

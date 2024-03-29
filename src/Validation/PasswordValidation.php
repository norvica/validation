<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Password;
use Norvica\Validation\Exception\ValueRuleViolation;

final class PasswordValidation
{
    public function __invoke(string $value, Password $rule): void
    {
        $length = strlen($value);

        if ($length > 128) {
            throw new ValueRuleViolation("Password must not be longer than 128 characters");
        }

        if ($length < $rule->min) {
            throw new ValueRuleViolation("Password must be at least {$rule->min} characters long");
        }

        $hasUpper = preg_match('#[A-Z]#', $value);
        $hasLower = preg_match('#[a-z]#', $value);
        $hasNumber = preg_match('#\d#', $value);
        $hasSpecial = preg_match('#[^A-Za-z0-9]#', $value);

        if ($rule->upper && !$hasUpper) {
            throw new ValueRuleViolation("Password must contain at least 1 upper case character");
        }

        if ($rule->lower && !$hasLower) {
            throw new ValueRuleViolation("Password must contain at least 1 lower case character");
        }

        if ($rule->number && !$hasNumber) {
            throw new ValueRuleViolation("Password must contain at least 1 number");
        }

        if ($rule->special && !$hasSpecial) {
            throw new ValueRuleViolation("Password must contain at least 1 special character");
        }
    }
}

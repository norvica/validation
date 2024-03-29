<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Number;
use Norvica\Validation\Exception\ValueRuleViolation;

final class NumberValidation
{
    public function __invoke(int|float $value, Number $constraint): void
    {
        if ($constraint->min !== null && $constraint->min > $value) {
            throw new ValueRuleViolation("Value must be higher than {$constraint->min}");
        }

        if ($constraint->max !== null && $constraint->max < $value) {
            throw new ValueRuleViolation("Value must be lower than {$constraint->min}");
        }
    }
}

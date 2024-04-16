<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Number;
use Norvica\Validation\Exception\ValueRuleViolation;

final class NumberValidation
{
    public function __invoke(int|float $value, Number $rule): void
    {
        if ($rule->min !== null && $rule->min > $value) {
            throw new ValueRuleViolation("Value must be higher than {$rule->min}");
        }

        if ($rule->max !== null && $rule->max < $value) {
            throw new ValueRuleViolation("Value must be lower than {$rule->max}");
        }
    }
}

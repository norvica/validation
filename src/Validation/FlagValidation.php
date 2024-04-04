<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Flag;
use Norvica\Validation\Exception\ValueRuleViolation;

final class FlagValidation
{
    public function __invoke(bool $value, Flag $rule): void
    {
        // both `true` and `false` are allowed
        if ($rule->value === null) {
            return;
        }

        if ($value !== $rule->value) {
            $parameter = $rule->value ? 'true' : 'false';

            throw new ValueRuleViolation("Value must be {$parameter}");
        }
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Flag;
use Norvica\Validation\Exception\ValueRuleViolation;

final class FlagValidation
{
    public function __invoke(bool $value, Flag $constraint): void
    {
        if ($value !== $constraint->value) {
            $parameter = $constraint->value ? 'true' : 'false';

            throw new ValueRuleViolation("Value must be {$parameter}");
        }
    }
}

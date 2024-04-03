<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Text;
use Norvica\Validation\Exception\ValueRuleViolation;

final class TextValidation
{
    public function __invoke(string $value, Text $rule): void
    {
        $length = strlen($value);

        if ($rule->minLength !== null && $rule->minLength > $length) {
            throw new ValueRuleViolation("Value must be at least {$rule->minLength} characters long");
        }

        if ($rule->maxLength !== null && $rule->maxLength < $length) {
            throw new ValueRuleViolation("Value must be no more than {$rule->maxLength} characters long");
        }

        if (null !== $rule->regExp && !preg_match($rule->regExp, $value)) {
            throw new ValueRuleViolation("Value doesn't match the required format");
        }
    }
}

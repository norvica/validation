<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use DateTimeImmutable;
use Norvica\Validation\Exception\ValueRuleViolation;
use Norvica\Validation\Rule\DateTime;

final class DateTimeValidation
{
    public function __invoke(DateTimeImmutable $value, DateTime $rule): void
    {
        $format = $rule->format;

        if ($rule->min !== null && $rule->min > $value) {
            throw new ValueRuleViolation("Value must be after {$rule->min->format($format)}");
        }

        if ($rule->max !== null && $rule->max < $value) {
            throw new ValueRuleViolation("Value must be before {$rule->max->format($format)}");
        }
    }
}

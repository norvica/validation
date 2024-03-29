<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Option;
use Norvica\Validation\Exception\ValueRuleViolation;

final class OptionValidation
{
    public function __invoke(array|string|int|float $value, Option $rule): void
    {
        if (is_array($value)) {
            if (!$rule->multiple) {
                throw new ValueRuleViolation('Multiple options are not allowed');
            }

            if (!array_is_list($value)) {
                throw new ValueRuleViolation('Options must be a numerically indexed array');
            }

            $diff = array_diff($value, $rule->options);
            if (count($diff) > 0) {
                throw new ValueRuleViolation(
                    sprintf('Values must match allowed options: %s', implode('", "', $rule->options))
                );
            }

            return;
        }

        if (!in_array($value, $rule->options, true)) {
            throw new ValueRuleViolation(
                sprintf('Value must match one of the allowed options: %s', implode('", "', $rule->options))
            );
        }
    }
}

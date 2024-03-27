<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Flag;
use Norvica\Validation\Exception\ConstraintViolation;

final class FlagValidation
{
    public function __invoke(bool $value, Flag $constraint): void
    {
        if ($value !== $constraint->value) {
            $parameter = $constraint->value ? 'true' : 'false';

            throw new ConstraintViolation("Value must be {$parameter}");
        }
    }
}

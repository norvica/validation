<?php

declare(strict_types=1);

namespace Norvica\Validation;

use Norvica\Validation\Constraint\Validatable;
use Norvica\Validation\Exception\ConstraintViolation;
use Norvica\Validation\Normalizing\Normalizable;
use TypeError;

final class Validator
{
    /**
     * @throws ConstraintViolation
     */
    public function check(mixed $value, Validatable|Normalizable $constraint): void
    {
        if ($constraint instanceof Normalizable) {
            foreach ($constraint->normalizers() as $sanitize) {
                $value = $sanitize($value);
            }
        }

        if (!$constraint instanceof Validatable) {
            throw new TypeError(
                sprintf("Constraint '%s' must implement '%s' interface.", get_debug_type($constraint), Validatable::class)
            );
        }

        $class = $constraint->validator();
        $validator = new $class(); // TODO: allow PSR container integration
        $validator($value, $constraint);
    }
}

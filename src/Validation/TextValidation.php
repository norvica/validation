<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Text;
use Norvica\Validation\Exception\ConstraintViolation;

final class TextValidation
{
    public function __invoke(string $value, Text $constraint): void
    {
        $length = strlen($value);

        if ($constraint->minLength !== null && $constraint->minLength > $length) {
            throw new ConstraintViolation("Value must be at least {$constraint->minLength} characters long");
        }

        if ($constraint->maxLength !== null && $constraint->maxLength < $length) {
            throw new ConstraintViolation("Value must be no more than  {$constraint->maxLength} characters long");
        }

        if (null !== $constraint->regExp && !preg_match($constraint->regExp, $value)) {
            throw new ConstraintViolation("Value doesn't match the required format");
        }
    }
}

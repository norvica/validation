<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Uuid;
use Norvica\Validation\Exception\ConstraintViolation;

final class UuidValidation
{
    public function __invoke(string $uuid, Uuid $constraint): void
    {
        if ($constraint->version === null) {
            if (!preg_match('#^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$#i', $uuid)) {
                throw new ConstraintViolation('Value must be a valid UUID');
            }

            return;
        }

        $pattern = '#^[0-9A-F]{8}-[0-9A-F]{4}-' . $constraint->version . '[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$#i';
        if (!preg_match($pattern, $uuid)) {
            throw new ConstraintViolation("Value must be a valid UUID (version {$constraint->version})");
        }
    }
}

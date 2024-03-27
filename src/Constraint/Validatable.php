<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

interface Validatable
{
    /**
     * @return callable-string
     */
    public static function validator(): string;
}

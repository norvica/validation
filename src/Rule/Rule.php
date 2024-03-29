<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

interface Rule
{
    /**
     * @return callable-string
     */
    public static function validator(): string;
}

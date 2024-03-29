<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Slug extends Text
{
    public function __construct(
        int|null $minLength = 2,
        int|null $maxLength = 64,
        string|null $regExp = '#^[a-z0-9-_]+$#i',
    ) {
        parent::__construct($minLength, $maxLength, $regExp);
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Numeric;
use Norvica\Validation\Validation\NumberValidation;
use Override;

readonly class Number implements Validatable, Normalizable
{
    public function __construct(
        public int|float|null $min = null,
        public int|float|null $max = null,
    ) {
    }

    #[Override]
    public static function normalizers(): array
    {
        return [
            new Numeric(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return NumberValidation::class;
    }
}

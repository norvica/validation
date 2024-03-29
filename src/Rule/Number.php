<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Numeric;
use Norvica\Validation\Validation\NumberValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Number implements Rule, Normalizable
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

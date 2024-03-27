<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Binary;
use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Validation\FlagValidation;
use Override;

readonly class Flag implements Validatable, Normalizable
{
    public function __construct(
        public bool $value,
    ) {
    }

    #[Override]
    public static function normalizers(): array
    {
        return [
            new Binary(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return FlagValidation::class;
    }
}

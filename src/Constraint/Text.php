<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Trim;
use Norvica\Validation\Validation\TextValidation;
use Override;

readonly class Text implements Validatable, Normalizable
{
    public function __construct(
        public int|null $minLength = null,
        public int|null $maxLength = null,
        public string|null $regExp = null,
    ) {
    }

    #[Override]
    public static function normalizers(): array
    {
        return [
            new Trim(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return TextValidation::class;
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\TextValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Text implements Rule, Normalizable
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

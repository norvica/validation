<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Binary;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Validation\FlagValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Flag implements Rule, Normalizable
{
    public function __construct(
        public bool|null $value = null,
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

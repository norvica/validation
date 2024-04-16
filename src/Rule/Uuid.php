<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Lower;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\UuidValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Uuid implements Rule, Normalizable
{
    public function __construct(
        public int|null $version = null,
    ) {
    }

    #[Override]
    public function normalizers(): array
    {
        return [
            new Trim(),
            new Lower(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return UuidValidation::class;
    }
}

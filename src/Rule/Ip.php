<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\IpValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Ip implements Rule, Normalizable
{
    public function __construct(
        public int|null $version = null,
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
        return IpValidation::class;
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Trim;
use Norvica\Validation\Validation\IpValidation;
use Override;

readonly class Ip implements Validatable, Normalizable
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

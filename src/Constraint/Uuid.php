<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Lower;
use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Trim;
use Norvica\Validation\Validation\UuidValidation;
use Override;

readonly class Uuid implements Validatable, Normalizable
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
            new Lower(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return UuidValidation::class;
    }
}

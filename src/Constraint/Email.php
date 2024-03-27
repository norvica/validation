<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Lower;
use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Trim;
use Norvica\Validation\Validation\EmailValidation;
use Override;

readonly class Email implements Validatable, Normalizable
{
    /**
     * @param bool $dns Perform DNS checks (default `false`).
     */
    public function __construct(
        public bool $dns = false,
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
        return EmailValidation::class;
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Lower;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\EmailValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Email implements Rule, Normalizable
{
    /**
     * @param bool $dns Perform DNS checks (default `false`).
     */
    public function __construct(
        public bool $dns = false,
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
        return EmailValidation::class;
    }
}

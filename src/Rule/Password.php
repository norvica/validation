<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Validation\PasswordValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Password implements Rule, Normalizable
{
    /**
     * @param int $min Minimal password length (default `8`).
     * @param bool $upper Require at least one upper case character (default `true`).
     * @param bool $lower Require at least one lower case character (default `true`).
     * @param bool $number Require at least one numeric character (default `true`).
     * @param bool $special Require at least one special character (default `true`).
     */
    public function __construct(
        public int $min = 8,
        public bool $upper = true,
        public bool $lower = true,
        public bool $number = true,
        public bool $special = true,
    ) {
    }

    #[Override]
    final public function normalizers(): array
    {
        return []; // password string should stay as is, no normalizers
    }

    #[Override]
    public static function validator(): string
    {
        return PasswordValidation::class;
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\OptionValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Option implements Rule, Normalizable
{
    /**
     * @param string[] $options
     * @param bool $multiple Allow multiple options.
     */
    public function __construct(
        public array $options,
        public bool $multiple,
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
        return OptionValidation::class;
    }
}

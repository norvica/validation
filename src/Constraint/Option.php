<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Trim;
use Norvica\Validation\Validation\OptionValidation;
use Override;

readonly class Option implements Validatable, Normalizable
{
    /**
     * @param string[] $options
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

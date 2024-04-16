<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Spaceless;
use Norvica\Validation\Normalizer\Upper;
use Norvica\Validation\Validation\IbanValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Iban implements Rule, Normalizable
{
    #[Override]
    public function normalizers(): array
    {
        return [
            new Spaceless(),
            new Upper(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return IbanValidation::class;
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Constraint;

use Norvica\Validation\Normalizing\Normalizable;
use Norvica\Validation\Normalizing\Spaceless;
use Norvica\Validation\Normalizing\Upper;
use Norvica\Validation\Validation\IbanValidation;
use Override;

readonly class Iban implements Validatable, Normalizable
{
    #[Override]
    public static function normalizers(): array
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

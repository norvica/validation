<?php

declare(strict_types=1);

namespace Norvica\Validation\Instruction;

use Attribute;
use Norvica\Validation\Rule\Rule;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class OptionalX
{
    /**
     * @param Rule|AndX|OrX|Rule[] $rules
     */
    public function __construct(
        public Rule|AndX|OrX|array $rules,
    ) {
    }
}

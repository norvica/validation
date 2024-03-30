<?php

declare(strict_types=1);

namespace Norvica\Validation\Instruction;

use Attribute;
use Norvica\Validation\Rule\Rule;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class EachX
{
    /**
     * @param Rule|Rule[] $rules
     */
    public function __construct(
        public Rule|array $rules,
    ) {
    }
}

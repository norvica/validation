<?php

declare(strict_types=1);

namespace Norvica\Validation\Instruction;

use Attribute;
use Norvica\Validation\Rule\Rule;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class OrX
{
    /**
     * @var Rule[]
     */
    public array $rules;

    public function __construct(
        Rule ...$rules,
    ) {
        $this->rules = $rules;
    }
}

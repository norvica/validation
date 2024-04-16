<?php

declare(strict_types=1);

namespace Norvica\Validation;

use Norvica\Validation\Violation\Violation;

final readonly class Result
{
    /**
     * @param Violation[] $violations
     */
    public function __construct(
        public array $violations = [],
        public object|array|string|int|float|bool|null $normalized = null,
    ) {
    }
}

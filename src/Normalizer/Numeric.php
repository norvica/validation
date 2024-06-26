<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizer;

final class Numeric
{
    public function __invoke(string|int|float $value): int|float
    {
        return $value + 0;
    }
}

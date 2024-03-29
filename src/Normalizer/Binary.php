<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizer;

final class Binary
{
    public function __invoke(string|int|float|bool|null $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizer;

final class Lower
{
    public function __invoke(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map(strtolower(...), $value);
        }

        return strtolower($value);
    }
}

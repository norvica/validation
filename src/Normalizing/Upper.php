<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizing;

final class Upper
{
    public function __invoke(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map(strtoupper(...), $value);
        }

        return strtoupper($value);
    }
}

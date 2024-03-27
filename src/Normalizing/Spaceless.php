<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizing;

final class Spaceless
{
    public function __invoke(string|array $value): string|array
    {
        $characters = [' ', "\n", "\r", "\t", "\v", "\0"];
        if (is_array($value)) {
            return array_map(static fn($v) => str_replace($characters, '', $v), $value);
        }

        return str_replace($characters, '', $value);
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizer;

final readonly class Trim
{
    public function __construct(
        public string $characters = " \n\r\t\v\0",
    ) {
    }

    public function __invoke(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map(fn(string $v) => trim($v, $this->characters), $value);
        }

        return trim($value, $this->characters);
    }
}

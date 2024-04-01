<?php

declare(strict_types=1);

namespace Norvica\Validation\Registry;

use Norvica\Validation\Exception\LogicException;
use Override;

final readonly class MapRegistry implements Registry
{
    /**
     * @param array<string, callable> $validators
     */
    public function __construct(
        public array $validators,
    ) {
    }

    #[Override]
    public function get(string $validator): callable
    {
        if (!$this->has($validator)) {
            throw new LogicException("Validator '{$validator}' not found.");
        }

        return $this->validators[$validator];
    }

    #[Override]
    public function has(string $validator): bool
    {
        return isset($this->validators[$validator]);
    }
}

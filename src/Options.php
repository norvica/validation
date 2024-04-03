<?php

declare(strict_types=1);

namespace Norvica\Validation;

final readonly class Options
{
    /**
     * @param bool $throw (default: true) Controls whether the validator throws an exception immediately on the first violation (true) or aggregates all violations into the result (false).
     * @param bool $strict (default: true) Determines if an exception is thrown when data properties lack corresponding validation rules.
     */
    public function __construct(
        public bool|null $throw = null,
        public bool|null $strict = null,
    ) {
    }

    public function merge(Options $options): self
    {
        return new self(
            throw: $options->throw ?? $this->throw,
            strict: $options->strict ?? $this->strict,
        );
    }

    public static function default(): self
    {
        return new self(
            throw: true,
            strict: true,
        );
    }
}

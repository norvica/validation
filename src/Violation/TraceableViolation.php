<?php

declare(strict_types=1);

namespace Norvica\Validation\Violation;

interface TraceableViolation
{
    /**
     * Returns the array property path where the validation rule failed (e.g., ["email"] or ["users", 0 "name"]).
     */
    public function getRawPath(): array;

    /**
     * Returns the property path where the validation rule failed (e.g., "email" or "users[0].name").
     */
    public function getPath(): string;

    /**
     * Returns a human-readable message describing the validation failure.
     */
    public function getText(): string;
}

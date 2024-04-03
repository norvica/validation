<?php

declare(strict_types=1);

namespace Norvica\Validation\Violation;

final class Violation implements TraceableViolation
{
    use TraceableViolationTrait;

    public function __construct(array $path, string $message)
    {
        $this->path = $path;
        $this->text = $message;
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Exception;

use DomainException;
use Throwable;

final class PropertyRuleViolation extends DomainException
{
    public readonly array $path;

    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        array $path = [],
    )
    {
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }
}

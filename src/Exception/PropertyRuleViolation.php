<?php

declare(strict_types=1);

namespace Norvica\Validation\Exception;

use DomainException;
use Norvica\Validation\Violation\TraceableViolation;
use Norvica\Validation\Violation\TraceableViolationTrait;
use Throwable;

final class PropertyRuleViolation extends DomainException implements TraceableViolation
{
    use TraceableViolationTrait;

    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        array $path = [],
    ) {
        $this->path = $path;
        $this->text = $message;
        parent::__construct("{$this->getPath()}: {$message}", $code, $previous);
    }
}

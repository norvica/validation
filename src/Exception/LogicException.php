<?php

declare(strict_types=1);

namespace Norvica\Validation\Exception;

use Throwable;

final class LogicException extends \LogicException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        array $path = [],
    ) {
        parent::__construct(
            sprintf('%s: %s', implode('.', $path), $message),
            $code,
            $previous,
        );
    }
}

<?php

declare(strict_types=1);

namespace Norvica\Validation\Exception;

use DomainException;
use Throwable;

final class PropertyRuleViolation extends DomainException
{
    /**
     * @var string[]
     */
    public readonly array $path;
    public readonly string $text;

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

    public function getPath(): string
    {
        $formatted = '';
        foreach ($this->path as $part) {
            $formatted .= is_int($part) ? "[{$part}]" : ".{$part}";
        }

        return ltrim($formatted, '.');
    }

    public function getText(): string
    {
        return $this->text;
    }
}

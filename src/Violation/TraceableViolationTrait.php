<?php

declare(strict_types=1);

namespace Norvica\Validation\Violation;

trait TraceableViolationTrait
{
    /**
     * @var string[]
     */
    protected readonly array $path;
    protected readonly string $text;

    public function getRawPath(): array
    {
        return $this->path;
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

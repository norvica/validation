<?php

declare(strict_types=1);

namespace Norvica\Validation\Registry;

interface Registry
{
    public function get(string $validator): callable;

    public function has(string $validator): bool;
}

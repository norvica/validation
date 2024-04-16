<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizer;

interface Normalizable
{
    /**
     * @return callable[]
     */
    public function normalizers(): array;
}

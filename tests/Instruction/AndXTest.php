<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Instruction;

use Norvica\Validation\Instruction\AndX;
use Norvica\Validation\Rule\Slug;
use Norvica\Validation\Rule\Uuid;
use Tests\Norvica\Validation\Single\ValidationTestCase;

final class AndXTest extends ValidationTestCase
{
    public function testValid(): void
    {
        $this->assertValid(
            'e2575f66-47ea-4152-ba1e-0ed63dec1e4f',
            new AndX(
                new Uuid(4),
                new Slug(),
            ),
        );
    }

    public function testInvalid(): void
    {
        $this->assertInvalid(
            'e2575f66',
            new AndX(
                new Uuid(4),
                new Slug(),
            ),
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Instruction;

use Norvica\Validation\Instruction\OrX;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Uuid;
use Tests\Norvica\Validation\Single\ValidationTestCase;

final class OrXTest extends ValidationTestCase
{
    public function testValid(): void
    {
        // one is valid
        $this->assertValid(
            'e2575f66-47ea-4152-ba1e-0ed63dec1e4f',
            new OrX(
                new Email(),
                new Uuid(),
            ),
        );
    }

    public function testInvalid(): void
    {
        // none are valid
        $this->assertInvalid(
            'e2575f66',
            new OrX(
                new Email(),
                new Uuid(),
            ),
        );
    }
}

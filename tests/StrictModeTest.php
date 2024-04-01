<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation;

use Norvica\Validation\Exception\LogicException;
use Norvica\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class StrictModeTest extends TestCase
{
    public function testStrict(): void
    {
        $this->expectException(LogicException::class);

        $validator = new Validator();
        $validator->validate(value: ['email' => 'john.doe@example.com'], rules: []);
    }

    public function testRelaxed(): void
    {
        $validator = new Validator();
        $validator->validate(value: ['email' => 'john.doe@example.com'], rules: [], strict: false);

        $this->assertTrue(true);
    }
}

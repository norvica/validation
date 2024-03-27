<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Validatable;
use Norvica\Validation\Exception\ConstraintViolation;
use Norvica\Validation\Validator;
use PHPUnit\Framework\TestCase;

abstract class ValidationTestCase extends TestCase
{
    protected Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    public function assertValid(mixed $value, Validatable $constraint): void
    {
        try {
            $this->validator->check($value, $constraint);
        } catch (ConstraintViolation $violation) {
            $this->fail("Failed asserting value '{$value}' passed validation. {$violation->getMessage()}.");
        }

        $this->assertTrue(true);
    }

    public function assertInvalid(mixed $value, Validatable $constraint): void
    {
        $this->expectException(ConstraintViolation::class);

        $this->validator->check($value, $constraint);
    }
}

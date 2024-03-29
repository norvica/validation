<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Norvica\Validation\Exception\PropertyRuleViolation;
use Norvica\Validation\Rule\Rule;
use Norvica\Validation\Exception\ValueRuleViolation;
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

    public function assertValid(mixed $value, Rule|array|null $rules): void
    {
        try {
            $this->validator->validate($value, $rules);
        } catch (PropertyRuleViolation $violation) {
            $this->fail("Failed asserting value '{$value}' passed validation. {$violation->getMessage()}.");
        }

        $this->assertTrue(true);
    }

    public function assertInvalid(mixed $value, Rule|array|null $rules, array $path = []): void
    {
        try {
            $this->validator->validate($value, $rules);
        } catch (PropertyRuleViolation $violation) {
            $this->assertEquals($path, $violation->path);

            return;
        }

        $this->fail(
            sprintf(
                "Failed asserting that exception of type '%s' has been thrown.",
                PropertyRuleViolation::class,
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation;

use Norvica\Validation\Exception\PropertyRuleViolation;
use Norvica\Validation\Instruction\AndX;
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Instruction\OptionalX;
use Norvica\Validation\Instruction\OrX;
use Norvica\Validation\Rule\Rule;
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

    public function assertValid(mixed $value, Rule|OptionalX|EachX|AndX|OrX|array|null $rules): void
    {
        try {
            $this->validator->validate($value, $rules);
        } catch (PropertyRuleViolation $violation) {
            $this->fail("Failed asserting value '{$value}' passed validation. {$violation->getText()}.");
        }

        $this->assertTrue(true);
    }

    public function assertInvalid(mixed $value, Rule|OptionalX|EachX|AndX|OrX|array|null $rules, array $path = []): void
    {
        try {
            $this->validator->validate($value, $rules);
        } catch (PropertyRuleViolation $violation) {
            $this->assertEquals($path, $violation->getRawPath());

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

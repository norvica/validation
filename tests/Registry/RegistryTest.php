<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Registry;

use Norvica\Validation\Registry\Registry;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Validation\EmailValidation;
use Norvica\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    public function testMapRegistry(): void
    {
        $stub = static function(string $value, Email $rule) {};
        $validator = new Validator([EmailValidation::class => $stub]);
        $validator->validate('a@b', new Email());

        $this->assertTrue(true);
    }

    public function testCustomRegistry(): void
    {
        $stub = static function (string $value, Email $rule) {};
        $registry = new class ($stub) implements Registry {
            public function __construct(
                private $validation,
            ) {
            }

            public function get(string $validator): callable
            {
                return $this->validation;
            }

            public function has(string $validator): bool
            {
                return true;
            }
        };
        $validator = new Validator($registry);
        $validator->validate('a@b', new Email());

        $this->assertTrue(true);
    }
}

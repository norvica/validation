<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Instruction;

use Generator;
use Norvica\Validation\Instruction\OptionalX;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class OptionalXTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'empty' => [[], ['email' => new OptionalX(new Email())]];

        yield 'null' => [['email' => null], ['email' => new OptionalX(new Email())]];
    }

    #[DataProvider('valid')]
    public function testValid(mixed $value, array $rules): void
    {
        $this->assertValid($value, $rules);
    }

    public static function invalid(): Generator
    {
        yield 'empty' => [[], ['email' => new Email()], ['email']];

        yield 'null' => [['email' => null], ['email' => new Email()], ['email']];

        yield 'missing value' => [
            ['email' => 'john.doe@example.com'],
            ['email' => new Email(), 'password' => new Password()],
            ['password'],
        ];

        yield 'invalid value' => [['email' => 'a@b'], ['email' => new OptionalX(new Email())], ['email']];
    }

    #[DataProvider('invalid')]
    public function testInvalid(mixed $value, array $rules, array $path): void
    {
        $this->assertInvalid($value, $rules, $path);
    }
}

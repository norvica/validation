<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Array;

use Generator;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Ip;
use Norvica\Validation\Rule\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\Single\ValidationTestCase;

final class ArrayTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'login' => [
            ['email' => 'john.doe@example.com', 'password' => 'Ul1!oooo'],
            ['email' => new Email(), 'password' => new Password()],
        ];

        yield 'nested' => [
            ['allowed' => ['localhost' => '127.0.0.1']],
            ['allowed' => ['localhost' => new Ip()]],
        ];
    }

    #[DataProvider('valid')]
    public function testValid(array $values, array $rules): void
    {
        $this->assertValid($values, $rules);
    }

    public static function invalid(): Generator
    {
        yield 'login (invalid e-mail)' => [
            ['email' => 'john.doe@', 'password' => 'Ul1!oooo'],
            ['email' => new Email(), 'password' => new Password()],
            ['email'],
        ];

        yield 'login (invalid password)' => [
            ['email' => 'john.doe@example.com', 'password' => 'oooo'],
            ['email' => new Email(), 'password' => new Password()],
            ['password'],
        ];

        yield 'nested' => [
            ['allowed' => ['localhost' => '0.0.0']],
            ['allowed' => ['localhost' => new Ip()]],
            ['allowed', 'localhost'],
        ];
    }

    #[DataProvider('invalid')]
    public function testInvalid(array $values, array $rules, array $path): void
    {
        $this->assertInvalid($values, $rules, $path);
    }
}

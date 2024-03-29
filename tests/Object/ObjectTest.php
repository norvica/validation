<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Object;

use Generator;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Ip;
use Norvica\Validation\Rule\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\Single\ValidationTestCase;

final class ObjectTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'stdClass' => [
            (object) ['email' => 'john.doe@example.com', 'password' => 'Ul1!oooo'],
            ['email' => new Email(), 'password' => new Password()],
        ];

        yield 'anonymous class with array rules' => [
            new class ('john.doe@example.com', 'Ul1!oooo') {
                public function __construct(
                    public string $email,
                    public string $password,
                ) {
                }
            },
            ['email' => new Email(), 'password' => new Password()],
        ];

        yield 'anonymous class with attribute rules' => [
            new class ('john.doe@example.com', 'Ul1!oooo') {
                public function __construct(
                    #[Email]
                    public string $email,
                    #[Password]
                    public string $password,
                ) {
                }
            },
            null,
        ];

        yield 'anonymous class with nested class attribute rules' => [
            new class (
                new class ('0.0.0.0') {
                    public function __construct(
                        #[Ip]
                        public string $localhost,
                    ) {
                    }
                },
            ) {
                public function __construct(
                    public object $allowed,
                ) {
                }
            },
            null,
        ];

        yield 'anonymous class with nested class array rules' => [
            new class (
                new class ('0.0.0.0') {
                    public function __construct(
                        public string $localhost,
                    ) {
                    }
                },
            ) {
                public function __construct(
                    public object $allowed,
                ) {
                }
            },
            ['allowed' => ['localhost' => new Ip()]],
        ];
    }

    #[DataProvider('valid')]
    public function testValid(object $value, array|null $rules): void
    {
        $this->assertValid($value, $rules);
    }

    public static function invalid(): Generator
    {
        yield 'stdClass' => [
            (object) ['email' => 'john.doe@example.com', 'password' => 'oooo'],
            ['email' => new Email(), 'password' => new Password()],
            ['password'],
        ];

        yield 'stdClass (nested)' => [
            (object) ['allowed' => ['localhost' => '0.0.0']],
            ['allowed' => ['localhost' => new Ip()]],
            ['allowed', 'localhost'],
        ];

        yield 'anonymous class with array rules' => [
            new class ('john.doe@example.com', 'oooo') {
                public function __construct(
                    public string $email,
                    public string $password,
                ) {
                }
            },
            ['email' => new Email(), 'password' => new Password()],
            ['password'],
        ];

        yield 'anonymous class with attribute rules' => [
            new class ('john.doe@example.com', 'oooo') {
                public function __construct(
                    #[Email]
                    public string $email,
                    #[Password]
                    public string $password,
                ) {
                }
            },
            null,
            ['password'],
        ];

        yield 'anonymous class with nested class attribute rules' => [
            new class (
                new class ('0.0.0.') {
                    public function __construct(
                        #[Ip]
                        public string $localhost,
                    ) {
                    }
                },
            ) {
                public function __construct(
                    public object $allowed,
                ) {
                }
            },
            null,
            ['allowed', 'localhost'],
        ];

        yield 'anonymous class with nested class array rules' => [
            new class (
                new class ('0.0.0.') {
                    public function __construct(
                        public string $localhost,
                    ) {
                    }
                },
            ) {
                public function __construct(
                    public object $allowed,
                ) {
                }
            },
            ['allowed' => ['localhost' => new Ip()]],
            ['allowed', 'localhost'],
        ];
    }

    #[DataProvider('invalid')]
    public function testInvalid(object $value, array|null $rules, array $path): void
    {
        $this->assertInvalid($value, $rules, $path);
    }
}

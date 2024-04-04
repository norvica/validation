<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class EmailTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'uppercase' => ['JOHN@EXAMPLE.COM'];
        yield 'local part with .' => ['john.doe@example.com'];
        yield 'local part with +' => ['john+doe@example.com'];
        yield 'local part with -' => ['john-doe@example.com'];
        yield 'local part with _' => ['john_doe@example.com'];
        // less common cases
        yield 'local part with !' => ['john!doe@example.com'];
        yield 'local part with $' => ['john$doe@example.com'];
        yield 'local part with %' => ['john%doe@example.com'];
        yield 'local part with *' => ['john*doe@example.com'];
        yield "local part with '" => ["john'doe@example.com"];
        yield 'local part with ~' => ['john~doe@example.com'];
    }

    #[DataProvider('valid')]
    public function testValid(string $value): void
    {
        $this->assertValid($value, new Email());
    }

    #[DataProvider('valid')]
    public function testValidDns(string $value): void
    {
        $this->assertValid($value, new Email(dns: true));
    }

    public static function invalid(): Generator
    {
        yield 'invalid domain' => ['a@b.c'];
        yield 'only local part' => ['john.doe'];
        yield 'no domain part' => ['john.doe@'];
        yield 'no local part' => ['@example.com'];
        yield 'no domain extension' => ['john.doe@example'];
        yield '@ replacement' => ['john.doe(at)example.com'];
        yield 'consecutive dots' => ['john..doe@example.com'];
        yield 'consecutive dots in domain' => ['john.doe@example..com'];
        yield 'invalid character' => ['john#@example.com'];
        yield 'domain starting with a hyphen' => ['john.doe@-test.com'];
        yield 'local part is too long' => [str_pad('', 65, 'a') . '@example.com'];
        yield 'domain part is too long' => ['john.doe@' . str_pad('', 255, 'a') . '.com'];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value): void
    {
        $this->assertInvalid($value, new Email());
    }

    public static function invalidDns(): Generator
    {
        yield 'domain does not exist' => ['john.doe@domainthatpresumablydoesnotexist.com'];
    }

    #[DataProvider('invalidDns')]
    public function testInvalidDns(string $value): void
    {
        $this->assertInvalid($value, new Email(dns: true));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class PasswordTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'minimal requirements' => ['Oo1!oooo'];
        yield 'with space' => ['Oo1! ooo'];
    }

    #[DataProvider('valid')]
    public function testValid(string $value): void
    {
        $this->assertValid($value, new Password());
    }

    public static function invalid(): Generator
    {
        yield 'too short' => ['Oo1!ooo'];
        yield 'too long' => ['Oo1!' . str_pad('', 125, 'o')];
        yield 'missing uppercase' => ['oo1!oooo'];
        yield 'missing lower' => ['OO1!OOOO'];
        yield 'missing number' => ['Ooo!oooo'];
        yield 'missing special' => ['Oo1ooooo'];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value): void
    {
        $this->assertInvalid($value, new Password());
    }
}

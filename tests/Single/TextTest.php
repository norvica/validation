<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Text;
use PHPUnit\Framework\Attributes\DataProvider;

final class TextTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'string' => [' abc ', new Text(minLength: 1, maxLength: 3, regExp: '#^\w+$#')];
        yield 'number' => [' 123 ', new Text(minLength: 1, maxLength: 3, regExp: '#^\d+$#')];
    }

    #[DataProvider('valid')]
    public function testValid(string $value, Text $rule): void
    {
        $this->assertValid($value, $rule);
    }

    public static function invalid(): Generator
    {
        yield 'too short' => ['abc', new Text(minLength: 4, regExp: '#^\w+$#')];
        yield 'too long' => ['abc', new Text( maxLength: 2, regExp: '#^\w+$#')];
        yield 'pattern mismatch' => ['abc', new Text(maxLength: 2, regExp: '#^\d+$#')];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value, Text $rule): void
    {
        $this->assertInvalid($value, $rule);
    }
}

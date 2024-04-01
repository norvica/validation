<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Flag;
use PHPUnit\Framework\Attributes\DataProvider;

final class FlagTest extends ValidationTestCase
{
    public static function on(): Generator
    {
        yield 'yes' => ['Yes'];
        yield 'on' => ['ON'];
        yield 'string true' => [' true '];
        yield 'boolean true' => [true];
        yield 'string 1' => ['1'];
        yield 'integer 1' => [1];
    }

    #[DataProvider('on')]
    public function testOnValid(mixed $value): void
    {
        $this->assertValid($value, new Flag(true));
    }

    #[DataProvider('off')]
    public function testOnInvalid(mixed $value): void
    {
        $this->assertInvalid($value, new Flag(true));
    }

    public static function off(): Generator
    {
        yield 'no' => ['No'];
        yield 'off' => ['OFF'];
        yield 'string false' => [' false '];
        yield 'boolean false' => [false];
        yield 'string 0' => ['0'];
        yield 'integer 0' => [0];
        yield 'empty string' => [' '];
    }

    #[DataProvider('off')]
    public function testOffValid(mixed $value): void
    {
        $this->assertValid($value, new Flag(false));
    }

    #[DataProvider('on')]
    public function testOffInvalid(mixed $value): void
    {
        $this->assertInvalid($value, new Flag(false));
    }
}

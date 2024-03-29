<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Option;
use PHPUnit\Framework\Attributes\DataProvider;

final class OptionTest extends ValidationTestCase
{
    public static function singleValid(): Generator
    {
        yield 'string option' =>  [' a ', new Option(options: ['a', 'b', 'c'], multiple: false)];
        yield 'numeric option' => [' 2 ', new Option(options: ['0', '1', '2'], multiple: false)];
    }

    public static function multipleValid(): Generator
    {
        yield 'string option' => [[' a', 'c '], new Option(options: ['a', 'b', 'c'], multiple: true)];
        yield 'numeric option' => [[' 0', '2 '], new Option(options: ['0', '1', '2'], multiple: true)];
    }

    #[DataProvider('singleValid')]
    #[DataProvider('multipleValid')]
    public function testValid(mixed $value, Option $constraint): void
    {
        $this->assertValid($value, $constraint);
    }

    public static function singleInvalid(): Generator
    {
        yield 'string option' => ['d', new Option(options: ['a', 'b', 'c'], multiple: false)];
        yield 'numeric option' => ['4', new Option(options: ['0', '1', '2'], multiple: false)];
    }

    public static function multipleInvalid(): Generator
    {
        yield 'string option' => [['a', 'd'], new Option(options: ['a', 'b', 'c'], multiple: true)];
        yield 'numeric option' => [['0', '4'], new Option(options: ['0', '1', '2'], multiple: true)];
    }

    #[DataProvider('singleInvalid')]
    #[DataProvider('multipleInvalid')]
    public function testInvalid(mixed $value, Option $constraint): void
    {
        $this->assertInvalid($value, $constraint);
    }
}

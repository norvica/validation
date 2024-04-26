<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Option;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class OptionTest extends ValidationTestCase
{
    public static function singleValid(): Generator
    {
        yield 'string option (single)' =>  [' a ', new Option(options: ['a', 'b', 'c'], multiple: false)];
        yield 'numeric option (single)' => [' 2 ', new Option(options: ['0', '1', '2'], multiple: false)];
    }

    public static function multipleValid(): Generator
    {
        yield 'string option (multiple)' => [[' a', 'c '], new Option(options: ['a', 'b', 'c'], multiple: true)];
        yield 'numeric option (multiple)' => [[' 0', '2 '], new Option(options: ['0', '1', '2'], multiple: true)];
    }

    #[DataProvider('singleValid')]
    #[DataProvider('multipleValid')]
    public function testValid(mixed $value, Option $rule): void
    {
        $this->assertValid($value, $rule);
    }

    public static function singleInvalid(): Generator
    {
        yield 'string option (single)' => ['d', new Option(options: ['a', 'b', 'c'], multiple: false)];
        yield 'numeric option (single)' => ['4', new Option(options: ['0', '1', '2'], multiple: false)];
    }

    public static function multipleInvalid(): Generator
    {
        yield 'string option (multiple)' => [['a', 'd'], new Option(options: ['a', 'b', 'c'], multiple: true)];
        yield 'numeric option (multiple)' => [['0', '4'], new Option(options: ['0', '1', '2'], multiple: true)];
    }

    #[DataProvider('singleInvalid')]
    #[DataProvider('multipleInvalid')]
    public function testInvalid(mixed $value, Option $rule): void
    {
        $this->assertInvalid($value, $rule);
    }
}

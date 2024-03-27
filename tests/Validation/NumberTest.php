<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Validation;

use Generator;
use Norvica\Validation\Constraint\Number;
use PHPUnit\Framework\Attributes\DataProvider;

final class NumberTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'positive' => [10, new Number(min: 0)];
        yield 'negative' => [-10, new Number(max: 0)];
        yield 'range with integer' => [99, new Number(min: 0, max: 100)];
        yield 'range with integer string' => [' 99 ', new Number(min: 0, max: 100)];
        yield 'range with float string' => [' 99.9 ', new Number(min: 0, max: 100)];
    }

    #[DataProvider('valid')]
    public function testValid(mixed $value, Number $constraint): void
    {
        $this->assertValid($value, $constraint);
    }

    public static function invalid(): Generator
    {
        yield 'positive' => [-10, new Number(min: 0)];
        yield 'negative' => [10, new Number(max: 0)];
        yield 'range with integer' => [101, new Number(min: 0, max: 100)];
        yield 'range with integer string' => [' 101 ', new Number(min: 0, max: 100)];
        yield 'range with float string' => [' 101.01 ', new Number(min: 0, max: 100)];
    }

    #[DataProvider('invalid')]
    public function testInvalid(mixed $value, Number $constraint): void
    {
        $this->assertInvalid($value, $constraint);
    }
}
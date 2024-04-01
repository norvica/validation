<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Slug;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class SlugTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'hyphen separated' => ['abc-def'];
        yield 'alphanumeric' => ['abc-123'];
        yield 'underscore separated' => ['abc_def'];
    }

    #[DataProvider('valid')]
    public function testValid(string $value): void
    {
        $this->assertValid($value, new Slug());
    }

    public static function invalid(): Generator
    {
        yield 'too short' => ['a'];
        yield 'too long' => [str_pad('', 65, 'a')];
        yield 'special characters' => ['$abc'];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value): void
    {
        $this->assertInvalid($value, new Slug());
    }
}

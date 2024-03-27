<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Validation;

use Generator;
use Norvica\Validation\Constraint\Uuid;
use PHPUnit\Framework\Attributes\DataProvider;

final class UuidTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'v1' => ['a2952e6e-ec71-11ee-a951-0242ac120002', 1];
        yield 'v3' => ['6fa459ea-ee8a-3ca4-894e-db77e160355e', 3];
        yield 'v4' => ['71462c0e-18f0-4524-b4d8-7826a3f57949', 4];
        yield 'v5' => ['886313e1-3b8a-5372-9b90-0c9aee199e5d', 5];
        yield 'v7' => ['018e816c-6623-7bcb-9412-b595101f31be', 7];
    }

    #[DataProvider('valid')]
    public function testValid(string $value, int $version): void
    {
        $this->assertValid($value, new Uuid($version));
    }

    #[DataProvider('valid')]
    public function testValidGeneric(string $value): void
    {
        $this->assertValid($value, new Uuid());
    }

    public static function invalid(): Generator
    {
        yield 'v1 as v3' => ['a2952e6e-ec71-11ee-a951-0242ac120002', 3];
        yield 'v3 as v4' => ['6fa459ea-ee8a-3ca4-894e-db77e160355e', 4];
        yield 'v4 as v5' => ['71462c0e-18f0-4524-b4d8-7826a3f57949', 5];
        yield 'v5 as v7' => ['886313e1-3b8a-5372-9b90-0c9aee199e5d', 7];
        yield 'v7 as v1' => ['018e816c-6623-7bcb-9412-b595101f31be', 1];
        yield 'too short' => ['a2952e6e-ec71-11ee-a951-0242ac1200', null];
        yield 'invalid characters' => ['a2952e6e-ec71-11ee-a951-0242ac1g0002', null];
        yield 'incorrect hyphen placement' => ['a2952e6eec71-11eea951-0242ac120002', null];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value, int|null $version): void
    {
        $this->assertInvalid($value, new Uuid($version));
    }
}

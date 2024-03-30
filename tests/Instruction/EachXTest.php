<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Instruction;

use Generator;
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Rule\Ip;
use Norvica\Validation\Rule\Uuid;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\Single\ValidationTestCase;

final class EachXTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'direct' => [
            ['127.0.0.1', '0.0.0.0'],
            new EachX(new Ip()),
        ];

        yield 'scalar' => [
            ['allowed' => ['127.0.0.1', '0.0.0.0']],
            ['allowed' => new EachX(new Ip())],
        ];

        yield 'arrays' => [
            ['users' => [
                ['id' => '82de6575-7853-42d3-b962-dc466de7ce10'],
                ['id' => 'e2575f66-47ea-4152-ba1e-0ed63dec1e4f'],
            ]],
            ['users' => new EachX(['id' => new Uuid(4)])],
        ];
    }

    #[DataProvider('valid')]
    public function testValid(array $data, EachX|array $rules): void
    {
        $this->assertValid($data, $rules);
    }

    public static function invalid(): Generator
    {
        yield 'direct' => [
            ['a.b.c.d', '0.0.0.0'],
            new EachX(new Ip()),
            [0],
        ];

        yield 'scalar' => [
            ['allowed' => ['127.0.0.1', '0.0.0']],
            ['allowed' => new EachX(new Ip())],
            ['allowed', 1],
        ];

        yield 'arrays' => [
            ['users' => [
                ['id' => '82de6575'],
                ['id' => 'e2575f66-47ea-4152-ba1e-0ed63dec1e4f'],
            ]],
            ['users' => new EachX(['id' => new Uuid(4)])],
            ['users', 0, 'id'],
        ];
    }

    #[DataProvider('invalid')]
    public function testInvalid(array $data, EachX|array $rules, array $path): void
    {
        $this->assertInvalid($data, $rules, $path);
    }
}

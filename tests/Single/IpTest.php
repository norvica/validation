<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Ip;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class IpTest extends ValidationTestCase
{
    public static function validIpV4(): Generator
    {
        yield '0.0.0.0' => ['0.0.0.0'];
        yield '127.0.0.1' => ['127.0.0.1'];
        yield '192.168.0.100' => ['192.168.0.100'];
        yield '255.255.255.255' => ['255.255.255.255'];
    }

    #[DataProvider('validIpV4')]
    public function testValidV4(string $value): void
    {
        $this->assertValid($value, new Ip(4));
    }

    public static function invalidIpV4(): Generator
    {
        yield 'out of range (V4)' => ['256.10.10.10'];
        yield 'too few segments (V4)' => ['10.20.30'];
        yield 'too many segments (V4)' => ['10.20.30.40.50'];
        yield 'non-numeric characters (V4)' => ['192.168.0.x'];
    }

    #[DataProvider('invalidIpV4')]
    public function testInvalidV4(string $value): void
    {
        $this->assertInvalid($value, new Ip(4));
    }

    public static function validIpV6(): Generator
    {
        yield 'basic' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        yield 'zero compression' => ['2001:db8::8a2e:370:7334'];
        yield 'IPv4 embedding' => ['::ffff:192.168.1.1'];
    }

    #[DataProvider('validIpV6')]
    public function testValidV6(string $value): void
    {
        $this->assertValid($value, new Ip(6));
    }

    public static function invalidIpV6(): Generator
    {
        yield 'too many segments (V6)' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334:1'];
        yield 'invalid HEX characters (V6)' => ['fe80:0000:0000:0000:0202:b3gg:fe1e:8329'];
        yield 'invalid IPv4 embedding (V6)' => ['::ffff:300.1.1.5'];
    }

    #[DataProvider('invalidIpV6')]
    public function testInvalidV6(string $value): void
    {
        $this->assertInvalid($value, new Ip(6));
    }

    #[DataProvider('validIpV4')]
    #[DataProvider('validIpV6')]
    public function testValidGeneric(string $value): void
    {
        $this->assertValid($value, new Ip());
    }

    #[DataProvider('invalidIpV4')]
    #[DataProvider('invalidIpV6')]
    public function testInvalidGeneric(string $value): void
    {
        $this->assertInvalid($value, new Ip());
    }
}

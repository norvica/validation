<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Hostname;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class HostnameTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        $a = str_pad('', 63, 'a');
        $b = str_pad('', 63, 'b');
        $c = str_pad('', 63, 'c');
        $d = str_pad('', 61, 'd');

        yield 'domain' => ['example.com'];
        yield 'subdomain' => ['blog.example.com'];
        yield 'nested subdomain' => ['admin.blog.example.com'];
        yield 'hyphen' => ['foo-bar.com'];
        yield 'max label length' => ["{$a}.com"];
        yield 'max total length' => ["{$a}.{$b}.{$c}.{$d}"];
        yield 'shortest possible' => ["a.co"];
    }

    #[DataProvider('valid')]
    public function testValid(string $value): void
    {
        $this->assertValid($value, new Hostname());
    }

    public static function validWithHosts(): Generator
    {
        yield 'domain' => ['example.com', ['example.com']];
        yield 'subdomain' => ['blog.example.com', ['*.example.com']];
        yield 'nested subdomain' => ['admin.blog.example.com', ['*.example.com']];
    }

    #[DataProvider('validWithHosts')]
    public function testValidWithHosts(string $value, array $hosts): void
    {
        $this->assertValid($value, new Hostname(hosts: $hosts));
    }

    public static function invalid(): Generator
    {
        $a = str_pad('', 63, 'a');
        $b = str_pad('', 63, 'b');
        $c = str_pad('', 63, 'c');
        $d = str_pad('', 62, 'd');

        yield 'label starts with hyphen' => ['-example.com'];
        yield 'label ends with hyphen' => ['example-.com'];
        yield 'TLD starts with hyphen' => ['example.-com'];
        yield 'label length exceeds 63' => [str_pad('', 64, 'a') . '.com'];
        yield 'total length exceeds 253' => ["{$a}.{$b}.{$c}.{$d}"];
        yield 'empty label' => ['example..com'];
        yield 'special characters in label' => ['ex*ample.com'];
        yield 'single character TLD' => ['example.c'];
        yield 'missing TLD' => ['foo'];
        yield 'IP address' => ['192.168.1.1'];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value): void
    {
        $this->assertInvalid($value, new Hostname());
    }

    public static function invalidWithHosts(): Generator
    {
        yield 'precise domain' => ['blog.example.com', ['example.com']];
        yield 'precise subdomain' => ['admin.blog.example.com', ['blog.example.com']];
    }

    #[DataProvider('invalidWithHosts')]
    public function testInvalidWithHosts(string $value, array $hosts): void
    {
        $this->assertInvalid($value, new Hostname(hosts: $hosts));
    }
}

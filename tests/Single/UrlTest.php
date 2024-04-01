<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Url;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class UrlTest extends ValidationTestCase
{
    public static function validExampleDotCom(): Generator
    {
        yield 'basic HTTP' => ['http://example.com'];
        yield 'basic HTTPS' => ['https://example.com'];
        yield 'www' => ['https://www.example.com'];
        yield 'path' => ['https://example.com/path/to/page'];
        yield 'path to file' => ['https://example.com/index.html'];
        yield 'query parameters' => ['https://example.com/?key=value'];
        yield 'port' => ['https://example.com:8080'];
        yield 'hash' => ['https://example.com/#anchor'];
        yield 'authentication' => ['https://user:password@example.com'];
        yield 'combined' => ['https://user:password@example.com:443/path/to/page?key=value#some-anchor'];
    }

    public static function validExampleDotComSubdomains(): Generator
    {
        yield 'subdomain' => ['https://blog.example.com'];
        yield 'nested subdomain' => ['https://admin.blog.example.com'];
    }

    public static function validExampleTld(): Generator
    {
        yield 'example store query' => ['https://www.store.example/products?category=electronics&price_min=100&price_max=1000&sort=price_desc&brands=Samsung,Sony,LG&in_stock=true&page=2'];
        yield 'example job search query' => ['https://www.jobs.example/search?position=developer&skills[]=javascript&skills[]=python&skills[]=react&experience_level=mid-senior&remote=true'];
        yield 'example book search' => ['https://www.bookstore.example/search?query=Harry+Potter+and+the+Philosopher%27s+Stone'];
    }

    public static function validIp(): Generator
    {
        yield 'IPv4' => ['http://127.0.0.1'];
        yield 'IPv6' => ['http://[::1]'];
    }

    #[DataProvider('validExampleDotCom')]
    #[DataProvider('validExampleDotComSubdomains')]
    #[DataProvider('validExampleTld')]
    #[DataProvider('validIp')]
    public function testValid(string $value): void
    {
        $this->assertValid($value, new Url());
    }


    #[DataProvider('validExampleDotCom')]
    public function testValidWithPreciseHost(string $value): void
    {
        $this->assertValid($value, new Url(hosts: ['example.com']));
    }

    #[DataProvider('validExampleDotComSubdomains')]
    #[DataProvider('validExampleTld')]
    #[DataProvider('validIp')]
    public function testInvalidWithPreciseHost(string $value): void
    {
        $this->assertInvalid($value, new Url(hosts: ['example.com']));
    }

    #[DataProvider('validExampleDotCom')]
    #[DataProvider('validExampleDotComSubdomains')]
    public function testValidWithWildcardHost(string $value): void
    {
        $this->assertValid($value, new Url(hosts: ['*.example.com']));
        $this->assertValid($value, new Url(hosts: ['*.com']));
    }

    #[DataProvider('validExampleTld')]
    #[DataProvider('validIp')]
    public function testInvalidWithWildcardHost(string $value): void
    {
        $this->assertInvalid($value, new Url(hosts: ['*.test.com']));
    }

    public static function invalid(): Generator
    {
        yield 'missing scheme' => ['example.com'];
        yield 'unsupported scheme' => ['ftp://example.com'];
        yield 'invalid scheme' => ['htp://example.com'];
        yield 'scheme only' => ['https://'];
        yield 'invalid domain' => ['https://example..com'];
        yield 'space' => ['https://ex ample.com'];
        yield 'invalid characters in domain name' => ['https://ex*ample.com'];
        yield 'unsafe query' => ['https://example.com/?name=<script></script>'];
        yield 'unsafe fragment' => ['https://www.example.com/blog#<script></script>'];
        yield 'incomplete IPv4 address' => ['http://127.0.0'];
        yield 'invalid IPv6 address' => ['http://[gggg::gggg]'];
        yield 'invalid port' => ['http://example.com:65536'];
        yield 'missing TLD' => ['https://google'];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value): void
    {
        $this->assertInvalid($value, new Url());
    }
}

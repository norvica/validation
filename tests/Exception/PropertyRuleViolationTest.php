<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Exception;

use Generator;
use Norvica\Validation\Exception\PropertyRuleViolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PropertyRuleViolationTest extends TestCase
{
    public static function cases(): Generator
    {
        yield 'empty' => [[], ''];
        yield '1 level' => [['foo'], 'foo'];
        yield '2 levels' => [['foo', 'bar'], 'foo.bar'];
        yield 'list' => [['file', 0, 'size'], 'file[0].size'];
    }

    #[DataProvider('cases')]
    public function testFormat(array $path, string $expectation): void
    {
        $this->assertEquals($expectation, (new PropertyRuleViolation(path: $path))->getPath());
    }
}

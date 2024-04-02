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
        yield 'empty' => [[], '', 'A'];
        yield '1 level' => [['foo'], 'foo', 'B'];
        yield '2 levels' => [['foo', 'bar'], 'foo.bar', 'C'];
        yield 'list' => [['file', 0, 'size'], 'file[0].size', 'D'];
    }

    #[DataProvider('cases')]
    public function testFormat(array $path, string $string, string $message): void
    {
        $violation = new PropertyRuleViolation(message: $message, path: $path);
        $this->assertEquals($string, $violation->getPath());
        $this->assertEquals($message, $violation->getText());
    }
}

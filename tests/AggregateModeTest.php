<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation;

use Norvica\Validation\Instruction\AndX;
use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Instruction\OrX;
use Norvica\Validation\Options;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Hostname;
use Norvica\Validation\Rule\Ip;
use Norvica\Validation\Rule\Slug;
use Norvica\Validation\Rule\Text;
use Norvica\Validation\Rule\Uuid;
use Norvica\Validation\Validator;
use Norvica\Validation\Violation\TraceableViolation;
use PHPUnit\Framework\TestCase;

final class AggregateModeTest extends TestCase
{
    public function testInvalid(): void
    {
        $data = [
            'host' => '<invalid>',
            'title' => 'abcde',
            'users' => [
                ['id' => '<invalid>', 'email' => '<invalid>'],
                ['id' => '94a163ef-3e1e-407a-b20f-a695fb720afc', 'email' => 'alice@example.com'],
                ['id' => '<invalid>', 'email' => 'bob@example.com'],
            ],
            'allowed' => ['127.0.0.1', '<invalid>', '192.168.1.1'],
        ];

        $rules = [
            'slug' => new Slug(),
            'title' => new AndX(new Text(maxLength: 4), new Text(regExp: '#^[A-Z]{1}\w+$#')),
            'host' => new OrX(new Hostname(), new Ip()),
            'users' => new EachX(['id' => new Uuid(), 'email' => new Email()]),
            'allowed' => new EachX(new Ip()),
        ];

        $validator = new Validator();
        $result = $validator->validate($data, $rules, new Options(throw: false));

        $i = -1;
        $this->assertCount(8, $result->violations);
        $this->assertEquals('slug | Value is required.', self::format($result->violations[++$i]));
        $this->assertEquals('title | Value must be no more than 4 characters long', self::format($result->violations[++$i]));
        $this->assertEquals("title | Value doesn't match the required format", self::format($result->violations[++$i]));
        $this->assertEquals('host | Value does not match any of the configured rules.', self::format($result->violations[++$i]));
        $this->assertEquals('users[0].id | Value must be a valid UUID', self::format($result->violations[++$i]));
        $this->assertEquals('users[0].email | Value must be a valid E-mail address', self::format($result->violations[++$i]));
        $this->assertEquals('users[2].id | Value must be a valid UUID', self::format($result->violations[++$i]));
        $this->assertEquals('allowed[1] | Value must be a valid IP address', self::format($result->violations[++$i]));
    }

    private static function format(TraceableViolation $violation): string
    {
        return "{$violation->getPath()} | {$violation->getText()}";
    }
}

<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation;

use Norvica\Validation\Instruction\EachX;
use Norvica\Validation\Rule\Email;
use Norvica\Validation\Rule\Flag;
use Norvica\Validation\Rule\Hostname;
use Norvica\Validation\Rule\Iban;
use Norvica\Validation\Rule\Option;
use Norvica\Validation\Rule\Text;
use Norvica\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class NormalizationTest extends TestCase
{
    public function testNormalized(): void
    {
        $data = [
            'email' => ' JOHN.DOE@EXAMPLE.COM ',
            'subscription' => ' oFF ',
            'domain' => ' EXAMPLE.com ',
            'transactions' => [
                (object) ['account' => ' gb33 bukb 2020 1555 5555 55 ', 'message' => ' From John. '],
            ],
            'address' => new class (city: ' Berlin ', code: ' 10117 ', tags: [' billing ', ' delivery ']) {
                public function __construct(
                    #[Text(minLength: 2, maxLength: 64, regExp: '#^\w+$#')]
                    public string $city,
                    #[Text(regExp: '#^\d{5}$#')]
                    public string $code,
                    #[Option(options: ['billing', 'delivery'], multiple: true)]
                    public array $tags,
                ) {}
            },
        ];

        $rules = [
            'email' => new Email(),
            'subscription' => new Flag(value: false),
            'domain' => new Hostname(),
            'transactions' => new EachX([
                'account' => new Iban(),
                'message' => new Text(maxLength: 128),
            ]),
        ];

        $expected = [
            'email' => 'john.doe@example.com',
            'subscription' => false,
            'domain' => 'example.com',
            'transactions' => [
                ['account' => 'GB33BUKB20201555555555', 'message' => 'From John.'],
            ],
            'address' => ['city' => 'Berlin', 'code' => '10117', 'tags' => ['billing', 'delivery']],
        ];

        $validator = new Validator();
        $result = $validator->validate($data, $rules);

        $this->assertEquals($expected, $result->normalized);
    }
}

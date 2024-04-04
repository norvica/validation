<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Email;
use Norvica\Validation\Exception\ValueRuleViolation;
use Norvica\Validation\Rule\Hostname;

final class EmailValidation
{
    public function __invoke(string $value, Email $rule): void
    {
        $message = 'Value must be a valid E-mail address';

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValueRuleViolation($message);
        }

        [$local, $domain] = explode('@', $value, 2);

        if (!preg_match('/^[a-zA-Z0-9.!$%&\'*+\/=?^_`{|}~-]{1,64}$/', $local)) {
            throw new ValueRuleViolation($message);
        }

        (new HostnameValidation())($domain, new Hostname());

        // DNS check (A/MX records)
        if ($rule->dns && !checkdnsrr($domain) && !checkdnsrr($domain, 'A')) {
            throw new ValueRuleViolation($message);
        }
    }
}

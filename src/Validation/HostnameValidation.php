<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Exception\ValueRuleViolation;
use Norvica\Validation\Rule\Hostname;

final class HostnameValidation
{
    /**
     * @see https://datatracker.ietf.org/doc/html/rfc2606#section-2
     */
    private const RESERVED = ['test', 'example', 'invalid', 'localhost'];

    public function __invoke(string $value, Hostname $rule): void
    {
        $message = 'Value must be a valid hostname';

        if ($rule->reserved && in_array($value, self::RESERVED)) {
            return;
        }

        if (!filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new ValueRuleViolation($message);
        }

        if ($rule->hosts !== null) {
            $this->hosts($value, $rule->hosts);
        }

        // missing TLD
        $parts = explode('.', $value);
        if (count($parts) < 2) {
            throw new ValueRuleViolation($message);
        }

        // TLD is too short
        $tld = array_pop($parts);
        if (strlen($tld) < 2) {
            throw new ValueRuleViolation($message);
        }

        if ($rule->dns && !checkdnsrr($value, 'A') && !checkdnsrr($value, 'AAAA')) {
            throw new ValueRuleViolation($message);
        }
    }

    /**
     * @param string[] $hosts
     */
    public function hosts(string $host, array $hosts): void
    {
        foreach ($hosts as $restriction) {
            // full match
            if ($host === $restriction || $host === "www.{$restriction}") {
                return;
            }

            // wildcard match
            if (str_starts_with($restriction, '*.')
                && str_ends_with($host, ltrim($restriction, '*.'))) {
                return;
            }
        }

        throw new ValueRuleViolation('Host is not allowed');
    }
}

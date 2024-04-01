<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Exception\ValueRuleViolation;
use Norvica\Validation\Rule\Hostname;
use Norvica\Validation\Rule\Ip;
use Norvica\Validation\Rule\Url;

final class UrlValidation
{
    public function __invoke(string $value, Url $rule): void
    {
        $message = 'Value must be a valid URL';

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new ValueRuleViolation($message);
        }

        $parts = parse_url($value);

        $scheme = $parts['scheme'] ?? '';
        if (!in_array($scheme, $rule->schemes, true)) {
            throw new ValueRuleViolation('Scheme is not allowed');
        }

        $host = $parts['host'] ?? '';
        if ($rule->hosts !== null) {
            $this->hosts($host, $rule->hosts);
        }

        if (str_starts_with($host, '[') && str_ends_with($host, ']')) {
            // presumably IPv6
            (new IpValidation())(trim($host, '[]'), new Ip(6));
        } elseif (false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // IPv4
            (new IpValidation())($host, new Ip(4));
        } else {
            // hostname
            (new HostnameValidation())($host, new Hostname(dns: $rule->dns, reserved: $rule->reserved));
        }

        $path = $parts['path'] ?? '';
        if ($path && !preg_match('#^/(?:[a-zA-Z0-9\-._~!$&\'()*+,;=:@]+/?)*$#', $path)) {
            throw new ValueRuleViolation($message);
        }

        $query = $parts['query'] ?? '';
        if ($query && !preg_match("#^([a-zA-Z0-9\[\]_-]+=[\w\-.~:/?\#\[\]@!$&'()*+,;= %]*)*&?$#", $query)) {
            throw new ValueRuleViolation($message);
        }

        $fragment = $parts['fragment'] ?? '';
        if ($fragment && !preg_match("#^(?:[a-zA-Z0-9\-._~!$&'()*+,;=:@/?]+)?$#", $fragment)) {
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

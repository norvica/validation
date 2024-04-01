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
        if ($this->ip($host)) {
            // don't allow bypassing hosts restrictions by entering an IP address
            if ($rule->hosts !== null) {
                throw new ValueRuleViolation('Value must contain a valid hostname');
            }

            (new IpValidation())(trim($host, '[]'), new Ip());
        } else {
            // hostname
            (new HostnameValidation())(
                $host,
                new Hostname(
                    hosts: $rule->hosts,
                    dns: $rule->dns,
                    reserved: $rule->reserved,
                )
            );
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

    private function ip(string $host): bool
    {
        return (str_starts_with($host, '[') && str_ends_with($host, ']')) // IPv6
            || false !== filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4); // IPv4
    }
}

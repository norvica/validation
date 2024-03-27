<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Constraint\Ip;
use Norvica\Validation\Exception\ConstraintViolation;

final class IpValidation
{
    public function __invoke(string $value, Ip $constraint): void
    {
        $v4 = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#';
        $v6 = '#^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,2})\.){3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,2})|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,2})\.){3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,2}))$#';
        $valid = match ($constraint->version) {
            4 => preg_match($v4, $value) === 1,
            6 => preg_match($v6, $value) === 1,
            null => preg_match($v4, $value) === 1 || preg_match($v6, $value) === 1,
        };

        if (!$valid) {
            throw new ConstraintViolation(
                $constraint->version
                    ? "Value must be a valid IPv{$constraint->version} address"
                    : 'Value must be a valid IP address'
            );
        }
    }
}

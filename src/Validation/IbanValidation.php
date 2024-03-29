<?php

declare(strict_types=1);

namespace Norvica\Validation\Validation;

use Norvica\Validation\Rule\Iban;
use Norvica\Validation\Exception\ValueRuleViolation;

/**
 * @see https://www.iban.com/structure
 */
final class IbanValidation
{
    private const LENGTH = [
        'AD' => 24,
        'AE' => 23,
        'AL' => 28,
        'AT' => 20,
        'AZ' => 28,
        'BA' => 20,
        'BE' => 16,
        'BG' => 22,
        'BH' => 22,
        'BI' => 27,
        'BR' => 29,
        'BY' => 28,
        'CH' => 21,
        'CR' => 22,
        'CY' => 28,
        'CZ' => 24,
        'DE' => 22,
        'DJ' => 27,
        'DK' => 18,
        'DO' => 28,
        'EE' => 20,
        'EG' => 29,
        'ES' => 24,
        'FI' => 18,
        'FK' => 18,
        'FO' => 18,
        'FR' => 27,
        'GB' => 22,
        'GE' => 22,
        'GI' => 23,
        'GL' => 18,
        'GR' => 27,
        'GT' => 28,
        'HR' => 21,
        'HU' => 28,
        'IE' => 22,
        'IL' => 23,
        'IQ' => 23,
        'IS' => 26,
        'IT' => 27,
        'JO' => 30,
        'KW' => 30,
        'KZ' => 20,
        'LB' => 28,
        'LC' => 32,
        'LI' => 21,
        'LT' => 20,
        'LU' => 20,
        'LV' => 21,
        'LY' => 25,
        'MC' => 27,
        'MD' => 24,
        'ME' => 22,
        'MK' => 19,
        'MN' => 20,
        'MR' => 27,
        'MT' => 31,
        'MU' => 30,
        'NI' => 28,
        'NL' => 18,
        'NO' => 15,
        'OM' => 23,
        'PK' => 24,
        'PL' => 28,
        'PS' => 29,
        'PT' => 25,
        'QA' => 29,
        'RO' => 24,
        'RS' => 22,
        'RU' => 33,
        'SA' => 24,
        'SC' => 31,
        'SD' => 18,
        'SE' => 24,
        'SI' => 19,
        'SK' => 24,
        'SM' => 27,
        'SO' => 23,
        'ST' => 25,
        'SV' => 28,
        'TL' => 23,
        'TN' => 24,
        'TR' => 26,
        'UA' => 29,
        'VA' => 22,
        'VG' => 24,
        'XK' => 20,
    ];

    public function __invoke(string $value, Iban $constraint): void
    {
        $message = 'Value must be a valid IBAN';

        $country = substr($value, 0, 2);
        $length = self::LENGTH[$country] ?? null;
        if (strlen($value) !== $length) {
            throw new ValueRuleViolation($message);
        }

        // move first 4 chars (country code and check digits) to the end
        $value = substr($value, 4) . substr($value, 0, 4);

        $letters = range('A', 'Z');
        $replacements = array_combine($letters, range(10, 35));
        $value = strtr($value, $replacements);

        // modulus 97 check
        $mod = self::mod($value, '97');

        if ($mod !== '1') {
            throw new ValueRuleViolation($message);
        }
    }

    /**
     * Large int modulus.
     */
    private static function mod(string $n1, string $n2): string
    {
        if (function_exists('bcmod')) {
            return bcmod($n1, $n2);
        }

        $take = 5;
        $mod = '';
        do {
            $a = (int) $mod . substr($n1, 0, $take);
            $n1 = substr($n1, $take);
            $mod = (string) ($a % $n2);
        } while ($n1 !== '');

        return $mod;
    }
}

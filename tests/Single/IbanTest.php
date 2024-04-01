<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use Generator;
use Norvica\Validation\Rule\Iban;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

/**
 * @see https://www.iban.com/structure
 */
final class IbanTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'formatted with spaces' => ['GB33 BUKB 2020 1555 5555 55'];
        yield 'with leading and trailing space' => [' GB33BUKB20201555555555 '];
        yield 'lowercase' => ['gb33bukb20201555555555'];
        yield 'AD' => ['AD1400080001001234567890'];
        yield 'AE' => ['AE460090000000123456789'];
        yield 'AL' => ['AL35202111090000000001234567'];
        yield 'AT' => ['AT483200000012345864'];
        yield 'AZ' => ['AZ77VTBA00000000001234567890'];
        yield 'BA' => ['BA393385804800211234'];
        yield 'BE' => ['BE71096123456769'];
        yield 'BG' => ['BG18RZBB91550123456789'];
        yield 'BH' => ['BH02CITI00001077181611'];
        yield 'BI' => ['BI1320001100010000123456789'];
        yield 'BR' => ['BR1500000000000010932840814P2'];
        yield 'BY' => ['BY86AKBB10100000002966000000'];
        yield 'CH' => ['CH5604835012345678009'];
        yield 'CR' => ['CR23015108410026012345'];
        yield 'CY' => ['CY21002001950000357001234567'];
        yield 'CZ' => ['CZ5508000000001234567899'];
        yield 'DE' => ['DE75512108001245126199'];
        yield 'DJ' => ['DJ2110002010010409943020008'];
        yield 'DK' => ['DK9520000123456789'];
        yield 'DO' => ['DO22ACAU00000000000123456789'];
        yield 'EE' => ['EE471000001020145685'];
        yield 'EG' => ['EG800002000156789012345180002'];
        yield 'ES' => ['ES7921000813610123456789'];
        yield 'FI' => ['FI1410093000123458'];
        yield 'FK' => ['FK12SC987654321098'];
        yield 'FO' => ['FO9264600123456789'];
        yield 'FR' => ['FR7630006000011234567890189'];
        yield 'GB' => ['GB33BUKB20201555555555'];
        yield 'GE' => ['GE60NB0000000123456789'];
        yield 'GI' => ['GI56XAPO000001234567890'];
        yield 'GL' => ['GL8964710123456789'];
        yield 'GR' => ['GR9608100010000001234567890'];
        yield 'GT' => ['GT20AGRO00000000001234567890'];
        yield 'HR' => ['HR1723600001101234565'];
        yield 'HU' => ['HU93116000060000000012345676'];
        yield 'IE' => ['IE64IRCE92050112345678'];
        yield 'IL' => ['IL170108000000012612345'];
        yield 'IQ' => ['IQ20CBIQ861800101010500'];
        yield 'IS' => ['IS750001121234563108962099'];
        yield 'IT' => ['IT60X0542811101000000123456'];
        yield 'JO' => ['JO71CBJO0000000000001234567890'];
        yield 'KW' => ['KW81CBKU0000000000001234560101'];
        yield 'KZ' => ['KZ244350000012344567'];
        yield 'LB' => ['LB92000700000000123123456123'];
        yield 'LC' => ['LC14BOSL123456789012345678901234'];
        yield 'LI' => ['LI7408806123456789012'];
        yield 'LT' => ['LT601010012345678901'];
        yield 'LU' => ['LU120010001234567891'];
        yield 'LV' => ['LV97HABA0012345678910'];
        yield 'LY' => ['LY38021001000000123456789'];
        yield 'MC' => ['MC5810096180790123456789085'];
        yield 'MD' => ['MD21EX000000000001234567'];
        yield 'ME' => ['ME25505000012345678951'];
        yield 'MK' => ['MK07200002785123453'];
        yield 'MN' => ['MN580050099123456789'];
        yield 'MR' => ['MR1300020001010000123456753'];
        yield 'MT' => ['MT31MALT01100000000000000000123'];
        yield 'MU' => ['MU43BOMM0101123456789101000MUR'];
        yield 'NI' => ['NI79BAMC00000000000003123123'];
        yield 'NL' => ['NL02ABNA0123456789'];
        yield 'NO' => ['NO8330001234567'];
        yield 'OM' => ['OM040280000012345678901'];
        yield 'PK' => ['PK36SCBL0000001123456702'];
        yield 'PL' => ['PL10105000997603123456789123'];
        yield 'PS' => ['PS92PALS000000000400123456702'];
        yield 'PT' => ['PT50002700000001234567833'];
        yield 'QA' => ['QA54QNBA000000000000693123456'];
        yield 'RO' => ['RO66BACX0000001234567890'];
        yield 'RS' => ['RS35105008123123123173'];
        yield 'RU' => ['RU0204452560040702810412345678901'];
        yield 'SA' => ['SA4420000001234567891234'];
        yield 'SC' => ['SC74MCBL01031234567890123456USD'];
        yield 'SD' => ['SD8811123456789012'];
        yield 'SE' => ['SE7280000810340009783242'];
        yield 'SI' => ['SI56192001234567892'];
        yield 'SK' => ['SK8975000000000012345671'];
        yield 'SM' => ['SM76P0854009812123456789123'];
        yield 'SO' => ['SO061000001123123456789'];
        yield 'ST' => ['ST23000200000289355710148'];
        yield 'SV' => ['SV43ACAT00000000000000123123'];
        yield 'TL' => ['TL380010012345678910106'];
        yield 'TN' => ['TN5904018104004942712345'];
        yield 'TR' => ['TR320010009999901234567890'];
        yield 'UA' => ['UA903052992990004149123456789'];
        yield 'VA' => ['VA59001123000012345678'];
        yield 'VG' => ['VG07ABVI0000000123456789'];
        yield 'XK' => ['XK051212012345678906'];
    }

    #[DataProvider('valid')]
    public function testValid(string $value): void
    {
        $this->assertValid($value, new Iban());
    }

    public static function invalid(): Generator
    {
        yield 'invalid IBAN check digits MOD-97-10 as per ISO/IEC 7064:2003' => ['GB94BARC20201530093459'];
        yield 'invalid IBAN length' => ['GB96BARC202015300934591'];
        yield 'invalid checksum' => ['GB96BARC302015300934591'];
        yield 'invalid country' => ['US64SVBKUS6S3300958879'];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value): void
    {
        $this->assertInvalid($value, new Iban());
    }
}

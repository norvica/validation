<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Single;

use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Norvica\Validation\Rule\DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Validation\ValidationTestCase;

final class DateTimeTest extends ValidationTestCase
{
    public static function valid(): Generator
    {
        yield 'before date' => [
            ' 1999-12-12 ',
            new DateTime(
                max: self::date('2000-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'before date inclusive' => [
            ' 2000-01-01 ',
            new DateTime(
                max: self::date('2000-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'after date inclusive' => [
            ' 2000-01-01 ',
            new DateTime(
                min: self::date('2000-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'after date' => [
            ' 2000-01-02 ',
            new DateTime(
                min: self::date('2000-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'in date range' => [
            ' 2010-01-01 ',
            new DateTime(
                min: self::date('2000-01-01'),
                max: self::date('2020-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'before time' => [
            ' 23:59:58 ',
            new DateTime(
                max: self::time('23:59:59'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'before time inclusive' => [
            ' 23:59:59 ',
            new DateTime(
                max: self::time('23:59:59'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'after time inclusive' => [
            ' 13:13:13 ',
            new DateTime(
                min: self::time('13:13:13'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'after time' => [
            ' 13:13:14 ',
            new DateTime(
                min: self::time('13:13:13'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'in time range' => [
            ' 13:13:13 ',
            new DateTime(
                min: self::time('13:13:10'),
                max: self::time('13:13:15'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'custom format (Y) before' => [
            ' 1999 ',
            new DateTime(
                max: self::year('2000'),
                format: 'Y',
            ),
        ];

        yield 'custom format (Y) before inclusive' => [
            ' 2000 ',
            new DateTime(
                max: self::year('2000'),
                format: 'Y',
            ),
        ];

        yield 'custom format (Y) after inclusive' => [
            ' 2000 ',
            new DateTime(
                min: self::year('2000'),
                format: 'Y',
            ),
        ];

        yield 'custom format (Y) after' => [
            ' 2001 ',
            new DateTime(
                min: self::year('2000'),
                format: 'Y',
            ),
        ];

        yield 'custom format (Y-m) before' => [
            ' 2000-02 ',
            new DateTime(
                max: self::month('2000-03'),
                format: 'Y-m',
            ),
        ];

        yield 'custom format (Y-m) before inclusive' => [
            ' 2000-03 ',
            new DateTime(
                max: self::month('2000-03'),
                format: 'Y-m',
            ),
        ];

        yield 'custom format (Y-m) after inclusive' => [
            ' 2000-04 ',
            new DateTime(
                min: self::month('2000-04'),
                format: 'Y-m',
            ),
        ];

        yield 'custom format (Y-m) after' => [
            ' 2000-05 ',
            new DateTime(
                min: self::month('2000-04'),
                format: 'Y-m',
            ),
        ];

        yield 'custom format (H) midnight' => [
            ' 00 ',
            new DateTime(
                format: 'H',
            ),
        ];

        yield 'custom format (H) before' => [
            ' 20 ',
            new DateTime(
                max: self::hour('21'),
                format: 'H',
            ),
        ];

        yield 'custom format (H) before inclusive' => [
            ' 21 ',
            new DateTime(
                max: self::hour('21'),
                format: 'H',
            ),
        ];

        yield 'custom format (H) after inclusive' => [
            ' 21 ',
            new DateTime(
                min: self::hour('21'),
                format: 'H',
            ),
        ];

        yield 'custom format (H) after' => [
            ' 22 ',
            new DateTime(
                min: self::hour('21'),
                format: 'H',
            ),
        ];

        yield 'custom format (H:i) midnight' => [
            ' 00:00 ',
            new DateTime(
                format: 'H:i',
            ),
        ];

        yield 'custom format (H:i) before' => [
            ' 20:59 ',
            new DateTime(
                max: self::minute('21:00'),
                format: 'H:i',
            ),
        ];

        yield 'custom format (H:i) before inclusive' => [
            ' 21:00 ',
            new DateTime(
                max: self::minute('21:00'),
                format: 'H:i',
            ),
        ];

        yield 'custom format (H:i) after inclusive' => [
            ' 21:00 ',
            new DateTime(
                min: self::minute('21:00'),
                format: 'H:i',
            ),
        ];

        yield 'custom format (H:i) after' => [
            ' 21:01 ',
            new DateTime(
                min: self::minute('21:00'),
                format: 'H:i',
            ),
        ];

        $birthdate = new DateTimeImmutable('-18 years');
        yield 'birthdate (age) check' => [
            $birthdate->format(DateTime::ISO8601_DATE),
            new DateTime(
                max: new DateTimeImmutable('-18 years'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'timezone identifier in format' => [
            (new DateTimeImmutable(timezone: new DateTimeZone('America/Caracas')))->format('Y-m-d\TH:i:s.ue'),
            new DateTime(
                format: 'Y-m-d\TH:i:s.ue',
            ),
        ];

        yield 'timezone abbreviation in format' => [
            (new DateTimeImmutable(timezone: new DateTimeZone('Australia/Adelaide')))->format('Y-m-d\TH:i:s.uT'),
            new DateTime(
                format: 'Y-m-d\TH:i:s.uT',
            ),
        ];
    }

    #[DataProvider('valid')]
    public function testValid(string $value, DateTime $rule): void
    {
        $this->assertValid($value, $rule);
    }

    public static function invalid(): Generator
    {
        yield 'date with redundant data' => [
            '1999-12-12 23:59:59',
            new DateTime(
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'date with insufficient data' => [
            '1999-12',
            new DateTime(
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'invalid date' => [
            '1999-12-32',
            new DateTime(
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'before date' => [
            '2000-01-02',
            new DateTime(
                max: self::date('2000-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'after date' => [
            '1999-12-12',
            new DateTime(
                min: self::date('2000-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'in date range' => [
            '2020-01-01',
            new DateTime(
                min: self::date('2000-01-01'),
                max: self::date('2010-01-01'),
                format: DateTime::ISO8601_DATE,
            ),
        ];

        yield 'before time' => [
            '12:00:01',
            new DateTime(
                max: self::time('12:00:00'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'after time' => [
            '13:13:12',
            new DateTime(
                min: self::time('13:13:13'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'in time range' => [
            ' 13:13:09 ',
            new DateTime(
                min: self::time('13:13:10'),
                max: self::time('13:13:15'),
                format: DateTime::ISO8601_TIME,
            ),
        ];

        yield 'custom format (Y) invalid year' => [
            '90',
            new DateTime(
                format: 'Y',
            ),
        ];

        yield 'custom format (Y) before' => [
            '2001',
            new DateTime(
                max: self::year('2000'),
                format: 'Y',
            ),
        ];

        yield 'custom format (Y) after' => [
            '2000',
            new DateTime(
                min: self::year('2001'),
                format: 'Y',
            ),
        ];

        yield 'custom format (Y-m) invalid month' => [
            '2000-13',
            new DateTime(
                format: 'Y-m',
            ),
        ];

        yield 'custom format (Y-m) zero month' => [
            '2000-00',
            new DateTime(
                format: 'Y-m',
            ),
        ];

        yield 'custom format (Y-m) before' => [
            '2000-03',
            new DateTime(
                max: self::month('2000-02'),
                format: 'Y-m',
            ),
        ];

        yield 'custom format (Y-m) after' => [
            '2000-09',
            new DateTime(
                min: self::month('2000-10'),
                format: 'Y-m',
            ),
        ];

        yield 'custom format (H) invalid' => [
            '24',
            new DateTime(
                format: 'H',
            ),
        ];

        yield 'custom format (H) before' => [
            '22',
            new DateTime(
                max: self::hour('21'),
                format: 'H',
            ),
        ];

        yield 'custom format (H) after' => [
            '20',
            new DateTime(
                min: self::hour('21'),
                format: 'H',
            ),
        ];

        yield 'custom format (H:i) invalid' => [
            '00:60',
            new DateTime(
                format: 'H:i',
            ),
        ];

        yield 'custom format (H:i) before' => [
            '21:01',
            new DateTime(
                max: self::minute('21:00'),
                format: 'H:i',
            ),
        ];

        yield 'custom format (H:i) after' => [
            '20:59',
            new DateTime(
                min: self::minute('21:00'),
                format: 'H:i',
            ),
        ];

        $birthdate = new DateTimeImmutable('-18 years');
        yield 'birthdate (age) check' => [
            $birthdate
                ->setDate(
                    (int) $birthdate->format('Y'),
                    (int) $birthdate->format('m'),
                    ((int) $birthdate->format('d')) + 1,
                )
                ->format(DateTime::ISO8601_DATE),
            new DateTime(
                max: new DateTimeImmutable('-18 years'),
                format: DateTime::ISO8601_DATE,
            ),
        ];
    }

    #[DataProvider('invalid')]
    public function testInvalid(string $value, DateTime $rule): void
    {
        $this->assertInvalid($value, $rule);
    }

    private static function date(string $value): DateTimeImmutable
    {
        return \Norvica\Validation\Normalizer\DateTime::fromFormat($value, DateTime::ISO8601_DATE);
    }

    private static function time(string $value): DateTimeImmutable
    {
        return \Norvica\Validation\Normalizer\DateTime::fromFormat($value, DateTime::ISO8601_TIME);
    }

    private static function year(string $value): DateTimeImmutable
    {
        return \Norvica\Validation\Normalizer\DateTime::fromFormat($value, 'Y');
    }

    private static function month(string $value): DateTimeImmutable
    {
        return \Norvica\Validation\Normalizer\DateTime::fromFormat($value, 'Y-m');
    }

    private static function hour(string $value): DateTimeImmutable
    {
        return \Norvica\Validation\Normalizer\DateTime::fromFormat($value, 'H');
    }

    private static function minute(string $value): DateTimeImmutable
    {
        return \Norvica\Validation\Normalizer\DateTime::fromFormat($value, 'H:i');
    }
}

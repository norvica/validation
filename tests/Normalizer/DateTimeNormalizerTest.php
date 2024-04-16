<?php

declare(strict_types=1);

namespace Tests\Norvica\Validation\Normalizer;

use DateTimeImmutable;
use Generator;
use Norvica\Validation\Normalizer\DateTime as DateTimeNormalizer;
use Norvica\Validation\Rule\DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DateTimeNormalizerTest extends TestCase
{
    public static function data(): Generator
    {
        yield 'Y' => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:00'),
            'Y',
            '2014-01-01T00:00:00.000000+00:00',
        ];

        yield 'Y-m' => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:00'),
            'Y-m',
            '2014-04-01T00:00:00.000000+00:00',
        ];

        yield DateTime::ISO8601_DATE => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:00'),
            DateTime::ISO8601_DATE,
            '2014-04-06T00:00:00.000000+00:00',
        ];

        yield 'H' => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:00'),
            'H',
            '1970-01-01T15:00:00.000000+00:00',
        ];

        yield 'H:i' => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:00'),
            'H:i',
            '1970-01-01T15:05:00.000000+00:00',
        ];

        yield DateTime::ISO8601_TIME => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:00'),
            DateTime::ISO8601_TIME,
            '1970-01-01T15:05:45.000000+00:00',
        ];

        yield 'H:i:s.vP' => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188+00:30'),
            'H:i:s.vP',
            '1970-01-01T15:05:45.844000+00:30',
        ];

        yield 'H:i:s.uP' => [
            DateTimeImmutable::createFromFormat(DateTime::ISO8601_WITH_MICROSECONDS, '2014-04-06T15:05:45.844188-01:00'),
            'H:i:s.uP',
            '1970-01-01T15:05:45.844188-01:00',
        ];
    }

    #[DataProvider('data')]
    public function testToFormat(DateTimeImmutable $value, string $format, string $expectation): void
    {
        $processed = DateTimeNormalizer::toFormat($value, $format);

        $this->assertEquals($expectation, $processed->format(DateTime::ISO8601_WITH_MICROSECONDS));
    }
}

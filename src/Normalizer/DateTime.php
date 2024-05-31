<?php

declare(strict_types=1);

namespace Norvica\Validation\Normalizer;

use DateTimeImmutable;
use Norvica\Validation\Exception\NormalizationException;

final readonly class DateTime
{
    public function __construct(
        public string|null $format = null,
    ) {
    }

    public function __invoke(string $value): DateTimeImmutable
    {
        return self::fromFormat($value, $this->format);
    }

    /**
     * @internal
     */
    public static function fromFormat(string $value, string $format): DateTimeImmutable
    {
        $parsed = date_parse_from_format($format, $value);
        if ($parsed['warning_count'] > 0 || $parsed['error_count'] > 0) {
            throw new NormalizationException("Value must match the format '{$format}'");
        }

        if ($parsed['year'] !== false && $parsed['year'] < 1000) {
            throw new NormalizationException("Value must be a valid date/time.");
        }

        if ($parsed['month'] !== false && ($parsed['month'] < 1 || $parsed['month'] > 12)) {
            throw new NormalizationException("Value must be a valid date/time.");
        }

        if (!$parsed['is_localtime']) {
            $offset = ['P', '+00:00'];
        } else {
            $offset = match ($parsed['zone_type']) {
                1 => ['P', sprintf(
                    '%s%02d:%02d',
                    ($parsed['zone'] >= 0 ? '+' : '-'),
                    abs((int) ($parsed['zone'] / 3600)),
                    abs(($parsed['zone'] % 3600) / 60),
                )],
                2 => ['T', $parsed['tz_abbr']],
                3 => ['e', $parsed['tz_id']],
                default => throw new NormalizationException("Unknown timezone type '{$parsed['zone']}'."),
            };
        }

        $ts = sprintf(
            '%04d-%02d-%02dT%02d:%02d:%02d.%s%s',
            $parsed['year'] ?: 1970,
            $parsed['month'] ?: 1,
            $parsed['day'] ?: 1,
            $parsed['hour'] ?: 0,
            $parsed['minute'] ?: 0,
            $parsed['second'] ?: 0,
            substr(number_format($parsed['fraction'] ?: 0.0, 6), 2),
            $offset[1],
        );

        $datetime = DateTimeImmutable::createFromFormat(
            $f = 'Y-m-d\TH:i:s.u' . $offset[0],
            $ts,
        );

        if ($datetime === false) {
            throw new NormalizationException("Failed to instantiate \DateTimeImmutable with '{$ts}' using format '{$f}'.");
        }

        return $datetime;
    }

    /**
     * Ensures consistent formatting of date-like values (e.g., "+1 year") by removing unnecessary time components for reliable comparison.
     * Example: If today is 2024-04-15, "+1 year" would be normalized to "2025-04-15 00:00:00", not "2025-04-15 11:32:48" (which could vary based on execution time).
     *
     * @internal
     */
    public static function toFormat(DateTimeImmutable $value, string $format): DateTimeImmutable
    {
        return self::fromFormat($value->format($format), $format);
    }
}

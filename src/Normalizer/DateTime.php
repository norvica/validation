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
        $datetime = self::fromFormat($value, $this->format);

        return self::toFormat($datetime, $this->format);
    }

    /**
     * @internal
     */
    public static function fromFormat(string $value, string $format): DateTimeImmutable
    {
        if (false === $datetime = DateTimeImmutable::createFromFormat($format, $value)) {
            throw new NormalizationException("Value must match the format '{$format}'");
        }

        // PHP can handle values outside typical ranges (e.g., the 13th month, 30 days in February) by adjusting them,
        // which may lead to unexpected results.
        // For instance '2024-13-15' would become '2025-01-15', and '2024-02-30' would become '2024-03-02'.
        // This check ensures the provided value aligns with the expected date format and catches such adjustments.
        if ($value !== $datetime->format($format)) {
            throw new NormalizationException("Value must be a valid date/time within the specified format '{$format}'");
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
        preg_match_all(
            '/(?<year>[YyXx])|(?<month>[FmMn])|(?<day>[dDjl])|(?<hour>[HhGg])|(?<minute>i)|(?<second>s)|(?<millisecond>v)|(?<microsecond>u)/',
            $format,
            $matches,
        );

        $matches = array_filter(array_map(static fn($item) => array_filter($item), $matches));
        $year = isset($matches['year']) ? $value->format('Y') : '1970';
        $month = isset($matches['month']) ? $value->format('m') : '01';
        $day = isset($matches['day']) ? $value->format('d') : '01';
        $hour = isset($matches['hour']) ? $value->format('H') : '00';
        $minute = isset($matches['minute']) ? $value->format('i') : '00';
        $second = isset($matches['second']) ? $value->format('s') : '00';
        $offset = $value->format('P');

        if (isset($matches['millisecond'])) {
            $microsecond = $value->format('v') . '000';
        } elseif (isset($matches['microsecond'])) {
            $microsecond = $value->format('u');
        } else {
            $microsecond = '000000';
        }

        return DateTimeImmutable::createFromFormat(
            \Norvica\Validation\Rule\DateTime::ISO8601_WITH_MICROSECONDS,
            "{$year}-{$month}-{$day}T{$hour}:{$minute}:{$second}.{$microsecond}{$offset}",
        );
    }
}

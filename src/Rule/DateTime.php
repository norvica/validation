<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use DateTimeImmutable;
use Norvica\Validation\Normalizer\DateTime as DateTimeNormalizer;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\DateTimeValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DateTime implements Rule, Normalizable
{
    public const ISO8601 = DATE_ATOM;
    public const ISO8601_DATE = 'Y-m-d';
    public const ISO8601_TIME = 'H:i:s';
    public const ISO8601_WITH_MILLISECONDS = 'Y-m-d\TH:i:s.vP';
    public const ISO8601_WITH_MICROSECONDS = 'Y-m-d\TH:i:s.uP';

    public DateTimeImmutable|null $min;
    public DateTimeImmutable|null $max;
    public string $format;

    public function __construct(
        DateTimeImmutable|null $min = null,
        DateTimeImmutable|null $max = null,
        string $format = self::ISO8601,
    ) {
        $this->min = $min !== null ? DateTimeNormalizer::toFormat($min, $format) : null;
        $this->max = $max !== null ? DateTimeNormalizer::toFormat($max, $format) : null;
        $this->format = $format;
    }

    #[Override]
    public function normalizers(): array
    {
        return [
            new Trim(),
            new DateTimeNormalizer($this->format),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return DateTimeValidation::class;
    }
}

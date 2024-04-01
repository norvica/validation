<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Lower;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\HostnameValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Hostname implements Rule, Normalizable
{
    /**
     * @param string[] $hosts List of allowed hosts.
     * @param bool $dns Perform DNS record check.
     * @param bool $reserved Allow reserved TLDs.
     */
    public function __construct(
        public array|null $hosts = null,
        public bool $dns = false,
        public bool $reserved = false,
    ) {
    }

    #[Override]
    public static function normalizers(): array
    {
        return [
            new Trim(),
            new Lower(),
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return HostnameValidation::class;
    }
}

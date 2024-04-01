<?php

declare(strict_types=1);

namespace Norvica\Validation\Rule;

use Attribute;
use Norvica\Validation\Normalizer\Normalizable;
use Norvica\Validation\Normalizer\Trim;
use Norvica\Validation\Validation\UrlValidation;
use Override;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Url implements Rule, Normalizable
{
    /**
     * NOTICE: there's no support for IDN (internationalized domain names) or punycode domains.
     *
     * @param string[] $schemes Allowed schemes.
     * @param string[] $hosts List of allowed hosts.
     * @param bool $dns Perform DNS record check.
     * @param bool $reserved Allow reserved TLDs.
     */
    public function __construct(
        public array $schemes = ['http', 'https'],
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
        ];
    }

    #[Override]
    public static function validator(): string
    {
        return UrlValidation::class;
    }
}

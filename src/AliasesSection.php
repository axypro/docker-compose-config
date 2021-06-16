<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * "aliases" section for "service.networks"
 */
class AliasesSection extends BaseListSection
{
    /** {@inheritdoc} */
    protected bool $canShort = false;
}

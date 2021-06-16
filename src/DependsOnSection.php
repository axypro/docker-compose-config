<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * "depends_on" section of "service"
 */
class DependsOnSection extends BaseListSection
{
    protected bool $canShort = false;
}

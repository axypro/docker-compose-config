<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * Trait of elements that can be disabled.
 * Disabled element is not shown in the config.
 */
trait TDisable
{
    public function disable(): void
    {
        $this->amIEnabled = false;
    }

    public function enable(): void
    {
        $this->amIEnabled = true;
    }

    public function isEnabled(): bool
    {
        return $this->amIEnabled;
    }

    protected bool $amIEnabled = true;
}

<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

interface IComposeElement
{
    /**
     * @return array|string|null
     *         NULL - don't show this section
     */
    public function getData(): array|string|null;
}

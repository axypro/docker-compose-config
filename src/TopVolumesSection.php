<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

use ArrayAccess;

/**
 * The top "volumes" section
 */
class TopVolumesSection implements IComposeElement, ArrayAccess
{
    public function __construct(mixed $data)
    {
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $this->create((string)$name, $value);
            }
        }
    }

    public function create(string $name, mixed $data = null): TopVolume
    {
        $volume = new TopVolume($data);
        $this->volumes[$name] = $volume;
        return $volume;
    }

    public function clear(): void
    {
        $this->volumes = [];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->volumes[$offset]);
    }

    public function offsetGet($offset): ?TopVolume
    {
        return $this->volumes[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($value instanceof TopVolume) {
            $this->volumes[$offset] = $value;
        } else {
            $this->create($offset, $value);
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->volumes[$offset]);
    }

    public function getData(): array|string|null
    {
        $data = [];
        foreach ($this->volumes as $name => $volume) {
            $dv = $volume->getData();
            if ($dv !== null) {
                $data[$name] = $dv;
            }
        }
        return empty($data) ? null : $data;
    }

    /** @var TopVolume[] */
    private array $volumes = [];
}

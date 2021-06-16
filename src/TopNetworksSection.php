<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

use ArrayAccess;

/**
 * The top "networks" section
 */
class TopNetworksSection implements IComposeElement, ArrayAccess
{
    public function __construct(mixed $data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->create($k, $v);
            }
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        $data = [];
        foreach ($this->networks as $name => $network) {
            $network = $network->getData();
            if (is_array($network)) {
                $data[$name] = $network;
            }
        }
        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function create(string $name, mixed $data = null): TopNetwork
    {
        $network = new TopNetwork($data);
        $this->networks[$name] = $network;
        return $network;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->networks[$offset]);
    }

    public function offsetGet($offset): ?TopNetwork
    {
        return $this->networks[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($value instanceof TopNetwork) {
            $this->networks[$offset] = $value;
        } else {
            $this->create($offset, $value);
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->networks[$offset]);
    }

    /** @var TopNetwork[] */
    private array $networks = [];
}

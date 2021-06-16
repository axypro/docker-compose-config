<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

use ArrayAccess;

/**
 * "networks" section of "service"
 */
class ServiceNetworksSection implements IComposeElement, ArrayAccess
{
    public function __construct(mixed $data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_int($k)) {
                    $this->join((string)$v, []);
                } else {
                    $this->join((string)$k, $v);
                }
            }
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        $result = [];
        foreach ($this->networks as $name => $network) {
            $value = $network->getData();
            if ($value !== null) {
                $result[$name] = $value;
            }
        }
        return $result;
    }


    /**
     * Joins to a network
     *
     * @param string $name
     * @param mixed $data
     * @return ServiceNetwork
     */
    public function join(string $name, mixed $data): ServiceNetwork
    {
        $network = new ServiceNetwork($data);
        $this->networks[$name] = $network;
        return $network;
    }

    public function clear(): void
    {
        $this->networks = [];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->networks[$offset]);
    }

    public function offsetGet($offset): ?ServiceNetwork
    {
        return $this->networks[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->join((string)$offset, $value);
    }

    public function offsetUnset($offset): void
    {
        unset($this->networks[$offset]);
    }

    /** @var ServiceNetwork[] */
    private array $networks = [];
}

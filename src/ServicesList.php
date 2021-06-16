<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

use ArrayAccess;
use LogicException;

/**
 * Section "services" on the top level
 */
class ServicesList implements IComposeElement, ArrayAccess
{
    public function __construct(mixed $data)
    {
        if (!is_array($data)) {
            return;
        }
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $this->create($k, $v);
            }
        }
    }

    /** {@inheritdoc} */
    public function getData(): ?array
    {
        $result = [];
        foreach ($this->services as $name => $service) {
            $data = $service->getData();
            if ($data !== null) {
                $result[$name] = $data;
            }
        }
        return empty($result) ? null : $result;
    }

    /**
     * Creates a service
     *
     * @param string $name
     * @param mixed|null $data
     * @return ComposeService
     */
    public function create(string $name, mixed $data = null): ComposeService
    {
        $service = new ComposeService($data);
        $this->services[$name] = $service;
        return $service;
    }

    /**
     * Disables a service
     *
     * @param string $name
     */
    public function disableService(string $name): void
    {
        if (isset($this->services[$name])) {
            $this->services[$name]->disable();
        }
    }

    /**
     * Clears the service list
     */
    public function clear(): void
    {
        $this->services = [];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->services[$offset]);
    }

    public function offsetGet($offset): ?ComposeService
    {
        return $this->services[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($value instanceof ComposeService) {
            $this->services[$offset] = $value;
        } else {
            $this->services[$offset] = $this->create($value);
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->services[$offset]);
    }

    /** @var ComposeService[] */
    private array $services = [];
}

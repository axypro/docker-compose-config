<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * "ports" section of "service"
 */
class PortsSection implements IComposeElement
{
    /** @var MappedPort[] */
    public array $ports = [];

    public function __construct(mixed $data)
    {
        if (is_string($data)) {
            $data = [$data];
        }
        if (!is_array($data)) {
            return;
        }
        foreach ($data as $item) {
            $this->bind($item);
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        $result = [];
        foreach ($this->ports as $port) {
            if ($port === null) {
                continue;
            }
            if ($port instanceof IComposeElement) {
                $port = $port->getData();
                if ($port === null) {
                    continue;
                }
            }
            $result[] = (string)$port;
        }
        return $result;
    }

    /**
     * Binds a port
     *
     * @param mixed|null $data [optional]
     * @param string|null $key [optional]
     *        a key that will be associated with this port
     * @param string|null $group
     * @return MappedPort
     */
    public function bind(mixed $data = null, ?string $key = null, ?string $group = null): MappedPort
    {
        $port = new MappedPort($data);
        $this->ports[] = $port;
        if ($key !== null) {
            $this->byKey[$key] = $port;
        }
        if ($group !== null) {
            if (!isset($this->groups[$group])) {
                $this->groups[$group] = [];
            }
            $this->groups[$group][] = $port;
        }
        return $port;
    }

    /**
     * Returns a port by its key
     * The port can be disabled already
     *
     * @param string $key
     * @return MappedPort|null
     */
    public function getByKey(string $key): ?MappedPort
    {
        return $this->byKey[$key] ?? null;
    }

    /**
     * @param string $group
     * @return MappedPort[]
     */
    public function getGroup(string $group): array
    {
        return $this->groups[$group] ?? [];
    }

    /**
     * Unbinds a port by its key
     *
     * @param string $key
     */
    public function unbindByKey(string $key): void
    {
        $port = $this->getByKey($key);
        if ($port !== null) {
            $port->disable();
        }
    }

    public function unbindGroup(string $group): void
    {
        foreach ($this->getGroup($group) as $port) {
            $port->disable();
        }
    }

    /**
     * Unbind all ports
     */
    public function clear(): void
    {
        $this->ports = [];
    }

    /** @var MappedPort[] */
    private array $byKey = [];

    /** @var MappedPort[][] */
    private array $groups = [];
}

<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * Base class for section that contains unordered list of items (strings or integers) like "expose", "depends_on", etc
 */
abstract class BaseListSection implements IComposeElement
{
    /** Can section use short format for single element (string instead list) */
    protected bool $canShort = false;

    public function __construct(mixed $data)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
                $this->on($item);
            }
        } else {
            $this->on($data);
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        $this->items = array_filter($this->items);
        $items = array_keys($this->items);
        if (empty($this->items)) {
            return null;
        }
        if ($this->canShort && (count($items) === 1) && isset($items[0])) {
            return (string)$items[0];
        }
        return $items;
    }

    /**
     * Adds an item to the list and enables it
     *
     * @param string|int $item
     * @param string|null $key [optional]
     *        an unique key that will be associated with this item
     * @param string|null $group [optional]
     *        a group of this item
     */
    public function on(mixed $item, ?string $key = null, ?string $group = null): void
    {
        if (is_string($item) || is_int($item)) {
            $this->items[$item] = true;
            if ($key !== null) {
                $this->byKey[$key] = $item;
            }
            if ($group !== null) {
                if (!isset($this->groups[$group])) {
                    $this->groups[$group] = [];
                }
                $this->groups[$group][] = $item;
            }
        }
    }

    /**
     * Removes an item from the list
     *
     * @param string|int $item
     */
    public function off(string|int $item): void
    {
        $this->items[$item] = false;
    }

    /**
     * Checks if an item is in the list
     *
     * @param string|int $item
     * @return bool
     */
    public function isOn(string|int $item): bool
    {
        return $this->items[$item] ?? false;
    }

    /**
     * Returns an item by the associated key
     * Item will be returned even if it was removed
     *
     * @param string $key
     * @return string|int|null
     */
    public function getByKey(string $key): string|int|null
    {
        return $this->byKey[$key] ?? null;
    }

    public function getGroup(string $group): array
    {
        return $this->groups[$group] ?? [];
    }

    public function keyOff(string $key): void
    {
        $item = $this->byKey[$key] ?? null;
        if ($item !== null) {
            $this->items[$item] = false;
        }
    }

    public function keyOn(string $key): void
    {
        $item = $this->byKey[$key] ?? null;
        if ($item !== null) {
            $this->items[$item] = true;
        }
    }

    public function groupOff(string $group): void
    {
        foreach ($this->getGroup($group) as $item) {
            $this->off($item);
        }
    }

    public function groupOn(string $group): void
    {
        foreach ($this->getGroup($group) as $item) {
            $this->on($item);
        }
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /** @var bool[] */
    private array $items = [];

    /** @var array */
    private array $byKey = [];

    /**
     * @var array[]
     */
    private array $groups = [];
}

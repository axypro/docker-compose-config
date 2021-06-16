<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

use ArrayAccess;

/**
 * Base class for key-value sections like "args", "environment", "labels"
 */
class BaseKVSection implements IComposeElement, ArrayAccess
{
    public function __construct(mixed $values)
    {
        $this->loadValues($values);
    }

    public function getData(): ?array
    {
        return empty($this->values) ? null : $this->values;
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = (string)$value;
    }

    public function offsetUnset($offset)
    {
        unset($this->values);
    }

    private function loadValues(mixed $values): void
    {
        if (!is_array($values)) {
            if (is_string($values)) {
                $this->loadString($values);
            }
            return;
        }
        foreach ($values as $k => $value) {
            if (is_int($k)) {
                $this->loadString((string)$value);
            } else {
                $this->values[$k] = $value;
            }
        }
    }

    private function loadString(string $value): void
    {
        $kv = explode('=', $value, 2);
        $this->values[$kv[0]] = $kv[1] ?? '';
    }

    /** @var string[] */
    private array $values = [];
}

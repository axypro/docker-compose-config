<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * A network settings.
 * An item of the top "networks" section
 */
class TopNetwork implements IComposeElement
{
    use TDisable;

    public ?string $driver = null;
    public array $driver_opts = [];
    public bool $attachable = false;
    public bool $internal = false;
    public bool $external = false;
    public ?string $name = null;
    public array $additional = [];

    public function __construct(mixed $data)
    {
        if (!is_array($data)) {
            return;
        }
        foreach ($this->fields as $k) {
            if (!array_key_exists($k, $data)) {
                continue;
            }
            $default = $this->$k;
            $v = $data[$k];
            unset($data[$k]);
            if (is_array($default)) {
                if (is_array($v)) {
                    $this->$k = $v;
                }
            } elseif (is_bool($default)) {
                $this->$k = !empty($v);
            } else {
                if (is_string($v)) {
                    $this->$k = $v;
                }
            }
        }
        $this->additional = $data;
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        if (!$this->amIEnabled) {
            return null;
        }
        $data = [];
        foreach ($this->fields as $k) {
            $v = $this->$k;
            if (($v !== null) && ($v !== false) && ($v !== '') && ($v !== [])) {
                $data[$k] = $v;
            }
        }
        return array_replace($data, $this->additional);
    }

    private array $fields = ['driver', 'driver_opts', 'attachable', 'internal', 'external', 'name'];
}

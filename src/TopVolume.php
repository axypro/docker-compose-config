<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * A named volume as an item of the top "volumes" section
 */
class TopVolume implements IComposeElement
{
    use TDisable;

    public ?string $driver = null;
    public array $driver_opts = [];
    public bool $external = false;
    public array $additional = [];

    public function __construct(mixed $data)
    {
        $this->driver = is_string($data['driver'] ?? null) ? $data['driver'] : null;
        $this->driver_opts = is_array($data['driver_opts'] ?? null) ? $data['driver_opts'] : [];
        $this->external = !empty($data['external']);
        if (is_array($data)) {
            unset($data['driver']);
            unset($data['driver_opts']);
            unset($data['external']);
            $this->additional = $data;
        }
    }

    public function getData(): ?array
    {
        if (!$this->amIEnabled) {
            return null;
        }
        $data = [];
        if ($this->driver !== null) {
            $data['driver'] = $this->driver;
        }
        if (!empty($this->driver_opts)) {
            $data['driver_opts'] = $this->driver_opts;
        }
        if ($this->external) {
            $data['external'] = true;
        }
        return array_replace($data, $this->additional);
    }
}

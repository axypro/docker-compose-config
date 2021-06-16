<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * A container port mapped to the host (an item of "service.ports" section)
 */
class MappedPort implements IComposeElement
{
    use TDisable;

    public string|int|null $target = null;
    public string|int|null $published = null;
    public ?string $protocol = null;

    public function __construct(mixed $data)
    {
        if (is_array($data)) {
            $target = $data['target'] ?? null;
            $published = $data['published'] ?? null;
            $protocol = $data['protocol'] ?? null;
            if (is_string($target) || is_int($target)) {
                $this->target = $target;
            }
            if (is_string($published) || is_int($published)) {
                $this->published = $published;
            }
            if (is_string($protocol)) {
                $this->protocol = $protocol;
            }
        } elseif (is_string($data)) {
            $data = explode('/', $data, 2);
            $this->protocol = $data[1] ?? null;
            $data = explode(':', $data[0], 3);
            if (count($data) >= 2) {
                $this->target = array_pop($data);
                $this->published = implode(':', $data);
            }
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        if (!$this->amIEnabled) {
            return null;
        }
        if ($this->target === null) {
            return null;
        }
        $result = "{$this->published}:{$this->target}";
        if ($this->protocol !== null) {
            $result = "$result/{$this->protocol}";
        }
        return $result;
    }
}

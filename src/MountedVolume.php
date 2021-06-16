<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * Mounted volume as an item of "service.volumes" section
 */
class MountedVolume implements IComposeElement
{
    use TDisable;

    public ?string $type = null;
    public ?string $source = null;
    public ?string $target = null;
    public bool $read_only = false;
    public array $bind = [
        'propagation' => false,
    ];
    public array $volume = [
        'nocopy' => false,
    ];
    public array $tmpfs = [
        'size' => null,
    ];

    public function __construct(mixed $data = null)
    {
        if (is_array($data)) {
            $this->parseArray($data);
        } elseif (is_string($data)) {
            $this->parseString($data);
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        if (!$this->amIEnabled) {
            return null;
        }
        if (($this->source === null) || ($this->target === null)) {
            return null;
        }
        $canShort = true;
        $data = [];
        if ($this->type !== null) {
            $data['type'] = $this->type;
            if (!in_array($this->type, ['volume', 'bind'])) {
                $canShort = false;
            }
        }
        $data['source'] = $this->source;
        $data['target'] = $this->target;
        if (!empty($this->read_only)) {
            $data['read_only'] = true;
        }
        if ($this->bind !== ['propagation' => false]) {
            $data['bind'] = $this->bind;
            $canShort = false;
        }
        if ($this->volume !== ['nocopy' => false]) {
            $data['volume'] = $this->volume;
            $canShort = false;
        }
        if ($this->tmpfs !== ['size' => null]) {
            $data['tmpfs'] = $this->tmpfs;
            $canShort = false;
        }
        if (!$canShort) {
            return $data;
        }
        $data = [
            $this->source,
            $this->target,
        ];
        if ($this->read_only) {
            $data[] = 'ro';
        }
        return implode(':', $data);
    }

    private function parseArray(array $data): void
    {
        $this->type = is_string($data['type'] ?? null) ? $data['type'] : null;
        $this->source = is_string($data['source'] ?? null) ? $data['source'] : null;
        $this->target = is_string($data['target'] ?? null) ? $data['target'] : null;
        $this->read_only = !empty($data['read_only']);
        $this->bind['propagation'] = !empty($data['bind']['propagation']);
        $this->volume['nocopy'] = !empty($data['volume']['nocopy']);
        $this->tmpfs['size'] = $data['tpmfs']['size'] ?? null;
    }

    private function parseString(string $data): void
    {
        $e = explode(':', $data, 3);
        $this->source = $e[0] ?? null;
        $this->target = $e[1] ?? null;
        $this->read_only = (($e[2] ?? null) === 'ro');
    }
}

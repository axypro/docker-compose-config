<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * "volumes" section of "service"
 */
class ServiceVolumesSection implements IComposeElement
{
    /** @var MountedVolume[] */
    public array $volumes = [];

    public function __construct(mixed $data = null)
    {
        if (is_array($data)) {
            foreach ($data as $v) {
                $this->mount($v);
            }
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        $data = [];
        foreach ($this->volumes as $volume) {
            $vd = $volume->getData();
            if ($vd !== null) {
                $data[] = $vd;
            }
        }
        return empty($data) ? null : $data;
    }

    /**
     * Mounts a volume
     *
     * @param mixed $data
     * @param string|null $key
     *        a key that will associated with the volume
     * @return MountedVolume
     */
    public function mount(mixed $data, ?string $key = null): MountedVolume
    {
        $volume = new MountedVolume($data);
        $this->volumes[] = $volume;
        if ($key !== null) {
            $this->byKey[$key] = $volume;
        }
        return $volume;
    }

    /**
     * Finds a bound volume by the source
     * Returns the first matching
     *
     * @param string $source
     * @return MountedVolume|null
     */
    public function findBySource(string $source): ?MountedVolume
    {
        foreach ($this->volumes as $volume) {
            if ($volume->source === $source) {
                return $volume;
            }
        }
        return null;
    }

    /**
     * Finds a bound volume by the source
     * Returns the first matching
     *
     * @param string $target
     * @return MountedVolume|null
     */
    public function findByTarget(string $target): ?MountedVolume
    {
        foreach ($this->volumes as $volume) {
            if ($volume->target === $target) {
                return $volume;
            }
        }
        return null;
    }

    /**
     * Returns a bound volume by its key
     *
     * @param string $key
     * @return MountedVolume|null
     */
    public function getByKey(string $key): ?MountedVolume
    {
        return $this->byKey[$key] ?? null;
    }

    /**
     * Unbinds all volumes
     */
    public function clear(): void
    {
        $this->volumes = [];
    }

    /** @var MountedVolume[]  */
    private array $byKey = [];
}

<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * "build" section of "service"
 */
class BuildSection implements IComposeElement
{
    public ?string $context = null;
    public ?string $dockerfile = null;
    public ArgsSection $args;
    public array $cache_from = [];
    public LabelsSection $labels;
    public ?string $network = null;
    public string|int|null $shm_size = null;
    public ?string $target = null;
    public array $additional = [];

    public function __construct(mixed $data = null)
    {
        $this->labels = new LabelsSection($data['labels'] ?? null);
        $this->args = new ArgsSection($data['args'] ?? null);
        if (!is_array($data)) {
            if (is_string($data)) {
                $this->context = $data;
            }
            return;
        }
        unset($data['labels']);
        unset($data['args']);
        foreach ($this->fields as $k) {
            if (!isset($data[$k])) {
                continue;
            }
            $default = $this->$k;
            $v = $data[$k];
            unset($data[$k]);
            $correct = is_array($v) ? is_array($default) : (!is_array($default));
            if ($correct) {
                $this->$k = $v;
            }
        }
        $this->additional = (array)$data;
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        if ($this->context === null) {
            return null;
        }
        $data = [];
        foreach ($this->fields as $k) {
            $v = $this->$k;
            if ($v instanceof IComposeElement) {
                $v = $v->getData();
            }
            if (($v !== null) && ($v !== [])) {
                $data[$k] = $v;
            }
        }
        $data = array_replace($data, $this->additional);
        if (count($data) <= 1) {
            return $this->context;
        }
        return $data;
    }

    /**
     * Remove this section from the parent service
     */
    public function disable(): void
    {
        $this->context = null;
    }

    private array $fields = ['context', 'dockerfile', 'args', 'cache_from', 'labels', 'network', 'shm_size', 'target'];
}

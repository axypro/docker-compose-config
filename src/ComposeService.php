<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * Single "service" section
 */
class ComposeService implements IComposeElement
{
    use TDisable;

    public ?string $image = null;
    public ?string $container_name = null;
    public ?string $restart = null;
    public BuildSection $build;
    public PortsSection $ports;
    public ExposeSection $expose;
    public ServiceVolumesSection $volumes;
    public EnvironmentSection $environment;
    public LabelsSection $labels;
    public ?string $network_mode = null;
    public ServiceNetworksSection $networks;
    public DependsOnSection $depends_on;
    public array $additional = [];

    public function __construct(mixed $data = null)
    {
        if (is_array($data)) {
            foreach (['image', 'container_name', 'restart', 'network_mode'] as $k) {
                $this->$k = is_string($data[$k] ?? null) ? $data[$k] : null;
                unset($data[$k]);
            }
        }
        $this->build = new BuildSection($data['build'] ?? null);
        $this->ports = new PortsSection($data['ports'] ?? null);
        $this->expose = new ExposeSection($data['expose'] ?? null);
        $this->environment = new EnvironmentSection($data['environment'] ?? null);
        $this->labels = new LabelsSection($data['labels'] ?? null);
        $this->networks = new ServiceNetworksSection($data['networks'] ?? null);
        $this->depends_on = new DependsOnSection($data['depends_on'] ?? null);
        $this->volumes = new ServiceVolumesSection($data['volumes'] ?? null);
        if (!is_array($data)) {
            return;
        }
        unset($data['build']);
        unset($data['ports']);
        unset($data['volumes']);
        unset($data['expose']);
        unset($data['environment']);
        unset($data['labels']);
        unset($data['networks']);
        unset($data['depends_on']);
        $this->additional = (array)$data;
    }

    /** {@inheritdoc} */
    public function getData(): ?array
    {
        if (!$this->amIEnabled) {
            return null;
        }
        $data = [];
        $keys = [
            'image',
            'container_name',
            'restart',
            'build',
            'ports',
            'expose',
            'volumes',
            'environment',
            'labels',
            'network_mode',
            'networks',
            'depends_on',
        ];
        foreach ($keys as $k) {
            $v = $this->$k;
            if ($v instanceof IComposeElement) {
                $v = $v->getData();
            }
            if (($v !== null) && ($v !== [])) {
                $data[$k] = $v;
            }
        }
        return array_replace($data, $this->additional);
    }
}

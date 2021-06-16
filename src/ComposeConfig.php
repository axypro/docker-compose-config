<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * Main class of compose config
 */
class ComposeConfig implements IComposeElement
{
    public ?string $version = null;
    public ServicesList $services;
    public TopVolumesSection $volumes;
    public TopNetworksSection $networks;
    public array $additional = [];

    public function __construct(?array $data = null)
    {
        $version = $data['version'] ?? null;
        if (is_string($version)) {
            $this->version = $version;
        }
        $this->services = new ServicesList($data['services'] ?? null);
        $this->volumes = new TopVolumesSection($data['volumes'] ?? null);
        $this->networks = new TopNetworksSection($data['networks'] ?? null);
        if (is_array($data)) {
            unset($data['version']);
            unset($data['services']);
            unset($data['volumes']);
            unset($data['networks']);
            $this->additional = (array)$data;
        }
    }

    /** {@inheritdoc} */
    public function getData(): array
    {
        $fields = [
            'version' => $this->version,
            'services' => $this->services->getData(),
            'volumes' => $this->volumes->getData(),
            'networks' => $this->networks->getData(),
        ];
        $result = [];
        foreach ($fields as $k => $v) {
            if ($v !== null) {
                $result[$k] = $v;
            }
        }
        return array_replace($result, $this->additional);
    }
}

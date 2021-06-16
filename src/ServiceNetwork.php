<?php

declare(strict_types=1);

namespace axy\docker\compose\config;

/**
 * An item of "service.networks" section
 */
class ServiceNetwork implements IComposeElement
{
    use TDisable;

    public AliasesSection $aliases;
    public array $additional = [];

    public function __construct(mixed $data)
    {
        $this->aliases = new AliasesSection($data['aliases'] ?? null);
        if (is_array($data)) {
            unset($data['aliases']);
            $this->additional = $data;
        }
    }

    /** {@inheritdoc} */
    public function getData(): array|string|null
    {
        if (!$this->amIEnabled) {
            return null;
        }
        $result = [];
        $aliases = $this->aliases->getData();
        if ($aliases !== null) {
            $result['aliases'] = $aliases;
        }
        return array_replace($result, $this->additional);
    }
}

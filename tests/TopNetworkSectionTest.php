<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\TopNetworksSection;

class TopNetworkSectionTest extends BaseTestCase
{
    public function testNetwork(): void
    {
        $networks = new TopNetworksSection([
            'first' => [],
            'second' => [
                'driver' => 'bridge',
                'driver_opts' => [
                    'foo' => 'bar',
                ],
                'attachable' => true,
                'x' => 'y',
            ],
        ]);
        $networks['first']->disable();
        $networks['second']->attachable = false;
        $networks->create('third', [
            'external' => true,
        ]);
        $this->assertEquals([
            'second' => [
                'driver' => 'bridge',
                'driver_opts' => [
                    'foo' => 'bar',
                ],
                'x' => 'y',
            ],
            'third' => [
                'external' => true,
            ],
        ], $networks->getData());
    }
}

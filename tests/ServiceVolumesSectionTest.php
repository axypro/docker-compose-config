<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\ServiceVolumesSection;

class ServiceVolumesSectionTest extends BaseTestCase
{
    public function testService(): void
    {
        $volumes = new ServiceVolumesSection([
            './www:/var/www:ro',
            './log:/var/log',
            [
                'type' => 'volume',
                'source' => 'volume',
                'target' => '/var/data',
                'read_only' => true,
                'volume' => [
                    'nocopy' => true,
                ],
            ],
        ]);
        $this->assertSame('./www', $volumes->volumes[0]->source);
        $volumes->volumes[1]->bind['propagation'] = true;
        $volumes->volumes[2]->volume['nocopy'] = false;
        $volumes->mount('./one:/two')->read_only = true;
        $volumes->mount(null);
        $this->assertEquals([
            './www:/var/www:ro',
            [
                'source' => './log',
                'target' => '/var/log',
                'bind' => [
                    'propagation' => true,
                ],
            ],
            'volume:/var/data:ro',
            './one:/two:ro',
        ], $volumes->getData());
    }

    public function testKey(): void
    {
        $volumes = new ServiceVolumesSection();
        $volumes->mount('one:/one');
        $volumes->mount('two:/two', 'two');
        $this->assertNull($volumes->getByKey('one'));
        $volumes->getByKey('two')->read_only = true;
        $this->assertEquals([
            'one:/one',
            'two:/two:ro',
        ], $volumes->getData());
    }
}

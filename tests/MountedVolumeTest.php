<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\MountedVolume;

class MountedVolumeTest extends BaseTestCase
{
    public function testVolume(): void
    {
        $volume = new MountedVolume('volume:/var/data:ro');
        $this->assertSame('volume', $volume->source);
        $this->assertSame('/var/data', $volume->target);
        $this->assertTrue($volume->read_only);
        $this->assertSame('volume:/var/data:ro', $volume->getData());
        $volume->read_only = false;
        $this->assertSame('volume:/var/data', $volume->getData());
        $volume->bind['propagation'] = true;
        $this->assertEquals([
            'source' => 'volume',
            'target' => '/var/data',
            'bind' => [
                'propagation' => true,
            ],
        ], $volume->getData());
        $volume->bind['propagation'] = false;
        $volume->read_only = true;
        $volume->volume['nocopy'] = true;
        $this->assertEquals([
            'source' => 'volume',
            'target' => '/var/data',
            'read_only' => true,
            'volume' => [
                'nocopy' => true,
            ],
        ], $volume->getData());
    }
}

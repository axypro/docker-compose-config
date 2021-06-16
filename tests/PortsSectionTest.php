<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\PortsSection;

class PortsSectionTest extends BaseTestCase
{
    public function testPorts(): void
    {
        $ports = new PortsSection([
            '8080:80',
        ]);
        $this->assertEquals([
            '8080:80',
        ], $ports->getData());

        $ports->bind('8081:81', 'one', 'first');
        $ports->bind('8082:82', null, 'first');
        $ports->bind('8083:83', null, 'second');
        $ports->bind('8084:84', null, 'second');
        $ports->getByKey('one')->protocol = 'udp';

        $this->assertEquals([
            '8080:80',
            '8081:81/udp',
            '8082:82',
            '8083:83',
            '8084:84',
        ], $ports->getData());
        $ports->unbindByKey('one');
        $ports->unbindByKey('two');
        $this->assertEquals([
            '8080:80',
            '8082:82',
            '8083:83',
            '8084:84',
        ], $ports->getData());

        $this->assertCount(2, $ports->getGroup('first'));
        $this->assertCount(2, $ports->getGroup('second'));
        $this->assertCount(0, $ports->getGroup('third'));

        $ports->unbindGroup('second');
        $ports->unbindGroup('third');
        $this->assertEquals([
            '8080:80',
            '8082:82',
        ], $ports->getData());

        $ports->unbindGroup('first');
        $this->assertEquals([
            '8080:80',
        ], $ports->getData());
    }
}

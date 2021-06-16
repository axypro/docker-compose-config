<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\MappedPort;

class MappedPortTest extends BaseTestCase
{
    public function testPort(): void
    {
        $port = new MappedPort('8000:80');
        $this->assertSame('80', $port->target);
        $this->assertSame('8000', $port->published);
        $this->assertNull($port->protocol);
        $this->assertSame('8000:80', $port->getData());

        $port = new MappedPort('127.0.0.1:8000-8010:80-90/tcp');
        $this->assertSame('80-90', $port->target);
        $this->assertSame('127.0.0.1:8000-8010', $port->published);
        $this->assertSame('tcp', $port->protocol);
        $this->assertSame('127.0.0.1:8000-8010:80-90/tcp', $port->getData());

        $port = new MappedPort([
            'target' => '70',
            'published' => '71',
        ]);
        $this->assertSame('71:70', $port->getData());
        $port->disable();
        $this->assertNull($port->getData());
    }
}

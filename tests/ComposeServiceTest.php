<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\ComposeService;

class ComposeServiceTest extends BaseTestCase
{
    public function testService(): void
    {
        $service = new ComposeService([
            'image' => 'alpine',
            'container_name' => 'name',
            'restart' => 'always',
            'build' => [
                'context' => './dir',
            ],
            'environment' => [
                'one=1',
                'two',
            ],
            'labels' => 'author=me',
            'networks' => [
                'one',
                'two' => [
                    'aliases' => ['a', 'b'],
                    'ipv4_address' => '1.2.3.4',
                    'ipam' => [
                        'driver' => 'default',
                    ],
                ],
            ],
            'volumes' => [
                './app:/var/www/app:ro',
            ],
            'ports' => [
                '127.0.0.1:80:80',
            ],
            'depends_on' => [
                'first',
                'second',
            ],
            'x-param' => 'y-param',
        ]);
        $this->assertSame('alpine', $service->image);
        $this->assertTrue(isset($service->networks['one']));
        $this->assertFalse(isset($service->networks['three']));
        $service->networks['one']->disable();
        $service->networks['three'] = [];
        $service->networks['three']->aliases->on('alias1');
        $service->restart = 'no';
        $service->ports->bind(['target' => '70', 'published' => '71'])->published = 72;
        $service->ports->bind(['target' => '73', 'published' => '74'])->disable();
        $service->depends_on->off('first');
        $service->depends_on->on('third');
        $service->volumes->findBySource('./app')->read_only = false;
        $this->assertEquals([
            'image' => 'alpine',
            'container_name' => 'name',
            'restart' => 'no',
            'build' => './dir',
            'ports' => [
                '127.0.0.1:80:80',
                '72:70',
            ],
            'volumes' => [
                './app:/var/www/app',
            ],
            'environment' => [
                'one' => '1',
                'two' => '',
            ],
            'labels' => [
                'author' => 'me',
            ],
            'networks' => [
                'two' => [
                    'aliases' => ['a', 'b'],
                    'ipv4_address' => '1.2.3.4',
                    'ipam' => [
                        'driver' => 'default',
                    ],
                ],
                'three' => [
                    'aliases' => [
                        'alias1',
                    ],
                ],
            ],
            'depends_on' => [
                'second',
                'third',
            ],
            'x-param' => 'y-param',
        ], $service->getData());
        $service->disable();
        $this->assertNull($service->getData());
    }
}

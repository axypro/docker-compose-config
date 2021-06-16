<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\ComposeConfig;

class ComposeConfigTest extends BaseTestCase
{
    public function testConfig(): void
    {
        $config = new ComposeConfig([
            'version' => '3.9',
            'services' => [
                'www' => [
                    'image' => 'nginx',
                ],
            ],
            'volumes' => [
                'one' => [
                    'driver' => 'foobar',
                    'driver_opts' => [
                        'type' => 'nfs',
                    ],
                    'var' => 'val',
                ],
                'two' => [],
            ],
            'networks' => [
                'one' => [
                    'external' => true,
                ],
            ],
            'x' => 'a',
            'y' => 'b',
        ]);
        $this->assertSame('3.9', $config->version);
        $config->version = null;
        $this->assertSame('nginx', $config->services['www']->image);
        $config->services->create('php')->image = 'php';
        $config->networks->create('three');
        $config->networks->create('two', []);
        unset($config->networks['three']);
        $this->assertSame('foobar', $config->volumes['one']->driver);
        $config->volumes['one']->driver_opts['device'] = './';
        $config->volumes['one']->external = true;
        $config->volumes['one']->additional['var'] .= '!';
        $config->volumes['two']->disable();
        $config->volumes->create('three');
        $this->assertEquals([
            'services' => [
                'www' => [
                    'image' => 'nginx',
                ],
                'php' => [
                    'image' => 'php',
                ],
            ],
            'volumes' => [
                'one' => [
                    'driver' => 'foobar',
                    'driver_opts' => [
                        'type' => 'nfs',
                        'device' => './',
                    ],
                    'external' => true,
                    'var' => 'val!',
                ],
                'three' => [],
            ],
            'networks' => [
                'one' => [
                    'external' => true,
                ],
                'two' => [],
            ],
            'x' => 'a',
            'y' => 'b',
        ], $config->getData());
    }
}

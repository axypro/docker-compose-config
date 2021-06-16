<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\ServicesList;

class ServicesListTest extends BaseTestCase
{
    public function testService(): void
    {
        $services = new ServicesList([
            'www' => [
                'image' => 'nginx',
            ],
            'php' => [
                'build' => './php',
            ],
        ]);
        $this->assertSame('nginx', $services['www']->image);
        $services->disableService('www');
        $services['php']->build->dockerfile = 'alternate';
        $services->create('db', [
            'image' => 'mysql',
        ])->additional['x'] = 'y';
        $this->assertEquals([
            'php' => [
                'build' => [
                    'context' => './php',
                    'dockerfile' => 'alternate',
                ],
            ],
            'db' => [
                'image' => 'mysql',
                'x' => 'y',
            ],
        ], $services->getData());
    }
}

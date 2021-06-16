<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\BuildSection;

class BuildSectionTest extends BaseTestCase
{
    public function testBuild(): void
    {
        $build = new BuildSection();
        $this->assertNull($build->getData());
        $build->context = './build';
        $this->assertSame('./build', $build->getData());
        $build->dockerfile = 'Dockerfile-alternate';
        $build->args['x'] = 'y';
        $build->cache_from[] = 'alpine:latest';
        $build->labels['com.example.description'] = 'Description';
        $build->network = 'host';
        $build->shm_size = 123;
        $build->target = 'prod';
        $build->additional['x-param'] = 'x-value';
        $this->assertEquals([
            'context' => './build',
            'dockerfile' => 'Dockerfile-alternate',
            'args' => [
                'x' => 'y',
            ],
            'cache_from' => [
                'alpine:latest',
            ],
            'labels' => [
                'com.example.description' => 'Description',
            ],
            'network' => 'host',
            'shm_size' => 123,
            'target' => 'prod',
            'x-param' => 'x-value',
        ], $build->getData());
        $build->disable();
        $this->assertNull($build->getData());
    }

    public function testLoad(): void
    {
        $build = new BuildSection([
            'args' => [
                'x' => 'y',
            ],
            'x-one' => 'y-one',
            'context' => './build',
            'labels' => 'wrong',
            'x-two' => 'y-two',
        ]);
        $this->assertEquals([
            'context' => './build',
            'args' => [
                'x' => 'y',
            ],
            'labels' => [
                'wrong' => '',
            ],
            'x-one' => 'y-one',
            'x-two' => 'y-two',
        ], $build->getData());
    }
}

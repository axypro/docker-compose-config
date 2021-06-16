<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use Symfony\Component\Yaml\Yaml;

class t extends BaseTestCase
{
    public function testT(): void
    {
        $data = Yaml::parse(file_get_contents(__DIR__ . '/t.yml'));

        $config = new \axy\docker\compose\config\ComposeConfig($data);
        $config->services['www']->build->dockerfile = null;
        $config->services['www']->additional['one'] = 'three';
        $config->services['www']->labels['me'] = 'I';
        $config->services['www']->ports->clear();
        $port = $config->services['www']->ports->bind();
        $port->target = '12';
        $port->published = '123';
        $php = $config->services->create('php');
        $php->image = 'php';
        $php->labels['author'] = 'U';
        $php->environment['x'] = '';
        $config->services['www']->expose->on(23);

        echo PHP_EOL . '----------' . PHP_EOL;
        print_r($data);
        echo PHP_EOL . '----------' . PHP_EOL;
        $result = $config->getData();
        print_r($result);
        $yaml = Yaml::dump($config->getData(), 10);
        echo PHP_EOL . '----------' . PHP_EOL;
        print_r(Yaml::parse($yaml));
        echo PHP_EOL . '----------' . PHP_EOL;
        echo $yaml;
        exit();
    }
}

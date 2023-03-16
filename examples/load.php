<?php

declare(strict_types=1);

namespace axy\docker\compose\config\examples;

use axy\docker\compose\config\ComposeConfig;

require_once __DIR__ . '/../index.php';

$config = new ComposeConfig([
    'services' => [
        'php' => [
            'build' => [
                'context' => './build/php',
            ],
        ],
    ],
]);

echo 'Context: ' . ($config->services['php']->build->context) . PHP_EOL;

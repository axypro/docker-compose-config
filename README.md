# axy\docker\compose\config

[![Latest Stable Version](https://img.shields.io/packagist/v/axy/docker-compose-config.svg?style=flat-square)](https://packagist.org/packages/axy/docker-compose-config)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)
[![License](https://poser.pugx.org/axy/docker-compose-config/license)](LICENSE)

Work with [docker-compose config](https://docs.docker.com/compose/compose-file/).

The library don't create and load any files, don't parse and build Yml.
Regular PHP arrays are used for input and output.

## ComposeConfig

`ComposeConfig` is main class of the library.
You can create an empty config or load an existing.

```php
use axy\docker\compose\config\ComposeConfig;

// data loading is a matter of external code
$data = Yaml::parse(file_get_contents('docker-compose.yml'));
$config = new ComposeConfig($data);

// manipulation
$service = $config->services->create('php');
$service->build->context = './build';
// ...

// data saving is a matter of external code
$data = $config->getData();
$yaml = Yaml::dump($data, 5);
file_put_contents('docker-compose.yml', $yaml);
```

The constructor `new ComposeConfig()` without argument creates an empty config.

## Validation

The library doesn't strictly validate on load.
If a field has wrong format it will be converted to correct value or ignored.

## Structures

A class of a config component has its own set of properties that correspond to the fields of that component.
For example, `ComposeConfig` has properties `$version`, `$services`, `$volumes`, `$network`.

For simple fields are created simple properties.
Nullable strings (such `$version`) or arrays.
Complex field correspond to object of the specific class.
For example, `$config->services` is container of service objects.

All structures are initialized on load even if they are not specified in the source file.
For example, you can work with `$service->build` as with an object even if `build` section is not specified for this service or specified as string.
It is just that all fields of `$service->build` will be empty and this field will not be put to result unless you change it.

Usually fields with NULL, empty strings or empty values are not put to the result.
Objects themselves decide this issue.

### `additional`

Some classes have public array `additional`.
All fields that don't correspond to other property fall to there.

```yml
version: "3.8"

services:
    www:
        image: nginx

foo: bar
bar: foo
```

`version` and `services` are standard fields but `foo` and `bar` will be added to additional.
All addition fields will be added to result as is.

## TDisable

Trait `TDisable` define follow methods:

* `isEnabled(): bool`
* `disable(): void`
* `enable(): void`

Objects with this trait can be disabled:

```php
$config->services['db']->disable(); // disable service "db"
```

Disabled components will be removed from result config.
Disabled object stores all its data and will be restored after `enable()`.

## `$config->services`

List of services.
Implements `ArrayAccess`.

```php
$php = $config->services->create('php'); // create empty service php
$www = $config->services->create('www', ['image' => 'nginx']); // create service based on loaded config
$config->services['php']->build->context = './build'; // get service by name
$config->services->disableService('db'); // disable services if it exists
$config->services->clear(); // clear the service list
```

## Service object

`ComposeService` instance contains follow properties:

* ?string `$image`
* ?string `$container_name`
* ?string `$restart`
* BuildSection `$build`
* PortsSection `$ports`
* ExposeSection `$expose`
* EnvironmentSection `$environment`
* LabelsSection `$labels`
* ?string `$network_mode`
* ServiceNetworksSection `$networks`
* DependsOnSection `$depends_on`
* array `$additional`

## Keys

Some sections like `ports`, `expose` and `volumes` (inside a service) are just a list of unnamed values.
Three are methods for search like `$volumes->findBySource()`.
Can also use "keys" and "groups".

You can bind a value with a key (an arbitrary string) or add it to a group.
Keys and groups will be not represented in yml file, but you can use for config manipulations.

```php
// Base template
$service->volumes->add('./app:/var/www/app'); // bind volume without key
$service->volumes->add('./log:/var/log/nginx', 'nginx_log'); // with key "nginx_log"

// ...

// I want disable mount nginx log
$service->volumes->getByKey('nginx_log')->disable();
```

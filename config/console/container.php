<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @var ContainerBuilder $containerBuilder
 */

$fileLocator = new FileLocator(__DIR__);
$loader = new PhpFileLoader($containerBuilder, $fileLocator);

// Console framework
$loader->load(__DIR__ . '/../../../../../vendor/untek-framework/console/src/resources/config/services/main.php');

// Init utility
$loader->load(__DIR__ . '/../../../../../vendor/untek-utility/init/src/resources/config/services/package.php');

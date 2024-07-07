<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Untek\Core\App\Bootstrap\ContainerFactory;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()->defaults()->public();
    $parameters = $configurator->parameters();

    $services
        ->set(ContainerInterface::class)
        ->factory([ContainerFactory::class, 'create'])
        ->public();
};
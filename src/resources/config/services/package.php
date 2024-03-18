<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Untek\Utility\Init\Presentation\Cli\Commands\InitCommand;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()->defaults()->public();
    $parameters = $configurator->parameters();

    $services->set(InitCommand::class, InitCommand::class)
        ->args(
            [
                service(\Psr\Container\ContainerInterface::class)
            ]
        )
        ->tag('console.command');
};
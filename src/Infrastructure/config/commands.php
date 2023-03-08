<?php

use Untek\Framework\Console\Symfony4\Interfaces\CommandConfiguratorInterface;

return function (CommandConfiguratorInterface $commandConfigurator) {
    $commandConfigurator->registerCommandClass(\Untek\Utility\Init\Presentation\Cli\Commands\InitCommand::class);
};

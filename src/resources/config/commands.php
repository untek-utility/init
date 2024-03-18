<?php

use Untek\Framework\Console\Symfony4\Interfaces\CommandConfiguratorInterface;

\Untek\Core\Code\Helpers\DeprecateHelper::hardThrow();
return function (CommandConfiguratorInterface $commandConfigurator) {
    $commandConfigurator->registerCommandClass(\Untek\Utility\Init\Presentation\Cli\Commands\InitCommand::class);
};

<?php

namespace Untek\Utility\Init\Infrastructure;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Untek\Core\Kernel\Bundle\BaseBundle;

class InitBundle extends BaseBundle
{

    public function getName(): string
    {
        return 'init';
    }

    public function boot(): void
    {
        if ($this->isCli()) {
            $this->configureFromPhpFile(__DIR__ . '/config/commands.php');
        }
    }

    public function build(ContainerBuilder $containerBuilder)
    {
        $this->load($containerBuilder, __DIR__ . '/DependencyInjection/Symfony/services/package.php');
    }
}

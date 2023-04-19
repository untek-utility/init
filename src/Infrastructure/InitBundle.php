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

    public function build(ContainerBuilder $containerBuilder)
    {
        $this->importServices($containerBuilder, __DIR__ . '/../Resources/config/services/package.php');
    }
}

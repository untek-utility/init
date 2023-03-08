<?php

namespace Untek\Utility\Init\Infrastructure;

use Mservis\Operator\Shared\Infrastructure\Bundle\BaseBundle;

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
}

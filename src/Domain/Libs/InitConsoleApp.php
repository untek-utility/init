<?php

namespace Untek\Utility\Init\Domain\Libs;

use Untek\Framework\Console\Symfony4\Base\BaseConsoleApp;

class InitConsoleApp extends BaseConsoleApp
{

    protected function bundles(): array
    {
        return [
            \Untek\Lib\Init\Bundle::class,
        ];
    }

    /*protected function initBundles(): void
    {
        $this->addBundles([
            \Untek\Lib\Init\Bundle::class,
        ]);
        parent::initBundles();
    }*/
}

<?php

namespace Untek\Utility\Init\Presentation\Libs;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Untek\Utility\Init\Presentation\Cli\Tasks\BaseTask;

class Init
{
    
    private StyleInterface $io;

    private array $profileConfig;

    public function __construct(
        StyleInterface $io,
        array $profileConfig,
    )
    {
        $this->profileConfig = $profileConfig;
        $this->io = $io;
    }

    public function run()
    {
        $profileConfig = $this->profileConfig;
        foreach ($this->profileConfig['tasks'] as $taskInstance) {
            /** @var BaseTask $taskInstance */
            $taskInstance->setParams($profileConfig);
            $taskInstance->setIo($this->io);
            $taskInstance->run();
        }
    }
}

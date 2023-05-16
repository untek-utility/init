<?php

namespace Untek\Utility\Init\Presentation\Libs;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Untek\Utility\Init\Presentation\Cli\Tasks\BaseTask;

class Init
{
    private InputInterface $input;

    private OutputInterface $output;

    private array $profileConfig;

    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        array $profileConfig,
    ) {
        $this->profileConfig = $profileConfig;
        $this->input = $input;
        $this->output = $output;
    }

    public function run()
    {
        if (!extension_loaded('openssl')) {
            die('The OpenSSL PHP extension is required.');
        }
        $profileConfig = $this->profileConfig;
        foreach ($this->profileConfig['definitions'] as $taskInstance) {
            /** @var BaseTask $taskInstance */
            $taskInstance->setConfigs($this->input, $this->output);
            $taskInstance->setParams($profileConfig);
            $taskInstance->run();
        }
    }
}

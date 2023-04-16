<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseTask
{
    protected $input;

    protected $output;

//    protected $env;

    protected array $params = [];

    protected string $sourcePath;

    abstract public function run();

    public function setParam(string $name, mixed $value) {
        $this->params[$name] = $value;
    }

    public function setParams(array $params) {
        $this->params = $this->params + $params;
    }

    public function setConfigs(InputInterface $input, OutputInterface $output, string $sourcePath)
    {
        $this->input = $input;
        $this->output = $output;
//        $this->env = $env;
        $this->sourcePath = $sourcePath;
    }
}

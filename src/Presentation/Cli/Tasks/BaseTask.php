<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseTask
{

    protected $input;
    protected $output;
    protected $root;
    protected $env;
    protected $params;

    public function __construct(InputInterface $input, OutputInterface $output, string $root, array $env, array $params)
    {
        $this->input = $input;
        $this->output = $output;
        $this->root = $root;
        $this->env = $env;
        $this->params = $params;
    }

    abstract public function run(array $links);

}

<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Symfony\Component\Console\Style\StyleInterface;

abstract class BaseTask
{

    protected StyleInterface $io;

    protected array $params = [];

    abstract public function run(): void;

    public function setParam(string $name, mixed $value)
    {
        $this->params[$name] = $value;
    }

    public function setParams(array $params)
    {
        $this->params = $this->params + $params;
    }

    public function setIo(StyleInterface $io): void
    {
        $this->io = $io;
    }
}

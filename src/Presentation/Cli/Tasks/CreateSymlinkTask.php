<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

class CreateSymlinkTask extends BaseTask
{
    public function __construct(private string $rootDir, private array $paths)
    {
    }

    public function run()
    {
        foreach ($this->paths as $link => $target) {
            //first removing folders to avoid errors if the folder already exists
            @rmdir($this->rootDir . "/" . $link);
            //next removing existing symlink in order to update the target
            if (is_link($this->rootDir . "/" . $link)) {
                @unlink($this->rootDir . "/" . $link);
            }
            if (@symlink($this->rootDir . "/" . $target, $this->rootDir . "/" . $link)) {
                $this->output->write("      symlink {$this->rootDir}/$target {$this->rootDir}/$link\n");
            } else {
                $this->output->write("<error>Cannot create symlink {$this->rootDir}/$target {$this->rootDir}/$link.</error>");
            }
        }
    }

}

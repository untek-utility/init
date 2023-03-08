<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

class CreateSymlinkTask extends BaseTask
{

    public function run(array $links)
    {
        foreach ($links as $link => $target) {
            //first removing folders to avoid errors if the folder already exists
            @rmdir($this->root . "/" . $link);
            //next removing existing symlink in order to update the target
            if (is_link($this->root . "/" . $link)) {
                @unlink($this->root . "/" . $link);
            }
            if (@symlink($this->root . "/" . $target, $this->root . "/" . $link)) {
                $this->output->write("      symlink {$this->root}/$target {$this->root}/$link\n");
            } else {
                $this->output->write("<error>Cannot create symlink {$this->root}/$target {$this->root}/$link.</error>");
            }
        }
    }

}

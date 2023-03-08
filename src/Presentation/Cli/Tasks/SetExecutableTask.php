<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

class SetExecutableTask extends BaseTask
{

    public function run(array $paths)
    {
        foreach ($paths as $executable) {
            if (file_exists("{$this->root}/$executable")) {
                if (@chmod("{$this->root}/$executable", 0755)) {
                    $this->output->write("      chmod 0755 $executable\n");
                } else {
                    $this->output->write("<error>Operation chmod not permitted for $executable.</error>");
                }
            } else {
                $this->output->write("<error>$executable does not exist.</error>");
            }
        }
    }

}

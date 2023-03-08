<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

class SetWritableTask extends BaseTask
{

    public function run(array $paths)
    {
        foreach ($paths as $writable) {
            if (is_dir("{$this->root}/$writable")) {
                if (@chmod("{$this->root}/$writable", 0777)) {
                    $this->output->write("      chmod 0777 $writable\n");
                } else {
                    $this->output->write("<error>Operation chmod not permitted for directory $writable.</error>");
                }
            } else {
                $this->output->write("<error>Directory $writable does not exist.</error>");
            }
        }
    }

}

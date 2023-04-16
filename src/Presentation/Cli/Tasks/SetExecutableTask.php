<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Code\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

class SetExecutableTask extends BaseTask
{

    public function __construct(private string $rootDir, private array $paths)
    {
    }

    public function run()
    {
        foreach ($this->paths as $executable) {
            if (file_exists("{$this->rootDir}/$executable")) {
                if (@chmod("{$this->rootDir}/$executable", 0755)) {
                    $this->output->write("   chmod 0755 $executable\n");
                } else {
                    $this->output->write("<error>Operation chmod not permitted for $executable.</error>");
                }
            } else {
                $this->output->write("<error>$executable does not exist.</error>");
            }
        }
    }

}

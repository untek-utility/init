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
        foreach ($this->paths as $path) {
            $fullPath = $this->rootDir . '/' . $path;
            if (file_exists($fullPath)) {
                if (@chmod($fullPath, 0755)) {
                    $this->output->write("   chmod 0755 \"$path\"\n");
                } else {
                    $this->output->write("<error>Operation chmod not permitted for \"$path\".</error>");
                }
            } else {
                $this->output->write("<error>\"$path\" does not exist.</error>");
            }
        }
    }

}

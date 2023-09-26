<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Code\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

class SetWritableTask extends BaseTask
{

    public function __construct(private string $rootDir, private array $paths)
    {
    }

    public function run(): void
    {
        foreach ($this->paths as $path) {
            $fullPath = $this->rootDir . '/' . $path;
            if (is_dir($fullPath)) {
                if (@chmod($fullPath, 0777)) {
                    $this->io->write("   chmod 0777 \"$path\"\n");
                } else {
                    $this->io->write("<error>Operation chmod not permitted for directory \"$path\".</error>");
                }
            } else {
                $this->io->write("<error>Directory \"$path\" does not exist.</error>");
            }
        }
    }
}

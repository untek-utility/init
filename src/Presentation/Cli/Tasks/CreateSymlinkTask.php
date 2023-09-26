<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

class CreateSymlinkTask extends BaseTask
{
    public function __construct(private string $rootDir, private array $paths)
    {
    }

    public function run(): void
    {
        foreach ($this->paths as $link => $target) {

            $linkFileName = $this->rootDir . "/" . $link;
            $targetFileName = $this->rootDir . "/" . $target;

            //first removing folders to avoid errors if the folder already exists
            @rmdir($linkFileName);
            //next removing existing symlink in order to update the target
            if (is_link($linkFileName)) {
                @unlink($linkFileName);
            }
            if (@symlink($targetFileName, $linkFileName)) {
                $this->io->write("      symlink \"$targetFileName\" \"$linkFileName\"\n");
            } else {
                $this->io->write("<error>Cannot create symlink \"$targetFileName\" \"$linkFileName\".</error>");
            }
        }
    }
}

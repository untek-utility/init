<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Contract\Common\Exceptions\NotSupportedException;

class ChmodTask extends BaseTask
{
    const WRITABLE = '0777';
    const EXECUTABLE = '0755';

    public function __construct(private string $rootDir, private string $path, private string $permissions)
    {
    }

    public function run()
    {
        $tergetPath = "{$this->rootDir}/{$this->path}";
        if (file_exists($tergetPath)) {
            if ($this->permissions == self::WRITABLE) {
                $res = @chmod($tergetPath, 0777);
            } elseif ($this->permissions == self::EXECUTABLE) {
                $res = @chmod($tergetPath, 0755);
            } else {
                throw new NotSupportedException('Not supported permissions!');
            }
            if ($res) {
                $this->output->write("   chmod $this->permissions \"$this->path\"\n");
            } else {
                $this->output->write("<error>Operation chmod not permitted for \"$this->path\".</error>");
            }
        } else {
            $this->output->write("<error>\"$this->path\" does not exist.</error>");
        }
    }
}

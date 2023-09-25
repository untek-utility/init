<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Contract\Common\Exceptions\NotSupportedException;

class MkDirTask extends BaseTask
{
    const WRITABLE = '0777';
    const EXECUTABLE = '0755';

    public function __construct(private string $rootDir, private string $path, private string $permissions)
    {
    }

    public function run()
    {
        $fullPath = $this->rootDir . '/' . $this->path;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
            if ($this->permissions == self::WRITABLE) {
                $res = @chmod($fullPath, 0777);
            } elseif ($this->permissions == self::EXECUTABLE) {
                $res = @chmod($fullPath, 0755);
            } else {
                throw new NotSupportedException('Not supported permissions!');
            }
            if ($res) {
                $this->output->write("   mkdir {$this->permissions} \"$this->path\" \n");
            } else {
                $this->output->write("<error>Operation chmod not permitted for \"$this->path\".</error>");
            }
        } else {
            $this->output->write("<info>Directory \"$this->path\" already exist.</info> \n");
        }
    }
}

<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Contract\Common\Exceptions\NotSupportedException;

class ChmodTask extends BaseTask
{
    const WRITABLE = '0777';
    const EXECUTABLE = '0755';
    
    public function __construct(private string $rootDir, private array $paths)
    {
    }

    public function run()
    {
        foreach ($this->paths as $path => $permissions) {
            $tergetPath = "{$this->rootDir}/$path";
            if (file_exists($tergetPath)) {
                if($permissions == self::WRITABLE) {
                    $res = @chmod($tergetPath, 0777);
                } elseif($permissions == self::EXECUTABLE) {
                    $res = @chmod($tergetPath, 0755);
                } else {
                    throw new NotSupportedException('Not supported permissions!');
                }
                if ($res) {
                    $this->output->write("   chmod {$permissions} $path\n");
                } else {
                    $this->output->write("<error>Operation chmod not permitted for $path.</error>");
                }
            } else {
                $this->output->write("<error>$path does not exist.</error>");
            }
        }
    }
}

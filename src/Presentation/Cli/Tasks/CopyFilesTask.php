<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Framework\Console\Symfony4\Helpers\InputHelper;

class CopyFilesTask extends BaseTask
{

    private $all = false;

    public function __construct(private string $rootDir, private string $sourceDir, private array $skipFiles = [])
    {
    }

    public function run()
    {
        $sourcePath = $this->sourceDir;
        if (!is_dir($sourcePath)) {
            $this->output->write("<error>Directory \"$sourcePath\" does not exist.</error>");
            exit(3);
        }

        $files = $this->getFileList($sourcePath);

        if (isset($this->skipFiles)) {
            $skipFiles = $this->skipFiles;
            array_walk($skipFiles, function (&$value) {
                $value = "{$this->rootDir}/$value";
            });
            $files = array_diff($files, array_intersect_key($skipFiles, array_filter($skipFiles, 'file_exists')));
        }
        foreach ($files as $file) {
            if (!$this->copyFile($this->rootDir, "$sourcePath/$file", $file)) {
                break;
            }
        }
    }

    private function getFileList($root, $basePath = '')
    {
        $files = [];
        $handle = opendir($root);
        while (($path = readdir($handle)) !== false) {
            if ($path === '.git' || $path === '.svn' || $path === '.' || $path === '..') {
                continue;
            }
            $fullPath = "{$root}/$path";
            $relativePath = $basePath === '' ? $path : "$basePath/$path";
            if (is_dir($fullPath)) {
                $files = array_merge($files, $this->getFileList($fullPath, $relativePath));
            } else {
                $files[] = $relativePath;
            }
        }
        closedir($handle);
        return $files;
    }

    private function copyFile($rootDir, $source, $target)
    {
        if (!is_file($source)) {
            $this->output->write("       skip \"$target\" (\"$source\" not exist)\n");
            return true;
        }
        $targetPath = $rootDir . '/' . $target;
        if (is_file($targetPath)) {
            if (file_get_contents($source) === file_get_contents($targetPath)) {
                $this->output->write("  unchanged \"$target\"\n");
                return true;
            }
            if ($this->all) {
                $this->output->write("  overwrite \"$target\"\n");
            } else {
                $this->output->write("      exist \"$target\"\n");
                $questionText = '            ...overwrite? [Yes|No|All] ';
                $answer = !empty($this->params['overwrite']) ? 'y' : InputHelper::question($this->input, $this->output, $questionText);

                if (!strncasecmp($answer, 'y', 1)) {
                    $this->output->write("  overwrite \"$target\"\n");
                } else {
                    if (!strncasecmp($answer, 'a', 1)) {
                        $this->output->write("  overwrite \"$target\"\n");
                        $this->all = true;
                    } else {
                        $this->output->write("       skip \"$target\"\n");
                        return true;
                    }
                }
            }
            file_put_contents($targetPath, file_get_contents($source));
            return true;
        }
        $this->output->write("   copy \"$target\"\n");
        @mkdir(dirname($rootDir . '/' . $target), 0777, true);
        file_put_contents($rootDir . '/' . $target, file_get_contents($source));
        return true;
    }
}

<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Framework\Console\Symfony4\Helpers\InputHelper;

class CopyFilesTask extends BaseTask
{

    public function run(array $links)
    {
        $rootPath = $this->env['path'];
        if (!is_dir($rootPath)) {
            $this->output->write("<error>$rootPath directory \"$rootPath\" does not exist.</error>");
            exit(3);
        }

        $files = $this->getFileList($rootPath);

        if (isset($this->env['skipFiles'])) {
            $skipFiles = $this->env['skipFiles'];
            array_walk($skipFiles, function (&$value) {
                $value = "{$this->root}/$value";
            });
            $files = array_diff($files, array_intersect_key($this->env['skipFiles'], array_filter($skipFiles, 'file_exists')));
        }
        $all = false;
        foreach ($files as $file) {
            if (!$this->copyFile($this->root, "{$this->env['path']}/$file", $file, $all)) {
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

    private function copyFile($root, $source, $target, &$all)
    {
        if (!is_file($source)) {
            $this->output->write("       skip $target ($source not exist)\n");
            return true;
        }
        if (is_file($root . '/' . $target)) {
            if (file_get_contents($source) === file_get_contents($root . '/' . $target)) {
                $this->output->write("  unchanged $target\n");
                return true;
            }
            if ($all) {
                $this->output->write("  overwrite $target\n");
            } else {
                $this->output->write("      exist $target\n");
                $questionText = '            ...overwrite? [Yes|No|All|Quit] ';
                $answer = $this->params['overwrite'] ?? InputHelper::question($this->input, $this->output, $questionText);

                if (!strncasecmp($answer, 'q', 1)) {
                    return false;
                } else {
                    if (!strncasecmp($answer, 'y', 1)) {
                        $this->output->write("  overwrite $target\n");
                    } else {
                        if (!strncasecmp($answer, 'a', 1)) {
                            $this->output->write("  overwrite $target\n");
                            $all = true;
                        } else {
                            $this->output->write("       skip $target\n");
                            return true;
                        }
                    }
                }
            }
            file_put_contents($root . '/' . $target, file_get_contents($source));
            return true;
        }
        $this->output->write("   generate $target\n");
        @mkdir(dirname($root . '/' . $target), 0777, true);
        file_put_contents($root . '/' . $target, file_get_contents($source));
        return true;
    }
}

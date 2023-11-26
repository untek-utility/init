<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Symfony\Component\Console\Question\Question;

class CopyFilesTask extends BaseTask
{

    private $all = false;

    public function __construct(private string $rootDir, private string $sourceDir, private array $skipFiles = [])
    {
    }

    public function run(): void
    {
        $sourcePath = $this->sourceDir;
        if (!is_dir($sourcePath)) {
            $this->io->write("<error>Directory \"$sourcePath\" does not exist.</error>");
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
            $this->copyFile($this->rootDir, "$sourcePath/$file", $file);
            /*if (!$this->copyFile($this->rootDir, "$sourcePath/$file", $file)) {
                break;
            }*/
        }
    }

    private function getFileList(string $root, string $basePath = ''): array
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

    private function copyFile(string $rootDir, string $source, string $target): void
    {
        if (!is_file($source)) {
            $this->io->write("       skip \"$target\" (\"$source\" not exist)\n");
            return;
        }
        $targetPath = $rootDir . '/' . $target;
        if (is_file($targetPath)) {
            if (file_get_contents($source) === file_get_contents($targetPath)) {
                $this->io->write("  unchanged \"$target\"\n");
                return;
            }
            if ($this->all) {
                $this->io->write("  overwrite \"$target\"\n");
            } else {
                $this->io->write("      exist \"$target\"\n");
                $questionText = '            ...overwrite? [Yes|No|All] ';

                $question = new Question($questionText);
                $answer = !empty($this->params['overwrite']) ? 'y' : ($this->io->askQuestion($question) ?? '');

                if (!strncasecmp($answer, 'y', 1)) {
                    $this->io->write("  overwrite \"$target\"\n");
                } else {
                    if (!strncasecmp($answer, 'a', 1)) {
                        $this->io->write("  overwrite \"$target\"\n");
                        $this->all = true;
                    } else {
                        $this->io->write("       skip \"$target\"\n");
                        return;
                    }
                }
            }
            file_put_contents($targetPath, file_get_contents($source));
            return;
        }
        $this->io->write("   copy \"$target\"\n");
        @mkdir(dirname($rootDir . '/' . $target), 0777, true);
        file_put_contents($rootDir . '/' . $target, file_get_contents($source));
    }
}

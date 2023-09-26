<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Text\Libs\RandomString;

class RandomTask extends BaseTask
{

    public function __construct(
        private string $rootDir,
        private string $path,
        private string $placeHolder,
        private int $length = 64,
        private string $quote = '"',
        private array $quoteChars = ['"', '$']
    )
    {
    }

    public function run(): void
    {
        $this->io->write("   generate random value for \"$this->placeHolder\" in file \"$this->path\"\n");
        $file = $this->rootDir . '/' . $this->path;
        $content = file_get_contents($file);
        $content = $this->generateRandomValue($content);
        file_put_contents($file, $content);
    }

    /**
     * @param $content
     * @return mixed
     */
    function generateRandomValue(string $content): string
    {
        $key = RandomString::generateNumLowerUpper($this->length);
        if ($this->quoteChars) {
            foreach ($this->quoteChars as $char) {
                $key = str_replace($char, '\\' . $char, $key);
            }
        }
        $content = str_replace('{' . $this->placeHolder . '}', $key, $content);
        return $content;
    }
}

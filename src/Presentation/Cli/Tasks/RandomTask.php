<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Text\Libs\RandomString;

class RandomTask extends BaseTask
{

    public int $length = 64;
    public string $quote = '"';
    public array $quoteChars = [
        '"',
        '$',
    ];

    public function __construct(private string $rootDir, private string $path, private string $placeHolder)
    {
    }

    public function run()
    {
        $this->output->write("   generate cookie validation key in \"$this->path\"\n");
        $file = $this->rootDir . '/' . $this->path;
        $content = file_get_contents($file);
        $content = $this->generateRandomKeysInEnvConfig($content);
        file_put_contents($file, $content);
    }

    /**
     * @param $content
     * @return mixed
     */
    function generateRandomKeysInEnvConfig($content)
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

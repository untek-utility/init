<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Text\Libs\RandomString;

class RandomTask extends BaseTask
{

    public $placeHolders = [];
    public $length = 32;
    public $quote = null;
    public $quoteChars = [];

    public function run(array $paths)
    {
        foreach ($paths as $file) {
            $this->output->write("   generate cookie validation key in $file\n");
            $file = $this->root . '/' . $file;
            $content = file_get_contents($file);
            $content = $this->generateRandomKeysInEnvConfig($content);
            file_put_contents($file, $content);
        }
    }

    /**
     * @param $content
     * @return mixed
     */
    function generateRandomKeysInEnvConfig($content)
    {
        foreach ($this->placeHolders as $placeHolder) {
            $key = RandomString::generateNumLowerUpper($this->length);
            if ($this->quoteChars) {
                foreach ($this->quoteChars as $char) {
                    $key = str_replace($char, '\\' . $char, $key);
                }
            }
            $content = str_replace('{' . $placeHolder . '}', $key, $content);
        }
        return $content;
    }
}

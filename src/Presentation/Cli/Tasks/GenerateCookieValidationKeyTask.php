<?php

namespace Untek\Utility\Init\Presentation\Cli\Tasks;

use Untek\Core\Text\Libs\RandomString;

class GenerateCookieValidationKeyTask extends BaseTask
{
    public function __construct(private string $rootDir, private array $paths)
    {
    }

    public function run()
    {
        foreach ($this->paths as $file) {
            $this->output->write("   generate cookie validation key in $file\n");
            $file = $this->rootDir . '/' . $file;

            $content = file_get_contents($file);

            $apps = [
                'COOKIE_VALIDATION_KEY_WEB',
                'COOKIE_VALIDATION_KEY_ADMIN',
                'JWT_PROFILES_AUTH_KEY',
            ];
            $content = $this->generateRandomKeysInEnvConfig($content, $apps);

            file_put_contents($file, $content);
        }
    }

    function generateKey()
    {
        $length = 32;
        $bytes = openssl_random_pseudo_bytes($length);
        $key = strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
        return $key;
    }

    /**
     * @param $content
     * @param $apps
     * @return mixed
     */
    function generateRandomKeysInEnvConfig($content, $apps)
    {
        foreach ($apps as $app) {
            $key = RandomString::generateNumLowerUpper(32);
            $content = str_replace('{' . $app . '}', $key, $content);
        }
        return $content;
    }

}

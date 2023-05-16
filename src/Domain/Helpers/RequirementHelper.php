<?php

namespace Untek\Utility\Init\Domain\Helpers;

class RequirementHelper
{

    public static function getImagickMemo(bool $result = null): string
    {
        $result = $result !== null ? $result : self::checkImagick();
        $imagickMemo = 'Either GD PHP extension with FreeType support or ImageMagick PHP extension with PNG support is required for image CAPTCHA.';
        if( ! $result) {
            $imagickMemo = 'Imagick extension should be installed with PNG support in order to be used for image CAPTCHA.';
        }
        return $imagickMemo;
    }

    public static function getGdMemo(bool $result = null): string
    {
        $result = $result !== null ? $result : self::checkGd();
        $gdMemo = 'Either GD PHP extension with FreeType support or ImageMagick PHP extension with PNG support is required for image CAPTCHA.';
        if( ! $result) {
            $gdMemo = 'GD extension should be installed with FreeType support in order to be used for image CAPTCHA.';
        }
        return $gdMemo;
    }

    public static function checkImagick(): bool
    {
        $imagickOK = false;
        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagickFormats = $imagick->queryFormats('PNG');
            if (in_array('PNG', $imagickFormats)) {
                $imagickOK = true;
            }
        }
        return $imagickOK;
    }

    public static function checkGd(): bool
    {
        $gdOK = false;
        if (extension_loaded('gd')) {
            $gdInfo = gd_info();
            if (!empty($gdInfo['FreeType Support'])) {
                $gdOK = true;
            }
        }
        return $gdOK;
    }

    public static function check(array $requirements): array
    {
        /*if (is_string($requirements)) {
            $requirements = require $requirements;
        }*/
        if (!is_array($requirements)) {
            self::usageError('Requirements must be an array, "' . gettype($requirements) . '" has been given!');
        }
        $result = array(
            'summary' => array(
                'total' => 0,
                'errors' => 0,
                'warnings' => 0,
            ),
            'requirements' => array(),
        );
        foreach ($requirements as $key => $rawRequirement) {
            $result['summary']['total']++;
            $requirement = self::checkItem($result, $rawRequirement, $key);
            if($requirement['error']) {
                $result['summary']['errors']++;
            } elseif($requirement['warning']) {
                $result['summary']['warnings']++;
            }
            $result['requirements'][] = $requirement;
        }

        return $result;
    }

    private static function checkItem(array $result, $rawRequirement, $key): array {
        $requirement = self::normalizeRequirement($rawRequirement, $key);
        if (!$requirement['condition']) {
            if ($requirement['mandatory']) {
                $requirement['error'] = true;
                $requirement['warning'] = true;
            } else {
                $requirement['error'] = false;
                $requirement['warning'] = true;
            }
        } else {
            $requirement['error'] = false;
            $requirement['warning'] = false;
        }

        return $requirement;
    }

    /**
     * Displays a usage error.
     * This method will then terminate the execution of the current application.
     * @param string $message the error message
     */
    private static function usageError(string $message)
    {
        echo "Error: $message\n\n";
        exit(1);
    }

    /**
     * Normalizes requirement ensuring it has correct format.
     * @param array $requirement raw requirement.
     * @param int $requirementKey requirement key in the list.
     * @return array normalized requirement.
     */
    private static function normalizeRequirement(array $requirement, $requirementKey = 0): array
    {
        if (!is_array($requirement)) {
            self::usageError('Requirement must be an array!');
        }
        if (!array_key_exists('condition', $requirement)) {
            self::usageError("Requirement '{$requirementKey}' has no condition!");
        } else {
            $evalPrefix = 'eval:';
            if (is_string($requirement['condition']) && strpos($requirement['condition'], $evalPrefix) === 0) {
                $expression = substr($requirement['condition'], strlen($evalPrefix));
                $requirement['condition'] = self::evaluateExpression($expression);
            }
        }
        if (!array_key_exists('name', $requirement)) {
            $requirement['name'] = is_numeric($requirementKey) ? 'Requirement #' . $requirementKey : $requirementKey;
        }
        if (!array_key_exists('mandatory', $requirement)) {
            if (array_key_exists('required', $requirement)) {
                $requirement['mandatory'] = $requirement['required'];
            } else {
                $requirement['mandatory'] = false;
            }
        }
        if (!array_key_exists('by', $requirement)) {
            $requirement['by'] = 'Unknown';
        }
        if (!array_key_exists('memo', $requirement)) {
            $requirement['memo'] = '';
        }

        return $requirement;
    }

    /**
     * Evaluates a PHP expression under the context of this class.
     * @param string $expression a PHP expression to be evaluated.
     * @return mixed the expression result.
     */
    private static function evaluateExpression(string $expression)
    {
        return eval('return ' . $expression . ';');
    }
}

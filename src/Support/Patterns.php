<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace SpaethTech\Support;


use Exception;
use ParseError;

/**
 * Class Patterns
 *
 * @package SpaethTech\Common
 * @author Ryan Spaeth <rspaeth@SpaethTech.net>
 * @final
 */
final class Patterns
{

    //private const PATTERN_JSON    = "/(\{.*\})/";
    private const PATTERN_JSON    = "/(\{.*})/";

    //private const PATTERN_ARRAY   = "/(\[.*\])/";
    private const PATTERN_ARRAY   = "/(\[.*])/";

    //private const PATTERN_EVAL    = "/(\`.*\`)/";


    /**
     * Checks to see whether the provided string value is a JSON literal and optionally mutates the literal value
     * to an associative array from the decoded JSON.
     *
     * @param string|null $value The string to validate as JSON.  When mutated, this value becomes an associative array.
     * @param bool $mutate When TRUE, the original value is mutated from the decoded JSON.  Defaults to TRUE.
     *
     * @return bool Returns TRUE, when the provided string is a valid JSON literal.
     */
    public static function isJSON(string &$value = null, bool $mutate = true): bool
    {
        $result = preg_match(self::PATTERN_JSON, $value) == true;

        $json = null;

        if($result)
        {
            $json = json_decode($value, true);

            if (json_last_error() !== JSON_ERROR_NONE)
                return false;

            if($mutate && $json !== null)
                $value = $json;
        }


        return $json !== null && $result;
    }

    /**
     * Checks to see whether the provided string value is a PHP array literal and optionally mutates the literal
     * value to an actual array.
     *
     * Examples:<br>
     * <small>`(String) "[ 'key' => 'value' ]"` becomes `(Array) [ 'key' => 'value' ]`</small><br>
     * <small>`(String) "[ 'apples', 'bananas' ]"` becomes `(Array) [ 'apples', 'bananas' ]`</small><br>
     *
     * @param string|null $value The string to validate as a PHP array.  When mutated, this value becomes an array.
     * @param bool $mutate When TRUE, the original value is mutated from interpreted array.  Defaults to TRUE.
     * @return bool Returns TRUE, when the provided string is a valid JSON literal.
     */
    public static function isArray(string &$value = null, bool $mutate = true): bool
    {
        $result = preg_match(self::PATTERN_ARRAY, $value) == true;

        $array = null;

        if($result)
        {
            try
            {
                $array = eval("return $value;");

            }
            catch(Exception|ParseError $e)
            {
                return false;
            }


            if($mutate && $array !== null)
                $value = $array;
        }

        return $array !== null && $result;
    }






    /**
     * @param string $pattern
     * @param array $params
     * @param string $token
     * @return string
     * @throws Exception
     */
    public static function interpolateUrl(string $pattern, array $params, string $token = ":"): string
    {

        if (/* $pattern === null || */ $pattern === "")
            return "";

        if ($token === null || $token === "")
            throw new Exception("[SpaethTech\Common\Patterns] A TOKEN must be provided to pattern match!");

        if (strpos($pattern, " ") !== false)
            throw new Exception("[SpaethTech\Common\Patterns] The \$pattern must not contain any spaces!");

        $segments = [];

        if (strpos($pattern, $token) !== false)
        {
            $parts = explode("/", $pattern);

            //print_r($parts);

            $counter = 0;

            foreach($parts as $part)
            {
                if($part === "" && $counter !== 0)
                    throw new Exception("[SpaethTech\Common\Patterns] The \$pattern must be in a valid URL format.");

                if (strpos($part, $token) !== 0)
                {
                    $segments[] = $part;
                    $counter++;
                    continue;
                }

                $part = substr($part, 1, strlen($part) - 1);

                if(!array_key_exists($part, $params))
                    throw new Exception("[SpaethTech\Common\Patterns] Parameter '$part' was not found in \$params!");

                $segments[] = $params[$part];

                $counter++;
            }

            return implode("/", $segments);
        }
        else
        {
            return $pattern;
        }


    }


}

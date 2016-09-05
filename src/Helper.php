<?php

namespace EdwinLuijten\Houston;

use EdwinLuijten\Houston\Payload\Partials\Body;

class Helper
{
    public static function coalesce()
    {
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            if ($argument) {
                return $argument;
            }
        }

        return null;
    }

    public static function uuid4()
    {
        mt_srand();
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param $string
     * @return string
     */
    public static function pascalToCamel($string)
    {
        return strtolower(
            preg_replace(
                '/([a-z0-9])([A-Z])/',
                '$1_$2',
                preg_replace(
                    '/([^_])([A-Z][a-z]+)/',
                    '$1_$2',
                    $string
                )
            )
        );
    }

    public static function serialize($objectVars, array $override = [], array $keys = [])
    {
        $serialized = [];

        foreach ($objectVars as $key => $value) {
            if ($value instanceof \JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            if (in_array($key, $keys)) {
                $serialized[$key] = $value;
            } elseif (!is_null($value)) {
                $key              = array_key_exists($key, $override) ? $override[$key] : Helper::pascalToCamel($key);
                $serialized[$key] = $value;
            }
        }

        return $serialized;
    }

    /**
     * @param $array
     * @param $key
     * @return null
     */
    public static function get($array, $key)
    {
        return isset($array[$key]) ? $array[$key] : null;
    }
}
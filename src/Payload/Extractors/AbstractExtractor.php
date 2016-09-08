<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

use EdwinLuijten\Houston\Defaults;
use EdwinLuijten\Houston\Helper;

abstract class AbstractExtractor
{
    protected static $defaults;
    protected $scrubFields;

    public function __construct($config)
    {
        self::$defaults = Defaults::get();

        $this->setScrubFields($config);
    }

    /**
     * @param mixed $scrubFields
     */
    public function setScrubFields($scrubFields)
    {
        $this->scrubFields = self::$defaults->scrubFields($this->get($scrubFields, 'scrubFields'));
    }

    /**
     * @param $array
     * @param string $replacement
     * @return null
     */
    protected function scrub($array, $replacement = '*')
    {
        $fields = $this->scrubFields;

        if (!$fields || !$array) {
            return null;
        }

        array_walk_recursive(
            $array,
            function (&$val, $key) use ($fields, $replacement, $array) {
                if (array_key_exists($key, $array)) {
                    $val = str_repeat($replacement, 8);
                }
            }
        );

        return $array;
    }

    /**
     * @param $array
     * @param $key
     * @return mixed
     */
    protected function get($array, $key)
    {
        return Helper::get($array, $key);
    }

    /**
     * @param $name
     * @param $level
     * @param $toLog
     * @param $context
     * @return null
     */
    protected function getOrCall($name, $level, $toLog, $context)
    {
        if (is_callable($this->{$name})) {
            try {
                return $this->{$name}($level, $toLog, $context);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $this->{$name};
    }
}
<?php

namespace EdwinLuijten\Houston\Payload\Partials;

class Level extends AbstractPayload
{

    private $level;
    private $value;

    private static $consts;

    public function __construct($level, $value)
    {
        $this->level = $level;
        $this->value = $value;
    }

    public static function init()
    {
        if (is_null(self::$consts)) {
            self::$consts = [
                "critical" => new Level("critical", 100000),
                "error"    => new Level("error", 10000),
                "warning"  => new Level("warning", 1000),
                "info"     => new Level("info", 100),
                "debug"    => new Level("debug", 10),
                "ignored"  => new Level("ignore", 0),
                "ignore"   => new Level("ignore", 0)
            ];
        }
    }

    public static function fromName($name)
    {
        self::init();

        $name = strtolower($name);

        return array_key_exists($name, self::$consts) ? self::$consts[$name] : null;
    }

    /**
     * @return integer
     */
    public function toInt()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->level;
    }

    /**
     * @param $name
     * @param $arguments
     * @return null
     */
    public function _callStatic($name, $arguments)
    {
        return self::fromName($name);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->level;
    }
}
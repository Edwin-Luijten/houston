<?php

namespace EdwinLuijten\Houston\Payload\Partials;

use EdwinLuijten\Houston\Helper;

class Notifier extends AbstractPayload
{
    const NAME = 'Houston';
    const VERSION = '0';

    private $name;
    private $version;

    public function __construct($name, $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    public static function defaultNotifier()
    {
        return new Notifier(self::NAME, self::VERSION);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function jsonSerialize()
    {
        return Helper::serialize(get_object_vars($this));
    }
}
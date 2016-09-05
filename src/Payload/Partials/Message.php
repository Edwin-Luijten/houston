<?php

namespace EdwinLuijten\Houston\Payload\Partials;

use EdwinLuijten\Houston\Helper;

class Message extends AbstractPayload
{
    private $body;

    private $extra;

    public function __construct($body, array $extra = [])
    {
        $this->body  = $body;
        $this->extra = $extra;
    }

    public function __set($key, $val)
    {
        $this->extra[$key] = $val;
    }

    public function __get($key)
    {
        return isset($this->extra[$key]) ? $this->extra[$key] : null;
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
        $payload = ['body' => $this->body];
        foreach ($this->extra as $key => $value) {
            $payload[$key] = $value;
        }

        return Helper::serialize($payload, null, array_keys($this->extra));
    }
}
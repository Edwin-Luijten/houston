<?php

namespace EdwinLuijten\Houston\Payload;

use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\Data;

class Payload implements \JsonSerializable
{

    /**
     * @var Data
     */
    private $data;

    /**
     * Payload constructor.
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(Data $data)
    {
        $this->data = $data;
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
        return Helper::serialize(get_object_vars($this));
    }
}
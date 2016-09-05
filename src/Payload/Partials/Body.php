<?php

namespace EdwinLuijten\Houston\Payload\Partials;

use EdwinLuijten\Houston\Helper;

class Body extends AbstractPayload
{
    /**
     * @var PayloadInterface
     */
    private $value;

    /**
     * Body constructor.
     * @param PayloadInterface $value
     */
    public function __construct(PayloadInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @return PayloadInterface
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param PayloadInterface $value
     */
    public function setValue(PayloadInterface $value)
    {
        $this->value = $value;
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
        $override = [
            'value' => $this->value->getKey(),
        ];

        return Helper::serialize(get_object_vars($this), $override);
    }
}
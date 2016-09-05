<?php

namespace EdwinLuijten\Houston\Payload\Partials;

use EdwinLuijten\Houston\Helper;

abstract class AbstractPayload implements PayloadInterface, \JsonSerializable
{
    /**
     * @return string
     */
    public function getKey()
    {
        return Helper::pascalToCamel(str_replace('EdwinLuijten\\Houston\\Payload\\Partials\\', '', self::class));
    }
}
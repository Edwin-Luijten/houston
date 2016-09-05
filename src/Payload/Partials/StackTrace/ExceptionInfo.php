<?php

namespace EdwinLuijten\Houston\Payload\Partials\StackTrace;

use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\AbstractPayload;

class ExceptionInfo extends AbstractPayload
{
    private $class;
    private $message;
    private $description;
    private $file;
    private $line;

    public function __construct($class, $message, $file, $line, $description = null)
    {
        $this->class       = $class;
        $this->message     = $message;
        $this->description = $description;
        $this->file        = $file;
        $this->line        = $line;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
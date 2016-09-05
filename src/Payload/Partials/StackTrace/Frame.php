<?php

namespace EdwinLuijten\Houston\Payload\Partials\StackTrace;

use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\AbstractPayload;

class Frame extends AbstractPayload
{
    private $filename;

    private $lineNumber;

    private $colNumber;

    private $method;

    private $code;

    private $context;

    private $arguments;

    private $kwArguments;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     * @return Frame
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @param mixed $lineNumber
     * @return Frame
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return mixed
     */
    public function getColNumber()
    {
        return $this->colNumber;
    }

    /**
     * @param mixed $colNumber
     * @return Frame
     */
    public function setColNumber($colNumber)
    {
        $this->colNumber = $colNumber;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return Frame
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return Frame
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     * @return Frame
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param mixed $arguments
     * @return Frame
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return mixed
     */
    public function getKwArguments()
    {
        return $this->kwArguments;
    }

    /**
     * @param mixed $kwArguments
     * @return Frame
     */
    public function setKwArguments($kwArguments)
    {
        $this->kwArguments = $kwArguments;
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
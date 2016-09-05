<?php

namespace EdwinLuijten\Houston\Payload\Partials\StackTrace;

use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\AbstractPayload;

class Trace extends AbstractPayload
{

    private $frames = [];

    private $exception;

    public function __construct($frames, ExceptionInfo $exception)
    {
        $this->setFrames($frames);
        $this->exception = $exception;
    }

    /**
     * @return mixed
     */
    public function getFrames()
    {
        return $this->frames;
    }

    /**
     * @param mixed $frames
     * @return Trace
     */
    public function setFrames($frames)
    {
        foreach ($frames as $frame) {
            if (!$frame instanceof Frame) {
                throw new \InvalidArgumentException('$frame must be an instance of ' . Frame::class);
            }
        }

        $this->frames = $frames;
    }

    /**
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param mixed $exception
     * @return Trace
     */
    public function setException($exception)
    {
        $this->exception = $exception;
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
<?php

namespace EdwinLuijten\Houston\Payload\Partials\StackTrace;

use EdwinLuijten\Houston\Payload\Partials\AbstractPayload;

class TraceChain extends AbstractPayload
{
    /**
     * @var array
     */
    private $traces;

    /**
     * TraceChain constructor.
     * @param array $traces
     */
    public function __construct(array $traces)
    {
        $this->setTraces($traces);
    }

    /**
     * @return array
     */
    public function getTraces()
    {
        return $this->traces;
    }

    /**
     * @param $traces
     */
    public function setTraces($traces)
    {
        if (count($traces) < 1) {
            throw new \InvalidArgumentException('$traces should have at least 1 Trace');
        }

        foreach ($traces as $trace) {
            if (!$trace instanceof Trace) {
                throw new \InvalidArgumentException('$trace must be instance of: ' . Trace::class);
            }
        }

        $this->traces = $traces;
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
        return array_map(function ($value) {
            if ($value instanceof \JsonSerializable) {
                return $value->jsonSerialize();
            }

            return $value;
        }, $this->traces);
    }
}
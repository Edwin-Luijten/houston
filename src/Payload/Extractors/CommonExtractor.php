<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

class CommonExtractor extends AbstractExtractor
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var mixed
     */
    private $fingerPrint;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * CommonExtractor constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->timestamp = time();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function extract($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
    }
}
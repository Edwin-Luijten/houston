<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

class CommonExtractor extends AbstractExtractor {

    /**
     * @var string
     */
    private $title;

    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function extract($value)
    {
        if (property_exists($this, $value)) {
            return $this->{$value};
        }
    }
}
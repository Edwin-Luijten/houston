<?php

namespace EdwinLuijten\Houston;

class Response
{
    private $status;

    private $info;

    private $uuid;

    public function __construct($status, $info, $uuid = null)
    {
        $this->status = $status;
        $this->info   = $info;
        $this->uuid   = $uuid;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function wasSuccessFull()
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function __toString()
    {
        return 'Status: ' . $this->status . PHP_EOL .
        'Body: ' . json_encode($this->info) . PHP_EOL;
    }
}
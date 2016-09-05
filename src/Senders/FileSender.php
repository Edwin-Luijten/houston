<?php

namespace EdwinLuijten\Houston\Senders;

use EdwinLuijten\Houston\Payload\Payload;

class FileSender implements SenderInterface
{
    private $logLocation = '/var/tmp';
    private $log;

    public function __construct($config)
    {
        if (array_key_exists('fileLogLocation', $config)) {
            $this->logLocation = $config['fileLogLocation'];
        }
    }

    public function send(Payload $payload)
    {
        if (empty($this->log)) {
            $this->loadFile();
        }

        fwrite($this->log, json_encode($payload->jsonSerialize()) . PHP_EOL);
    }

    private function loadFile()
    {
        $filename  = $this->logLocation . '/houston/' . microtime(true) . '.problem';
        $this->log = fopen($filename, 'a');
    }
}
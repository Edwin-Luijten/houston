<?php

namespace EdwinLuijten\Houston\Monolog\Handler;

use EdwinLuijten\Houston\HoustonNotifier;
use Exception;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HoustonHandler extends RotatingFileHandler
{
    /**
     * @var HoustonNotifier
     */
    protected $notifier;

    /**
     * Records whether any log records have been added since the last flush of the rollbar notifier
     *
     * @var bool
     */
    private $hasRecords = false;

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $record['formatted'] = $this->formatter->format($record);

        if (null === $this->mustRotate) {
            $this->mustRotate = !file_exists($this->url);
        }

        if ($this->nextRotation < $record['datetime']) {
            $this->mustRotate = true;
            $this->close();
        }

        parent::write($record);
    }
}
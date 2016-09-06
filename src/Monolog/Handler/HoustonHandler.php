<?php

namespace EdwinLuijten\Houston\Monolog\Handler;

use EdwinLuijten\Houston\HoustonNotifier;
use Exception;
use Monolog\Handler\AbstractSyslogHandler;
use Monolog\Logger;

class HoustonHandler extends AbstractSyslogHandler
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

    public function __construct(HoustonNotifier $notifier, $level = Logger::ERROR, $bubble = true)
    {
        $this->notifier = $notifier;

        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof Exception) {
            $context = $record['context'];
            $exception = $context['exception'];
            unset($context['exception']);

            $this->notifier->notify($this->level, $exception, $context);
        }

        $this->hasRecords = true;
    }
}
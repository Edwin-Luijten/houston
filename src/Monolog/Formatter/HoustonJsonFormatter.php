<?php

namespace EdwinLuijten\Houston\Monolog\Formatter;

use Monolog\Formatter\FormatterInterface;

class HoustonJsonFormatter implements FormatterInterface {
    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        // TODO: Implement format() method.
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        // TODO: Implement formatBatch() method.
    }
}
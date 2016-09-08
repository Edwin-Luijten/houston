<?php

namespace EdwinLuijten\Houston;

class Error extends \Exception
{
    /**
     * @var integer
     */
    public $errorLevel;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var string
     */
    public $file;

    /**
     * @var integer
     */
    public $line;

    /**
     * @var array
     */
    public $backtrace;

    /**
     * @var array
     */
    private static $consts;

    /**
     * Error constructor.
     * @param string $message
     * @param int $level
     * @param \Exception $file
     * @param $line
     * @param $backtrace
     */
    public function __construct($message, $level, $file, $line, $backtrace)
    {
        parent::__construct($message, $level);

        $this->errorLevel   = $level;
        $this->errorMessage = $message;
        $this->file         = $file;
        $this->line         = $line;
        $this->backtrace    = $backtrace;
    }

    /**
     * @return array
     */
    public function getBacktrace()
    {
        return $this->backtrace;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return self::getConst($this->errorLevel) . ": " . $this->errorMessage;
    }

    /**
     * @param $const
     * @return mixed|null
     */
    private function getConst($const)
    {
        if (is_null(self::$consts)) {
            self::$consts = [
                E_ERROR             => "E_ERROR",
                E_WARNING           => "E_WARNING",
                E_PARSE             => "E_PARSE",
                E_NOTICE            => "E_NOTICE",
                E_CORE_ERROR        => "E_CORE_ERROR",
                E_CORE_WARNING      => "E_CORE_WARNING",
                E_COMPILE_ERROR     => "E_COMPILE_ERROR",
                E_COMPILE_WARNING   => "E_COMPILE_WARNING",
                E_USER_ERROR        => "E_USER_ERROR",
                E_USER_WARNING      => "E_USER_WARNING",
                E_USER_NOTICE       => "E_USER_NOTICE",
                E_STRICT            => "E_STRICT",
                E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
                E_DEPRECATED        => "E_DEPRECATED",
                E_USER_DEPRECATED   => "E_USER_DEPRECATED"
            ];
        }

        return isset(self::$consts[$const]) ? self::$consts[$const] : null;
    }
}
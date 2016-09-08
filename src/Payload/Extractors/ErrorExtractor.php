<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

use EdwinLuijten\Houston\Error;
use EdwinLuijten\Houston\Payload\Partials\Level;

class ErrorExtractor extends AbstractExtractor
{

    private $context;

    private $errorLevels;

    private $exceptionLevel;

    private $psrLevels;

    private $messageLevel;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->setMessageLevel($config);
        $this->setExceptionLevel($config);
        $this->setPsrLevels($config);
        $this->setErrorLevels($config);
    }

    public function extract($key, $level = null, $toLog = null)
    {
        if ($key === 'level' && !empty($level) && !empty($toLog)) {
            return $this->getLevel($level, $toLog);
        }
    }

    /**
     * @param mixed $level
     * @param $toLog
     * @return
     */
    private function getLevel($level, $toLog)
    {
        if (is_null($level)) {
            if ($toLog instanceof Error) {
                $level = $this->get($this->errorLevels, $toLog->errorLevel);
            } elseif ($toLog instanceof \Exception) {
                $level = $this->exceptionLevel;
            } else {
                $level = $this->messageLevel;
            }
        }

        return Level::fromName($this->get($this->psrLevels, strtolower($level)));
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $this->get($context, 'context');
    }

    /**
     * @param mixed $errorLevels
     */
    public function setErrorLevels($errorLevels)
    {
        $this->errorLevels = self::$defaults->errorLevels($this->get($errorLevels, 'errorLevels'));
    }

    /**
     * @param $messageLevel
     */
    public function setMessageLevel($messageLevel)
    {
        $this->messageLevel = self::$defaults->messageLevel($this->get($messageLevel, 'messageLevel'));
    }

    /**
     * @param $exceptionLevel
     */
    public function setExceptionLevel($exceptionLevel)
    {
        $this->exceptionLevel = self::$defaults->exceptionLevel($this->get($exceptionLevel, 'exceptionLevel'));
    }

    /**
     * @param $psrLevels
     */
    public function setPsrLevels($psrLevels)
    {
        $this->psrLevels = self::$defaults->psrLevels($this->get($psrLevels, 'psrLevels'));
    }
}
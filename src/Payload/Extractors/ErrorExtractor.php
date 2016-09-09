<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

use EdwinLuijten\Houston\Error;
use EdwinLuijten\Houston\Payload\Partials\Level;
use EdwinLuijten\Houston\Payload\Partials\Message;

class ErrorExtractor extends AbstractExtractor
{

    private $context;

    private $errorLevels;

    private $exceptionLevel;

    private $psrLevels;

    private $messageLevel;

    private $baseException;

    private $willIncludeContext = true;

    private $willShiftFunction;

    private $toLog;

    private $level;

    /**
     * @var string
     */
    private $fingerPrint;

    public function __construct($config, $level, $toLog, $context)
    {
        parent::__construct($config);

        $this->toLog = $toLog;
        $this->level = $level;

        $this->setMessageLevel($config);
        $this->setExceptionLevel($config);
        $this->setPsrLevels($config);
        $this->setErrorLevels($config);
        $this->setContext($context);
        $this->setBaseException($config);
        $this->setWillIncludeCodeContext($config);

        $this->willShiftFunction = $this->get($config, 'shift_function');

        if (!isset($this->willShiftFunction)) {
            $this->willShiftFunction = true;
        }
    }

    public function extract($key)
    {
        if ($key === 'level') {
            return $this->getLevel();
        }

        if ($key === 'fingerprint') {
            return $this->getFingerprint();
        }

        if ($key === 'message') {
            return $this->getMessage();
        }

        if (property_exists($this, $key)) {
            return $this->{$key};
        }
    }

    /**
     * @return Level
     */
    private function getLevel()
    {
        if (is_null($this->level)) {
            if ($this->toLog instanceof Error) {
                $level = $this->get($this->errorLevels, $this->toLog->errorLevel);
            } elseif ($this->toLog instanceof \Exception) {
                $level = $this->exceptionLevel;
            } else {
                $level = $this->messageLevel;
            }
        }

        return Level::fromName($this->get($this->psrLevels, strtolower($level)));
    }

    /**
     * @return string
     */
    private function getFingerprint() {
        return md5((string)$this->toLog);
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        return new Message((string)$this->toLog, $this->context);
    }

    /**
     * @param mixed $context
     */
    private function setContext($context)
    {
        $this->context = $this->get($context, 'context');
    }

    /**
     * @param mixed $errorLevels
     */
    private function setErrorLevels($errorLevels)
    {
        $this->errorLevels = self::$defaults->errorLevels($this->get($errorLevels, 'errorLevels'));
    }

    /**
     * @param $messageLevel
     */
    private function setMessageLevel($messageLevel)
    {
        $this->messageLevel = self::$defaults->messageLevel($this->get($messageLevel, 'messageLevel'));
    }

    /**
     * @param $exceptionLevel
     */
    private function setExceptionLevel($exceptionLevel)
    {
        $this->exceptionLevel = self::$defaults->exceptionLevel($this->get($exceptionLevel, 'exceptionLevel'));
    }

    /**
     * @param $psrLevels
     */
    private function setPsrLevels($psrLevels)
    {
        $this->psrLevels = self::$defaults->psrLevels($this->get($psrLevels, 'psrLevels'));
    }

    /**
     * @param mixed $baseException
     */
    private function setBaseException($baseException)
    {
        $this->baseException = self::$defaults->baseException($this->get($baseException, 'baseException'));
    }

    /**
     * @param mixed $includeCodeContext
     */
    private function setWillIncludeCodeContext($includeCodeContext)
    {
        $codeContext = $this->get($includeCodeContext, 'include_error_code_context');

        if (!is_null($codeContext)) {
            $this->willIncludeContext = $codeContext;
        }
    }
}
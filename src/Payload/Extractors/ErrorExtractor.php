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

    private $baseException;

    private $willIncludeContext = true;

    /**
     * @var string
     */
    private $fingerPrint;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->setMessageLevel($config);
        $this->setExceptionLevel($config);
        $this->setPsrLevels($config);
        $this->setErrorLevels($config);
        $this->setContext($config);
        $this->setBaseException($config);
        $this->setWillIncludeCodeContext($config);

        $this->willShiftFunction = $this->get($config, 'shift_function');

        if (!isset($this->willShiftFunction)) {
            $this->willShiftFunction = true;
        }
    }

    public function extract($key, $level = null, $toLog = null)
    {
        if ($key === 'level') {
            return $this->getLevel($level, $toLog);
        }

        if ($key === 'fingerprint') {
            return $this->getFingerprint($toLog);
        }

        if ($key === 'message') {
            return $this->getMessage($toLog);
        }

        if (property_exists($this, $key)) {
            return $this->{$key};
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
     * @param $toLog
     * @return string
     */
    private function getFingerprint($toLog) {
        return md5((string)$toLog);
    }

    /**
     * @param $toLog
     * @param $context
     * @return Message
     */
    private function getMessage($toLog, $context)
    {
        return new Message((string)$toLog, $this->context);
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
     * @return Builder
     */
    private function setBaseException($baseException)
    {
        $this->baseException = self::$defaults->baseException($this->get($baseException, 'baseException'));
    }

    /**
     * @param mixed $includeCodeContext
     * @return Builder
     */
    private function setWillIncludeCodeContext($includeCodeContext)
    {
        $codeContext = $this->get($includeCodeContext, 'include_error_code_context');

        if (!is_null($codeContext)) {
            $this->willIncludeContext = $codeContext;
        }
    }
}
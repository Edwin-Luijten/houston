<?php

namespace EdwinLuijten\Houston\Payload;

use EdwinLuijten\Houston\Error;
use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Partials\Body;
use EdwinLuijten\Houston\Payload\Partials\Context;
use EdwinLuijten\Houston\Payload\Partials\Data;
use EdwinLuijten\Houston\Payload\Partials\StackTrace\ExceptionInfo;
use EdwinLuijten\Houston\Payload\Partials\StackTrace\Frame;
use EdwinLuijten\Houston\Payload\Partials\StackTrace\Trace;
use EdwinLuijten\Houston\Payload\Partials\StackTrace\TraceChain;

class Builder implements BuilderInterface
{
    /**
     * @var Extractor
     */
    private $extractor;

    /**
     * Builder constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->extractor = new Extractor($config);
    }

    /**
     * @param $level
     * @param $toLog
     * @param $context
     * @return Data
     */
    public function construct($level, $toLog, $context)
    {
        $environment = $this->extractor->getEnvironment();

        $body = $this->getBody($toLog, $context);
        $data = new Data($environment, $body);

        $data->setFramework($this->extractor->getFramework());
        $data->setLevel($this->extractor->getLevel($level, $toLog));
        $data->setTimestamp($this->extractor->getTimestamp());
        $data->setCodeVersion($this->extractor->getCodeVersion());
        $data->setContext($this->extractor->getContext());
        $data->setPlatform($this->extractor->getPlatform());
        $data->setLanguage($this->extractor->getLanguage());
        $data->setContext($this->extractor->getContext());
        $data->setRequest($this->extractor->getRequest());
        $data->setServer($this->extractor->getServer());
        $data->setCustom($this->extractor->getCustom());
        $data->setFingerprint($this->extractor->getFingerprint());
        $data->setTitle($this->extractor->getTitle());
        $data->setNotifier($this->extractor->getNotifier());
        $data->setUuid(Helper::uuid4());

        return $data;
    }

    protected function getBody($toLog, $context)
    {
        $baseException = $this->extractor->getBaseException();

        if ($toLog instanceof Error) {
            $content = $this->getErrorTrace($toLog);
        } elseif ($toLog instanceof $baseException) {
            $content = $this->getExceptionTrace($toLog);
        } else {
            $scrubFields = $this->extractor->getScrubFields();
            $content     = $this->extractor->getMessage($toLog, $this->extractor->scrub($context, $scrubFields));
        }

        return new Body($content);
    }

    protected function getErrorTrace(Error $error)
    {
        return $this->makeTrace($error, $error->getClassName());
    }

    protected function getExceptionTrace($exception)
    {
        $chain[] = $this->makeTrace($exception);

        $previous = $exception->getPrevious();

        $baseException = $this->extractor->getBaseException();

        while ($previous instanceof $baseException) {
            $chain[]  = $this->makeTrace($previous);
            $previous = $exception->getPrevious();
        }

        if (count($chain) > 1) {
            return new TraceChain($chain);
        }

        return new Trace($chain[0]->getFrames(), $chain[0]->getException());
    }

    protected function makeTrace($exception, $override = null)
    {
        $frames = $this->makeFrames($exception);

        $exceptionInfo = new ExceptionInfo(
            Helper::coalesce($override, get_class($exception)),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        return new Trace($frames, $exceptionInfo);
    }

    /**
     * @param $exception
     * @return array
     */
    protected function makeFrames($exception)
    {
        $frames = [];

        $frame = new Frame($exception->getFile());
        $frame->setLineNumber($exception->getLine());
        $this->addContextToFrame($frame, $exception->getFile(), $exception->getLine());

        $frames[] = $frame;
        $traces = $this->getTrace($exception);

        foreach ($traces as $trace) {
            $filename   = Helper::coalesce(Helper::get($trace, 'file'), '<internal>');
            $lineNumber = Helper::coalesce(Helper::get($trace, 'line'), 0);
            $method     = $trace['function'];

            $frame = new Frame($filename);
            $frame->setLineNumber($lineNumber);
            $frame->setMethod($method);

            if ($this->extractor->willIncludeContext()) {
                $this->addContextToFrame($frame, $filename, $lineNumber);
            }

            $frames[] = $frame;
        }

        array_reverse($frames);

        $nFrames = count($frames);

        if ($this->extractor->willShiftFunction() && $nFrames > 0) {
            for ($i = $nFrames - 1; $i > 0; $i--) {
                $frames[$i]->setMethod($frames[$i - 1]->getMethod());
            }
            $frames[0]->setMethod('<main>');
        }

        return $frames;
    }

    private function addContextToFrame(Frame $frame, $filename, $line)
    {
        if (!file_exists($filename)) {
            return;
        }

        $source = file($filename);

        if (!is_array($source)) {
            return;
        }

        $total = count($source);
        $line  = $line - 1;

        $frame->setCode($source[$line]);

        $offset = 6;
        $min    = max($line - $offset, 0);
        $pre    = null;
        $post   = null;

        if ($min !== $line) {
            $pre = array_slice($source, $min, $line - $min);
        }

        $max = min($line + $offset, $total);

        if ($max !== $line) {
            $post = array_slice($source, $line + 1, $max - $line);
        }

        $frame->setContext(new Context($pre, $post));
    }

    private function getTrace($exception)
    {
        if ($exception instanceof Error) {
            return $exception->getBacktrace();
        } else {
            return $exception->getTrace();
        }
    }

}
<?php

namespace EdwinLuijten\Houston\Payload;

use EdwinLuijten\Houston\Error;
use EdwinLuijten\Houston\Helper;
use EdwinLuijten\Houston\Payload\Extractors\EnvironmentExtractor;
use EdwinLuijten\Houston\Payload\Extractors\ErrorExtractor;
use EdwinLuijten\Houston\Payload\Extractors\RequestExtractor;
use EdwinLuijten\Houston\Payload\Extractors\ServerExtractor;
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
     * @var RequestExtractor
     */
    private $requestExtractor;

    /**
     * @var ServerExtractor
     */
    private $serverExtractor;

    /**
     * @var EnvironmentExtractor
     */
    private $environmentExtractor;

    /**
     * @var ErrorExtractor
     */
    private $errorExtractor;

    /**
     * Builder constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->requestExtractor     = new RequestExtractor($config);
        $this->serverExtractor      = new ServerExtractor($config);
        $this->environmentExtractor = new EnvironmentExtractor($config);
        $this->errorExtractor       = new ErrorExtractor($config);
    }

    /**
     * @param $level
     * @param $toLog
     * @param $context
     * @return Data
     */
    public function construct($level, $toLog, $context)
    {
        $environment = $this->environmentExtractor->extract('environment');

        $body = $this->getBody($toLog, $context);
        $data = new Data($environment, $body);

        $data->setPlatform($this->environmentExtractor->extract('platform'));
        $data->setLanguage($this->environmentExtractor->extract('language'));
        $data->setFramework($this->environmentExtractor->extract('framework'));
        $data->setCodeVersion($this->environmentExtractor->extract('codeVersion'));

        $data->setRequest($this->requestExtractor->extract());
        $data->setServer($this->serverExtractor->extract());

        $data->setTitle($this->extractor->getTitle());
        $data->setUuid(Helper::uuid4());
        $data->setLevel($this->extractor->getLevel($level, $toLog));


        $data->setContext($this->extractor->getContext());
        $data->setCustom($this->extractor->getCustom());

        $data->setNotifier($this->extractor->getNotifier());
        $data->setFingerprint($this->extractor->getFingerprint());
        $data->setTimestamp($this->extractor->getTimestamp());

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
        $traces   = $this->getTrace($exception);

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
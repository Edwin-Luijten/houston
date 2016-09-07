<?php

namespace EdwinLuijten\Houston;

use EdwinLuijten\Houston\Monolog\Formatter\HoustonJsonFormatter;
use EdwinLuijten\Houston\Payload\Payload;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HoustonNotifier
{
    /**
     * @var Config
     */
    private $config;

    /**
     * HoustonNotifier constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    public function configure(array $config)
    {
        $this->config->configure($config);
    }

    public function scope(array $config)
    {
        return new HoustonNotifier($this->extend($config));
    }

    public function extend(array $config)
    {
        return $this->config->extend($config);
    }

    public function notify($level, $toLog, array $context = [])
    {
        $config = $this->config->getConfig();
        $payload = $this->getPayload($level, $toLog, $context);
        $level   = $payload->getData()->getLevel()->toInt();

        $logger = new Logger('houston');

        if($config['handlerOptions']['disableDefaultHandler'] === false) {
            $handler = new RotatingFileHandler($config['handlerOptions']['fileLogLocation']);
            $handler->setFormatter(new HoustonJsonFormatter());

            $logger->pushHandler($handler);
        }

        foreach ($this->config->getHandlers() as $handler) {
            // Set the formatter on every handler
            $handler->setFormatter(new HoustonJsonFormatter());

            $logger->pushHandler($handler);
        }


        $logger->addRecord($level, '', $payload->jsonSerialize());
    }

    /**
     * @param $level
     * @param $toLog
     * @param $context
     * @return Payload
     */
    public function getPayload($level, $toLog, $context)
    {
        return $this->config->transform(new Payload(
            $this->config->getData($level, $toLog, $context)
        ), $level, $toLog, $context);
    }

    public function sendOrIgnore($payload, $toLog)
    {
//        if ($this->config->checkIgnored($payload, $toLog)) {
//            return new Response(0, 'Ignored');
//        }

        return $this->config->send($payload);
    }

    private function handleResponse($payload, $response)
    {
        $this->config->handleResponse($payload, $response);
    }
}
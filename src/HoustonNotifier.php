<?php

namespace EdwinLuijten\Houston;

use EdwinLuijten\Houston\Monolog\Formatter\HoustonJsonFormatter;
use EdwinLuijten\Houston\Payload\Payload;
use Monolog\Handler\RotatingFileHandler;

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
        $payload = $this->getPayload($level, $toLog, $context);
        $level   = $payload->getData()->getLevel()->toInt();
        $handler = new RotatingFileHandler($this->config->getConfig()['file_log_location'], 0, $level);
        $handler->setFormatter(new HoustonJsonFormatter());

//        $logger = new Logger('houston');
//        $logger->pushHandler($handler);

        $data['level']   = $level;
        $data['payload'] = $payload->jsonSerialize();
        $data['datetime'] = (new \DateTime())->format('Y-m-d H:i:s');
        $handler->handle($data);
        //$logger->log($level, $payload->getData()->getUuid(), $payload->jsonSerialize());
    }

    /**
     * @param $level
     * @param $toLog
     * @param $context
     * @return Payload
     */
    public function getPayload($level, $toLog, $context)
    {
        //var_dump($this->config->getData($level, $toLog, $context)); exit;
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
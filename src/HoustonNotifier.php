<?php

namespace EdwinLuijten\Houston;

use EdwinLuijten\Houston\Payload\Payload;

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
        $payload  = $this->getPayload($level, $toLog, $context);
        $response = $this->sendOrIgnore($payload, $toLog);

        $this->handleResponse(
            $payload,
            $response
        );

        return $response;
    }

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
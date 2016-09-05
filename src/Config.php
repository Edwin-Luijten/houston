<?php

namespace EdwinLuijten\Houston;

use EdwinLuijten\Houston\Payload\Builder;
use EdwinLuijten\Houston\Payload\BuilderInterface;
use EdwinLuijten\Houston\Senders\FileSender;
use EdwinLuijten\Houston\Senders\SenderInterface;

class Config
{
    /**
     * @var Builder
     */
    private $builder;

    private $config;

    private $transformer;

    private $filter;

    private $responseHandler;

    private $errorSampleRates;

    public function __construct(array $config)
    {
        $this->updateConfig($config);

        $levels = [
            E_WARNING,
            E_NOTICE,
            E_USER_ERROR,
            E_USER_WARNING,
            E_USER_NOTICE,
            E_STRICT,
            E_RECOVERABLE_ERROR
        ];

        if (defined('E_DEPRECATED')) {
            $levels = array_merge($levels, [E_DEPRECATED, E_USER_DEPRECATED]);
        }

        for ($i = 0, $n = count($levels); $i < $n; $i++) {
            $level = $levels[$i];
            if (!isset($this->errorSampleRates[$level])) {
                $this->errorSampleRates[$level] = 1;
            }
        }
    }

    public function configure($config)
    {
        $this->updateConfig($config);
    }

    public function extend($config)
    {
        return array_replace_recursive([], $this->config, $config);
    }

    public function getConfig()
    {
        return $this->config;
    }

    private function updateConfig($config)
    {
        $this->config = $config;
        $this->setBuilder($config);
        $this->setTransformer($config);
        $this->setSender($config);
        $this->setResponseHandler($config);
    }

    private function setBuilder($config)
    {
        $this->setup($config, 'builder', BuilderInterface::class, Builder::class, true);
    }

    private function setTransformer($config)
    {
        //$this->setup($config, 'transformer', )
    }

    private function setSender($config)
    {
        $default = FileSender::class;
        if (array_key_exists('handler', $config) && $config['handler'] === 'file') {

            if (array_key_exists('file_log_location', $config)) {
                $config['senderOptions'] = [
                    'fileLogLocation' => $config['file_log_location'],
                ];
            }
        }

        $this->setup($config, 'sender', SenderInterface::class, $default);
    }

    private function setResponseHandler($config)
    {

    }

    private function setup($config, $key, $type, $defaultClass = null, $useWholeConfig = false)
    {
        $$key = isset($config[$key]) ? $config[$key] : null;

        if (is_null($defaultClass) && is_null($$key)) {
            return;
        }

        if (is_null($$key)) {
            $$key = $defaultClass;
        }

        if (is_string($$key)) {
            if ($useWholeConfig) {
                $options = $config;
            } else {
                $options = isset($config[$key . 'Options']) ? $config[$key . 'Options'] : [];
            }

            $this->{$key} = new $$key($options);
        } else {
            $this->{$key} = $$key;
        }

        if (!$this->{$key} instanceof $type) {
            throw new \InvalidArgumentException($key . ' must be of type: ' . $type);
        }
    }

    public function getData($level, $toLog, $context)
    {
        return $this->builder->construct($level, $toLog, $context);
    }

    public function transform($payload, $level, $toLog, $context)
    {
        if (is_null($this->transformer)) {
            return $payload;
        }

        return $this->transformer->transform($payload, $level, $toLog, $context);
    }

    public function send($payload)
    {
        return $this->sender->send($payload);
    }

    public function handleResponse($payload, $response)
    {
        if (!is_null($this->responseHandler)) {
            $this->responseHandler->handle($payload, $response);
        }
    }
}
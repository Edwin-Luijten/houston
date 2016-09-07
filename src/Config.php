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

    private $handlers = [];

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
        $this->setHandlers($config);
    }

    private function setBuilder($config)
    {
        $this->setup($config, 'builder', BuilderInterface::class, Builder::class, true);
    }

    private function setHandlers($config)
    {
        $this->config['handlerOptions'] = [
            'fileLogLocation' => '/var/log/houston.problem',
            'disableDefaultHandler' => false,
        ];

        if (array_key_exists('file_log_location', $config)) {
            $this->config['handlerOptions']['fileLogLocation'] = $config['file_log_location'];
        }

        if (array_key_exists('disable_default_handler', $config)) {
            $this->config['handlerOptions']['disableDefaultHandler'] = $config['disable_default_handler'];
        }

        if (array_key_exists('handlers', $config)) {
            $this->senders = $config['handlers'];
        }

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

    public function getHandlers() {
        return $this->handlers;
    }
}
<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

class EnvironmentExtractor extends AbstractExtractor
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $framework;

    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $language;

    /**
     * Environment constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->setEnvironment($config);
        $this->setPlatform($config);
        $this->setFramework($config);
        $this->setLanguage();
    }

    public function extract($value)
    {
        if (property_exists($this, $value)) {
            return $this->{$value};
        }
    }

    /**
     * @param mixed $environment
     */
    private function setEnvironment($environment)
    {
        $this->environment = $this->get($environment, 'environment');
    }

    /**
     * @param mixed $platform
     */
    private function setPlatform($platform)
    {
        $this->platform = self::$defaults->platform($this->get($platform, 'platform'));
    }

    /**
     * @param mixed $framework
     */
    private function setFramework($framework)
    {
        $this->framework = $this->get($framework, 'framework');
    }

    private function setLanguage()
    {
        $this->language = 'PHP ' . phpversion();
    }
}
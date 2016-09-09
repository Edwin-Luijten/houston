<?php

namespace EdwinLuijten\Houston\Payload\Extractors;

use EdwinLuijten\Houston\Payload\Partials\Notifier;

class CommonExtractor extends AbstractExtractor
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * CommonExtractor constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->timestamp = time();
        $this->setNotifier($config);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function extract($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
    }

    /**
     * @param mixed $notifier
     * @return Builder
     */
    public function setNotifier($notifier)
    {
        $this->notifier = self::$defaults->notifier($this->get($notifier, 'notifier'));
    }
}